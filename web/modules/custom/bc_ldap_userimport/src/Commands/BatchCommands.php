<?php
namespace Drupal\bc_ldap_userimport\Commands;

use Drush\Commands\DrushCommands;
use Drupal\user\Entity\User;

Class BatchCommands extends DrushCommands
{

  /**
   * ldap check
   *
   * @command bc:ldapcheck
   * @aliases bclchk
   * @options $options arr AN option that takes multiple values.
   */
  public function ldapcheck($options=array())
  {
      $config = (object) \Drupal::config('bc_ldap_userimport.settings')->get();
      if ($config->enabled) {
        print_r($config);

        $ldapconn = ldap_connect($config->host) or die("Could not connect to LDAP server.");
        $ldapbind = ldap_bind($ldapconn, $config->rdn, $config->pass) or die("Could not bind to ldap");

        print_r( ldap_error($ldapconn) );


      }
  }

}
