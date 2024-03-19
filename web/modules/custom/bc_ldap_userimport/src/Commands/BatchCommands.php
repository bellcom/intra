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
	  header('Content-Type: text/html; charset=utf-8');
      $config = (object) \Drupal::config('bc_ldap_userimport.settings')->get();
      if ($config->enabled) {
//        print_r($config);

	$ldapconn = ldap_connect($config->host) or die("Could not connect to LDAP server.");
	ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

        $ldapbind = ldap_bind($ldapconn, $config->rdn, $config->pass) or die("Could not bind to ldap");

	$justthese = array('samaccountname', 'dn', 'name', 'mail', 'name', 'nickname', 'displayname', 'memberof', 'thumbnailphoto');
//	$justthese = array('samaccountname', 'dn', 'thumbnailphoto', 'mail', 'manager', 'name', 'nickname', 'mailnickname', 'memberof', 'displayname');
//	$justthese = array('samaccountname', 'dn', 'mail', 'manager', 'name', 'nickname', 'mailnickname', 'memberof', 'displayname');
//	$justthese = array('samaccountname', 'dn', 'mail', 'manager', 'name', 'mailnickname','displayname');

	// $justthese = array();
	$idx = 1;
	$counter = 0;
	$cookie = '';
	do {
		$result  = ldap_search(
			$ldapconn, 
			$config->dn, 
			$config->filter, 
			$justthese, 
			0, 
			0, 
			0, 
			LDAP_DEREF_NEVER,
			[['oid' => LDAP_CONTROL_PAGEDRESULTS, 'value' => ['size' => 250, 'cookie' => $cookie]]]
		);

		ldap_parse_result($ldapconn, $result, $errcode , $matcheddn , $errmsg , $referrals, $controls);

		$entries = ldap_get_entries($ldapconn, $result);
		echo count($entries) . "\n";
		$counter += count($entries);	

		if (isset($controls[LDAP_CONTROL_PAGEDRESULTS]['value']['cookie'])) {
        		// You need to pass the cookie from the last call to the next one
	        	$cookie = $controls[LDAP_CONTROL_PAGEDRESULTS]['value']['cookie'];
	    	} else {
    		    $cookie = '';
	    	}	

	} while (strlen($cookie) > 0);

	echo $counter . "\n";

	return;


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
				if ($key === 'thumbnailphoto') {
					$value = base64_encode($value[0]);
				} else {
					$value = utf8_encode($value[0]);
				}

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
			} else if ($key == 'memberof' && is_array($value)) {
				$groups = array();
				foreach ( $value AS $idx => $group ) {
					if ($idx == 'count') continue;
					$groups[] = utf8_encode($group);
				}
				$entry->memberof = $groups;
			}
		}

		if (!empty((array) $entry)) {
			$list[] = $entry;	
		}
	}

	$bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) );
	file_put_contents(__DIR__ . '/ldap_output.json' , $bom . json_encode( $list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );
	echo json_last_error_msg() . "\n";

      }
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

    $config = (object) \Drupal::config('bc_ldap_userimport.settings')->get();
    if ($config->enabled) {
      $ldapconn = ldap_connect($config->host) or die("Could not connect to LDAP server.");
      $ldapbind = ldap_bind($ldapconn, $config->rdn, $config->pass) or die("Could not bind to ldap");

      ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
      ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

      $result = ldap_search($ldapconn, $config->dn, $config->filter, $this->justthese, 0, -1);
      $entries = ldap_get_entries($ldapconn, $result);

echo count($entries) . "\n";


      $list = [];
      foreach ($entries as $ldap_entry) {

        if (!isset($ldap_entry['samaccountname'])) {
          continue;
        }
        if (!isset($ldap_entry['samaccountname'][0])) {
          continue;
        }

        $entry = new \stdClass();
        foreach ($ldap_entry as $key => $value) {


          if (isset($value['count']) && $value['count'] == 1) {

            $key = utf8_encode($key);
            if ($key === 'thumbnailphoto') {
              $value = base64_encode($value[0]);
            } else {
              $value = utf8_encode($value[0]);
            }

            if (!empty($key)) {
              $entry->{$key} = $value;
            }

          } else {
            if ($key == 'dn' && is_string($value)) {
              $value = utf8_encode($value);
              if (!empty($value)) {
                $entry->dn = $value;
              }
            } else {
              if ($key == 'memberof' && is_array($value)) {
                $groups = [];
                foreach ($value as $idx => $group) {
                  if ($idx == 'count') {
                    continue;
                  }
                  $groups[] = utf8_encode($group);
                }
                $entry->memberof = $groups;
              }
            }
          }

          if (!empty((array) $entry)) {
            $list[] = $entry;
          }

        }
      }

      $content = json_encode( $list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
      if (!json_last_error()) {
        $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) );
        file_put_contents($options['file'] , json_encode( $list, JSON_UNESCAPED_UNICODE) );

      } else {
        echo json_last_error_msg() . "\n";
      }
    }
  }

}
