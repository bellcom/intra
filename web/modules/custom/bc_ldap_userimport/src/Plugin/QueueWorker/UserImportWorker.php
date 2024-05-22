<?php

namespace Drupal\bc_ldap_userimport\Plugin\QueueWorker;

use Drupal\Core\Annotation\QueueWorker;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Custom Queue Worker.
 *
 * @QueueWorker(
 *   id = "ldap_user_import_queue",
 *   title = @Translation("Ldap user import Queue"),
 *   cron = {"time" = 300}
 * )
 */
final class UserImportWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Main constructor.
   *
   * @param array $configuration
   *   Configuration array.
   * @param mixed $plugin_id
   *   The plugin id.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Database\Connection $database
   *   The connection to the database.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, Connection $database) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->database = $database;
  }

  /**
   * Used to grab functionality from the container.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container.
   * @param array $configuration
   *   Configuration array.
   * @param mixed $plugin_id
   *   The plugin id.
   * @param mixed $plugin_definition
   *   The plugin definition.
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('database'),
    );
  }


  /**
   * Processes an item in the queue.
   *
   * @param mixed $data
   *   The queue item data.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \Exception
   */
  public function processItem($data) {

	if (is_array($data)) $data = (object) $data;

    // Processing of queue items logic goes here.
    $users = \Drupal::entityTypeManager()
      ->getStorage('user')
      ->loadByProperties([
        'mail' => $data->mail,
      ]);

    if ($users) {
      $user = reset($users);
      $user->set('name', $data->samaccountname);
      $user->set('display_name', utf8_encode($data->name));
      $user->set('field_displayname', utf8_encode($data->name));
      $user->set('field_email', $data->mail);

      if (!empty($data->thumbnailphoto)) {
        $image_data = base64_decode($data->thumbnailphoto);
        $file = \Drupal::service('file.repository')->writeData(
          $image_data,
          'public://userimages/' . $data->samaccountname . '.jpg',
          \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE
        );
        $file->save();

        $user->set('field_brugerbillede', array(
          'target_id' => $file->id(),
          'alt' => $data->samaccountname,
          'title' => $data->samaccountname
        ));
      }

      $user->save();

    } else {

      $user = User::create();
      $user->setUsername($data->samaccountname);
      $user->setEmail($data->mail);
      $user->set('display_name', utf8_encode($data->name));
      $user->set('field_displayname', utf8_encode($data->name));
      $user->set('field_email', $data->mail);
      $user->set('field_vis_telefon_i_email_signat', 'hide_both');

      if (!empty($data->thumbnailphoto)) {
        $image_data = base64_decode($data->thumbnailphoto);
        $file = \Drupal::service('file.repository')->writeData(
          $image_data,
          'public://userimages/' . $data->samaccountname . '.jpg',
          \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE
        );
        $file->save();

        $user->set('field_brugerbillede', array(
          'target_id' => $file->id(),
          'alt' => $data->samaccountname,
          'title' => $data->samaccountname
        ));
      }

      $user->activate();
      $user->enforceIsNew();
      $user->save();

    }
  }

}
