<?php
namespace Drupal\bc_speed_admin\Controller;

use Drupal\bc_speed_admin\Controller\Course;
use \Drupal\bc_speed_admin\Controller\Teacher;
use Drupal\Console\Bootstrap\Drupal;
use Drupal\Core\Controller\ControllerBase;
use \Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\filter\Entity\FilterFormat;
use Drupal\media\Entity\Media;
use Drupal\menu_link_content\Plugin\Menu\MenuLinkContent;
use Drupal\menu_link_content\Plugin\migrate\source\MenuLink;
use Drupal\menu_link_content\Entity\MenuLinkContent AS MLC;
use \Drupal\node\Entity\Node;
use \Drupal\file\Entity\File;
use Drupal\os2web_contact\Entity\Contact;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\path_alias\Entity\PathAlias;

Class Sync extends ControllerBase
{

    private static $db;

    public static function handler() {
        \Drupal::logger('speedadmin')->info("start speedadmin import ");

        self::$db = \Drupal::database();
        $teachers = Teacher::get();

        // TODO cleanup teachers that is not importet

        if ( $teachers && is_array($teachers)) {
            \Drupal::logger('speedadmin')->info("update " . count($teachers) . " theaters");
            $TopPersonPage = null;
            $find = self::$db->query("SELECT * FROM node__field_import_ref WHERE bundle = 'os2web_page' AND field_import_ref_value='-2';");
            $found = $find->fetchAll();
            if (count($found) == 1) {
              $TopPersonPage = Node::load(current($found)->entity_id);
            } else {
              \Drupal::logger('speedadmin')->info("no toppage for teachers found ( a page with -2 in import ref field) ");
            }

            foreach ( $teachers as $teacher ) {
                if ($teacher->Active) {
                    $find = self::$db->query("SELECT * FROM os2web_contact__field_import_ref WHERE field_import_ref_value='{$teacher->TeacherId}';");
                    $found = $find->fetchAll();
                    if (count($found) > 0) {
                        $first = current($found);
                        $Contact = Contact::load($first->entity_id);
                    } else {
                        $ContactInfo = array(
                            'name' => $teacher->Name . " " . $teacher->Surname
                        );
                        $Contact = Contact::create($ContactInfo);
                        $Contact->set('field_import_ref', $teacher->TeacherId);
                    }

                    if (!empty($teacher->Name)) $Contact->set('field_os2web_contact_firstname', $teacher->Name);
                    if (!empty($teacher->Surname)) $Contact->set('field_os2web_contact_surname', $teacher->Surname);
                    if (!empty($teacher->Email)) $Contact->set('field_os2web_contact_email', $teacher->Email);
                    if (!empty($teacher->Mobile)) $Contact->set('field_os2web_contact_mobile', $teacher->Mobile);
                    if (!empty($teacher->Phone)) $Contact->set('field_os2web_contact_phone', $teacher->Phone);
                    if (!empty($teacher->Address)) $Contact->set('field_adresse', $teacher->Address);
                    if (!empty($teacher->City)) $Contact->set('field_by', $teacher->City);
                    if (!empty($teacher->ZipCode)) $Contact->set('field_postnummer', $teacher->ZipCode);

                    if (!empty($teacher->AvailableClasses) && is_array($teacher->AvailableClasses)) {
                        $Contact->set('field_os2web_contact_job_task', implode(", ", $teacher->AvailableClasses));
                    }

                    if (!empty($teacher->Blob)) {
                        $image = Image::getRequest($teacher->Blob->BlobId, $teacher->Blob->MimeType);
                        if (!empty($image)) {
                            $image_object = \Drupal::service('file.repository')
                                ->writeData($image, 'public://public/' . $teacher->Blob->UniqueId . '.' . $teacher->Blob->Extention, FileSystemInterface::EXISTS_REPLACE);
                            if (is_object($image_object)) {
                                $Contact->set('field_image', array(
                                   'target_id' => $image_object->id(),
                                   'alt' => $teacher->Name . " " . $teacher->Surname,
                                   'title' => $teacher->Name . " " . $teacher->Surname
                                ));
                            }
                        }
                    }

                    if (!empty($teacher->Active)) {
                      $Contact->set('status', 1);
                    } else {
                      $Contact->set('status', 0);
                    }

                    $Contact->save();

                    if ($TopPersonPage) {
                      $url = $TopPersonPage->toUrl('canonical')->toString();
                      $namePath = str_replace(" ", "-", strtolower($Contact->getName()));
                      $alias = str_replace('/da', '', $url) . '/' . $namePath;

                      $path_alias_manager = \Drupal::entityTypeManager()->getStorage('path_alias');
                      $alias_objects = $path_alias_manager->loadByProperties([
                        'langcode' => 'da',
                        'path' => '/show-contact/' . $Contact->id()
                      ]);

                      if (empty($alias_objects)) {
                          $path_alias = PathAlias::create(array(
                            'langcode' => 'da',
                            'path' => '/show-contact/' . $Contact->id(),
                            'alias' => $alias
                          ));
                          $path_alias->save();

                      } else {
                          foreach( $alias_objects AS $alias_object ) {
                            $alias_object->alias = $alias;
                            $alias_object->save();
                          }
                      }

                    }
                } else {
                  $find = self::$db->query("SELECT * FROM os2web_contact__field_import_ref WHERE field_import_ref_value='{$teacher->TeacherId}';");
                  $found = $find->fetchAll();
                  if (count($found) > 0) {
                    $first = current($found);
                    $Contact = Contact::load($first->entity_id);
                    $Contact->status = 0;
                  }
                }
            }
        }

        $courses = Course::get();

        if (!empty($courses) && is_object($courses->tree) && !empty($courses->courses) && is_array($courses->courses)) {
          \Drupal::logger('speedadmin')->info("update " . count($courses->courses) . " courses");

          $tree = array();
          foreach( $courses->tree->Nodes as $course_node) {
            if ($course_node->TreeID == 1 && !empty($course_node->ChildNodes)) {
              $tree = self::children($course_node->ChildNodes, $courses->courses);
            }
          }

          if (count($tree)) {
            $find = self::$db->query("SELECT * FROM node__field_import_ref WHERE bundle = 'os2web_page' AND field_import_ref_value='-1';");
            $found = $find->fetchAll();
            if (count($found) == 1) {
              $TopPage = current($found)->entity_id;
              $node = Node::load($TopPage);
              if ( $node ) {

                $paragraph = null;
                $paragraphs = $node->get('field_os2web_page_paragraph_narr');
                foreach( $paragraphs->getValue() AS $paragraphids ) {
                  if (!empty($paragraphids['target_id'])) {
                    $_paragraph = \Drupal\paragraphs\Entity\Paragraph::load($paragraphids['target_id']);
                    if ($_paragraph->getType() == 'os2web_menu_links_paragraph') {
                      $paragraph = $_paragraph;
                      break;
                    }
                  }
                }

                if (!$paragraph) {
                  $paragraph = Paragraph::create(array(
                    'type' => 'os2web_menu_links_paragraph'
                  ));
                  $paragraph->set('field_os2web_menu_links_vm', 'image');
                  $paragraph->save();

                  $node->set('field_os2web_page_paragraph_narr', $paragraph);
                  $node->save();
                }

                foreach ( $tree as $obj ) {
                  self::createContent($TopPage, (object) $obj);
                }
              } else {
                \Drupal::logger('speedadmin')->info("no toppage or error toppages for courses found");
              }
            }
          }
        }
    }

    private static function children($ChildNodes, &$courses) {

      $tree = [];
      foreach ($ChildNodes as $CategoryId => $child) {
        $tree[$CategoryId]['title'] = $child->NodeName;
        $tree[$CategoryId]['treeid'] = $child->TreeID;
        $tree[$CategoryId]['Text'] = $child->NodeDescription;
        $tree[$CategoryId]['children'] = [];
        $tree[$CategoryId]['courses'] = [];
        if (!empty($child->Courses)) {
          foreach ($child->Courses as $course) {
            $tree[$CategoryId]['courses'][$course->CouseId]['title'] = $course->Course;
            foreach ($courses as $courseDef) {
              if ($course->CouseId == $courseDef->CouseId) {
                foreach( $courseDef AS $key => $value ) {
                  $tree[$CategoryId]['courses'][$course->CouseId][$key] = $value;
                }
              }
            }
          }
        }

        if (!empty($child->ChildNodes)) {
          $tree[$CategoryId]['children'] = self::children($child->ChildNodes, $courses);
        }
      }

      return $tree;
    }


    private static function createContent($parentId, $obj) {

      $addMenuLinker = false;
      if (!empty($obj->treeid)) {
        $import_ref = $obj->treeid;
        $addMenuLinker = true;
      } elseif (!empty($obj->CouseId)) {
        $import_ref = $obj->CouseId;
      }

      $find = self::$db->query("SELECT * FROM node__field_import_ref WHERE field_import_ref_value='{$import_ref}';");
      $found = $find->fetchAll();
      if (count($found) > 0) {
          $first = current($found);
          $node = Node::load($first->entity_id);
      } else {
          $node = Node::create(array(
            'title' => $obj->title,
            'type' => 'os2web_page',
          ));
          $node->set('field_import_ref', $import_ref);
      }

      if (!empty($obj->Text)) {
        $txt = trim($obj->Text);
        $txt = nl2br($txt);
        $txt = str_replace(array('&nbsp;', '&amp;'), '', $txt);
        $txt = strip_tags($txt);

        $node->set('field_os2web_page_intro', trim($txt));
      }

      $description = null;
      if (!empty($obj->Description)) {
        $description = trim($obj->Description);
        $description = nl2br($description);
        $description =  html_entity_decode($description);
        $description = strip_tags($description, '<br><a>');
      }

      if (false && !empty($obj->SubCategories)) {
        $html = '';
        foreach( $obj->SubCategories AS $idx => $cat ) {
            $html .= '<div class="course-text">' . $cat->Name . '</div><br>';
        }

        if (!empty($html)) {
          $description .= '<br><br>' . $html;
        }
      }

      if (!empty($obj->CouseId) && is_numeric($obj->CouseId)) {
        $description .= '<br><br><a class="course-assign" href="https://ring.speedadmin.dk/registration#/Course/' . $obj->CouseId . '" target="_blank">Tilmeld</a>';
      }

      if (!empty($description)) {
        $node->field_os2web_page_description->set(0, array(
          'value' => $description,
          'format' => 'wysiwyg_tekst'
        ));
      }

      if (!empty($obj->Blobs)) {
        $Blob = current($obj->Blobs);
        $image = Image::getRequest($Blob->BlobId, $Blob->MimeType);
        if (!empty($image)) {
          $image_object = \Drupal::service('file.repository')
            ->writeData($image, 'public://public/' . $Blob->UniqueId . '.' . $Blob->Extention, FileSystemInterface::EXISTS_REPLACE);
          if (is_object($image_object)) {
            $node->set('field_os2web_page_primaryimage', array(
              'target_id' => $image_object->id(),
              'alt' => $node->getTitle(),
              'title' => $node->getTitle()
            ));
          }
        }
      }

      if ($addMenuLinker) {
        $paragraph = false;
        $paragraphs = $node->get('field_os2web_page_paragraph_narr');
        foreach( $paragraphs->getValue() AS $paragraphids ) {
          if (!empty($paragraphids['target_id'])) {
            $_paragraph = \Drupal\paragraphs\Entity\Paragraph::load($paragraphids['target_id']);
            if ($_paragraph->getType() == 'os2web_menu_links_paragraph') {
              $_paragraph->set('field_os2web_menu_links_vm', 'image');
              $_paragraph->save();
              $paragraph = true;
              break;
            }
          }
        }

        if (!$paragraph) {
          $paragraph = Paragraph::create(array(
            'type' => 'os2web_menu_links_paragraph'
          ));
          $paragraph->set('field_os2web_menu_links_vm', 'image');
          $paragraph->save();
          $node->set('field_os2web_page_paragraph_narr', $paragraph);
        }
      }

      $node->save();

      $menu = \Drupal::menuTree()->load('main', new MenuTreeParameters('main'));
      // Find this node in menu
      $found = self::findInMenuLink($node->id(), $menu);
      // if node not in menu
      if (!$found) {
        // Find parrent node in menu
        $found = self::findInMenuLink($parentId, $menu);
        if ($found) {
            $MenuLink = MLC::create(array(
              'title' => $node->getTitle(),
              'link' => 'internal:/node/' . $node->id(),
              'menu_name' => 'main',
              'status' => true,
              'parent' => $found
            ));
            $MenuLink->save();
        }
      }

      if (!empty($obj->courses)) {
        foreach( $obj->courses AS $course ) {
          self::createContent($node->id(), (object) $course);
        }
      }

    }


    private static function findInMenuLink($nodeId, $menu) {
      $found = false;
      foreach( $menu AS $key => $link ) {
        if ($link->link->isEnabled()) {
          $linkNode = $link->link->getUrlObject()->getRouteParameters()['node'];
          if ($nodeId == $linkNode) $found = $link->link->getPluginId();
          if (!$found && $link->hasChildren) {
              $found = self::findInMenuLink($nodeId, $link->subtree);
          }
        }
      }

      return $found;
    }


    private static function createEvent() { }


}