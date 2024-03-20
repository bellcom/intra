<?php
namespace Drupal\bc_ldap_userimport\Controller;

Class LdapImport {

  private $json = NULL;

  public function getData() {

    $config = (object) \Drupal::config('bc_ldap_userimport.settings')->get();
    $results = array();
    if ($config->enabled) {

      $ldapconn = ldap_connect($config->host) or die("Could not connect to LDAP server.");
      ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
      ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

      $ldapbind = ldap_bind($ldapconn, $config->rdn, $config->pass) or die("Could not bind to ldap");

      $justthese = array('samaccountname', 'dn', 'name', 'mail', 'name', 'nickname', 'displayname', 'memberof', 'thumbnailphoto');
      //   $justthese = array('samaccountname', 'dn', 'thumbnailphoto', 'mail', 'manager', 'name', 'nickname', 'mailnickname', 'memberof', 'displayname');
      $idx = 1;
      $counter = 0;
      $cookie = '';
      do {
        $result = ldap_search($ldapconn, $config->dn, $config->filter, $justthese, 0, 0, 0, LDAP_DEREF_NEVER,
          [['oid' => LDAP_CONTROL_PAGEDRESULTS, 'value' => ['size' => 250, 'cookie' => $cookie]]]
        );

        ldap_parse_result($ldapconn, $result, $errcode, $matcheddn, $errmsg, $referrals, $controls);
        $entries = ldap_get_entries($ldapconn, $result);

        foreach ($entries as $entry) {
		if (is_array($entry) && !empty($entry)) {
		  	if (!empty($entry['mail'][0])) { $results[] = $entry; }
          	}
        }

	echo count($entries) . "\n";

        $counter += count($entries);
        if (isset($controls[LDAP_CONTROL_PAGEDRESULTS]['value']['cookie'])) {
		$cookie = $controls[LDAP_CONTROL_PAGEDRESULTS]['value']['cookie'];
        } else {
          $cookie = '';
        }

      } while (strlen($cookie) > 0);
    }

    return $results;

  }


  public function trimData($results=array()) {

    $list = [];
    foreach ($results as $idx => $result) {

      if (!isset($result['samaccountname'])) continue;
      if (!isset($result['samaccountname'][0])) continue;

      $entry = new \stdClass(); 
      foreach ($result as $key => $value) {

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
      }

      if (!empty((array) $entry)) {
          $list[] = $entry;
      }

      
    }

    return $list;
  }

}
