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
	  header('Content-Type: text/html; charset=utf-8');
      $config = (object) \Drupal::config('bc_ldap_userimport.settings')->get();
      if ($config->enabled) {
        print_r($config);

        $ldapconn = ldap_connect($config->host) or die("Could not connect to LDAP server.");
        $ldapbind = ldap_bind($ldapconn, $config->rdn, $config->pass) or die("Could not bind to ldap");

        print_r( ldap_error($ldapconn) ); echo "\n";

	ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    	ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

	$justthese = array('samaccountname', 'dn', 'name');
//	$justthese = array('samaccountname', 'dn', 'thumbnailphoto', 'mail', 'manager', 'name', 'nickname', 'mailnickname', 'memberof', 'displayname');
//	$justthese = array('samaccountname', 'dn', 'mail', 'manager', 'name', 'nickname', 'mailnickname', 'memberof', 'displayname');
//	$justthese = array('samaccountname', 'dn', 'mail', 'manager', 'name', 'mailnickname','displayname');

	// $justthese = array();

	$result  = ldap_search($ldapconn, $config->dn, $config->filter, $justthese);
	$entries = ldap_get_entries($ldapconn, $result);

	$list = array();
	$errorcount = 0;
	foreach ($entries as $ldap_entry) {

		if(!isset($ldap_entry['samaccountname']))
			continue;
      
		if(!isset($ldap_entry['samaccountname'][0]))
            		continue;

		$entry = new \stdClass();
		foreach ( $ldap_entry AS $key => $value ) {
			if (is_numeric($key)) continue;
			if ($key === 'count') continue;

			if (isset($value['count']) && $value['count'] == 1) {

				$key = utf8_encode($key);
				// echo $key . "\n";
				$value = $value[0];
				// echo $value . "\n";
				$value = utf8_encode($value);
				// echo "#\n";

				if (empty($key) || empty($value) ) {
					print_r( $value );
				} else {
					$entry->{$key} = $value;	
				}

			} else if ($key == 'dn' && is_string($value)) {
				$value = utf8_encode($value);
				if (!empty($value)) {
					$entry->dn = $value;
				}
			}
		}

		if (!empty((array) $entry)) {
//			print_r( $entry );
			$list[] = $entry;	
		}
	}
$bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) );
file_put_contents(__DIR__ . '/output.json' , $bom . json_encode( $list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );
// echo json_encode($list);
echo json_last_error_msg() . "\n";

//	echo count($entries) . "\n";
//	echo count( $list ) . "\n";

      }
  }

}
