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

        print_r( ldap_error($ldapconn) ); echo "\n";

	ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    	ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

	$justthese = array('samaccountname', 'dn');

	if (function_exists('ldap_control_paged_result')) {
	}
       	else echo "no ldap paged function\n";

	$result  = ldap_search($ldapconn, $config->dn, $config->filter, $justthese);
	$entries = ldap_get_entries($ldapconn, $result);
	
	foreach ($entries as $ldap_entry) {

		if(!isset($ldap_entry['samaccountname']))
			continue;
      
		if(!isset($ldap_entry['samaccountname'][0]))
            		continue;

		print_r( $ldap_entry );		
	}

	echo count($entries);

      }
  }

}
