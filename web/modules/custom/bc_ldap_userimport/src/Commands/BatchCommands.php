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

    $li = new \Drupal\bc_ldap_userimport\Controller\LdapImport();
    $data = $li->getData();
    $data = $li->trimData($data);

//    $config = (object) \Drupal::config('bc_ldap_userimport.settings')->get();
//    if ($config->enabled) {

      $content = json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
      if (!json_last_error()) {
        $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) );
        file_put_contents($options['file'] , $bom . json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );
      } else {
        echo json_last_error_msg() . "\n";
      }
//    }
  }


  /**
   * ldap import user json object from --file
   *
   * @description file export file
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

    print_r( $json );

  }

}
