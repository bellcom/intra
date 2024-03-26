<?php
namespace Drupal\bc_ldap_userimport\Commands;

use Drush\Commands\DrushCommands;
use Drupal\user\Entity\User;

Class BatchCommands extends DrushCommands
{

  private $justthese = array(
      'samaccountname',
      'dn',
      'name',
      'mail',
      'name',
      'nickname',
      'displayname',
      'memberof',
      'thumbnailphoto'
  );

  /**
   * ldap check
   *
   * @command bc:ldapcheck
   * @aliases bclchk
   * @options $options arr AN option that takes multiple values.
   */
  public function ldapcheck($options=array())
  {

  }


  /**
   * ldap export user json object to --file
   *
   * @description file export file
   * @command bc:ldapexport
   * @aliases bclexp
   * @options $options arr AN option that takes multiple values.
   * @option file filename on file to export to
   */
  public function ldapexport($options=array('file'=>null)) {

    if (empty($options['file'])) $options['file'] = sys_get_temp_dir() . '/ldap_user.json';

    if (!is_writeable(dirname($options['file']))) {
      echo $options['file'] . " is not writeable\n";
      return;
    }

    header('Content-Type: text/html; charset=utf-8');

    $li = new \Drupal\bc_ldap_userimport\Controller\LdapImport();
    $data = $li->getData();
    $data = $li->trimData($data);

    $content = json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    if (!json_last_error()) {
        // $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) );
	      file_put_contents($options['file'] ,  json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );
      } else {
        echo json_last_error_msg() . "\n";
      }
  }


  /**
   * ldap import user json object from --file
   *
   * @description file import file
   * @command bc:ldapimport
   * @aliases bclimp
   * @options $options arr AN option that takes multiple values.
   * @option file filename to import from
   */
  public function ldapimport($options=array('file' => '/tmp/ldap_user.json')) {

    if (!is_readable($options['file'])) {
      echo "no file " . $options['file'] . "\n";
      return;
    }

    $json = file_get_contents($options['file']);
    $json = json_decode($json);

    if (json_last_error()) {
      echo "json : " . json_last_error_msg() . "\n";
      return;
    }

    $queue = \Drupal::service('queue')->get('ldap_user_import_queue');
    foreach ( $json AS $idx => $user ) {

      if (!empty($user->samaccountname)) $user->samaccountname = strtolower(utf8_decode($user->samaccountname));
      if (!empty($user->mail)) $user->mail = strtolower(utf8_decode($user->mail));
      if (!empty($user->displayname)) $user->displayname = utf8_decode($user->displayname);
      if (!empty($user->name)) $user->name = utf8_decode($user->name);
      if (!empty($user->dn)) $user->dn = utf8_decode($user->dn);
      if (!empty($user->samaccountname)) $user->samaccountname = utf8_decode($user->samaccountname);
      if (!empty($user->memberof) && is_array($user->memberof)) {
        foreach ( $user->memberof AS &$member ) $member = utf8_decode($member);
      }

      if ( $idx < 2 ) {
        $queue->createItem($user);
      }

    }
  }


  /**
   * test
   *
   * @description cron test
   * @command bc:cron
   * @aliases bccron
   *
   */
  public function cron($options=array()) {

    $config = (object) \Drupal::config('bc_ldap_userimport.settings')->get();
    if ($config->enabled && $config->cron) {
      $run = true;
      $lastrun = $config->lastrun;
      if (!empty($lastrun)) {
        $compare = null;
        switch ($config->run) {
          case '': $run = false; echo "empty\n"; break;
          case 'day': $compare = strtotime("+1 day 00:00:00", $config->lastrun); break;
          case 'week': $compare = strtotime("+1 week 00:00:00", $config->lastrun); break;
          case 'month': $compare = strtotime("+1 month 00:00:00", $config->lastrun); break;
        }
        if ($compare > time()) {
          $run = false;
        }
      }

      if ($run) {
          $li = new \Drupal\bc_ldap_userimport\Controller\LdapImport();
          $data = $li->getData();
          $data = $li->trimData($data);
          $queue = \Drupal::service('queue')->get('ldap_user_import_queue');
          foreach ( $data AS $idx => $user ) {

            if (!empty($user->samaccountname)) $user->samaccountname = strtolower(utf8_decode($user->samaccountname));
            if (!empty($user->mail)) $user->mail = strtolower(utf8_decode($user->mail));
            if (!empty($user->displayname)) $user->displayname = utf8_decode($user->displayname);
            if (!empty($user->name)) $user->name = utf8_decode($user->name);
            if (!empty($user->dn)) $user->dn = utf8_decode($user->dn);
            if (!empty($user->samaccountname)) $user->samaccountname = utf8_decode($user->samaccountname);
            if (!empty($user->memberof) && is_array($user->memberof)) {
              foreach ( $user->memberof AS &$member ) $member = utf8_decode($member);
            }

            if ( $idx < 2 ) {
              $queue->createItem($user);
            }
          }

//        $saveConfig = \Drupal::configFactory()->getEditable('bc_ldap_userimport.settings');
//        $saveConfig->set('lastrun', time())->save();
      }
    }
  }


}
