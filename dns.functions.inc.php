<?php
/**
 * DNS Related Functionality
 * @author Joe Huss <detain@interserver.net>
 * @copyright 2017
 * @package MyAdmin
 * @category DNS
 */

use \MyDb\Mdb2\Db as db_mdb2;

include __DIR__ . '/pdns.functions.inc.php';

/**
 * max domains hosted on our dns server per client
 */
define('MAX_DNS_DOMAINS', 250);

/**
 * get_hostname()
 * ok this is a fucking awesome insanely fast way to lookup reverse dns settings for ips
 * basically what I did was i gave My and Nucleus   ACL (AXFR) permission on City,
 * so that instead of having to lookup ips one at a time they can load an entire 256
 * IPs at a time.   It caches all the IPs, and only does another query if it does not
 * already have the IP cached.   This allows us to do lookups 2600% Faster than most
 * any other way.    This was needed because as we're looking at reverse dns settings for
 * clients with multiple vlans and potentially tons of IPs, thats a TON of queries to
 * be making normally to get all the reverse dns settings for them from city, but this way
 * it will only be a couple queries no matter how many ips.   It also caches all results.
 *
 * @see API
 *
 * @param string $ip IP Address
 * @return string|false Hostname
 */
function get_hostname($ip) {
	global $cached_zones;
	$parts = explode('.', $ip);
	$zone = $parts[2] . '.' . $parts[1] . '.' . $parts[0] . '.in-addr.arpa';
	if (is_local_ip($ip) && !in_array($parts[0].'.'.$parts[1].'.'.$parts[2], array('173.225.102', '173.225.111'))) {
		if (!isset($cached_zones[$zone])) {
			require_once __DIR__.'/../../vendor/pear/net_dns2/Net/DNS2.php';
			$resolver = new Net_DNS2_Resolver(['nameservers' => ['66.45.228.79']]);
			//$resolver->nameservers = array('66.45.228.79');
			$tzone = [];
			try {
				$response = $resolver->query($zone, 'AXFR');
			} catch (Net_DNS2_Exception $e) {
				myadmin_log('dns', 'warning', "get_hostname({$ip}) -> Net_DNS_Resolver query failed: ".$e->getMessage(), __LINE__, __FILE__);
				$host = gethostbyaddr($ip);
				if ($host != $ip) {
					$cached_zones[$zone][$ip] = $host;
					unset($host);
					return $cached_zones[$zone][$ip];
				}
				return false;
			}
			//myadmin_log('dns', 'info', json_encode($response), __LINE__, __FILE__);
			if (count($response->answer)) {
				foreach ($response->answer as $rr)
					if ($rr->type == 'PTR')
						$tzone[implode('.', array_reverse(explode('.', str_replace('.in-addr.arpa', '', $rr->name))))] = $rr->ptrdname;
				$cached_zones[$zone] = $tzone;
				//myadmin_log('dns', 'debug', "City AXFR Loaded {$zone} with ".sizeof($tzone)." IPs", __LINE__, __FILE__);
			}
		}
		if (isset($cached_zones[$zone]) && isset($cached_zones[$zone][$ip]))
			return $cached_zones[$zone][$ip];
	} else {
		$host = gethostbyaddr($ip);
		if ($host != $ip) {
			$cached_zones[$zone][$ip] = $host;
			unset($host);
			return $cached_zones[$zone][$ip];
		}
	}
	return false;
}

/**
 * get_dns_domain()
 * Gets the DNS Entry for a given domain id.
 *
 * @see API
 * @param integer $domain_id The ID of the domain in question.
 * @param bool $bypass defaults to false, whether ot not to bypass domain ownership check
 * @param bool|string $acl optional name of acl to limitadmins by
 * @return array|false Either an array containing some information about the domain or false on failure.
 */
function get_dns_domain($domain_id, $bypass = false, $acl = false) {
	$domain_id = (int)$domain_id;
	$db = new db_mdb2(POWERDNS_DB, POWERDNS_USER, POWERDNS_PASSWORD, POWERDNS_HOST);
	$custid = $GLOBALS['tf']->session->account_id;
	function_requirements('has_acl');
	if ($bypass === true || ($GLOBALS['tf']->ima == 'admin' && ($acl == false || has_acl($acl))))
		$db->query("select * from domains where id='{$domain_id}'");
	else
		$db->query("select * from domains where id='{$domain_id}' and account='{$custid}'");
	if ($db->num_rows() > 0) {
		$db->next_record(MYSQL_ASSOC);
		return $db->Record;
	}
	return false;
}

/**
* To be used in combination with {@}get_dns_domain
* This gets all the records for a given domain.
*
* @see API
* @param int $domain_id The ID of the domain in question.
* @param bool $bypass
* @return array|false Either an array containing some information about the domain or false on failure.
*/
function get_dns_records($domain_id, $bypass = false) {
	$domain_id = (int)$domain_id;
	$db = new db_mdb2(POWERDNS_DB, POWERDNS_USER, POWERDNS_PASSWORD, POWERDNS_HOST);
	$custid = $GLOBALS['tf']->session->account_id;
	if ($GLOBALS['tf']->ima == 'admin' || $bypass == true)
		$db->query("select * from records where domain_id='{$domain_id}'");
	else
		$db->query("select records.* from records, domains where domains.id='{$domain_id}' and account='{$custid}' and domain_id=domains.id");
	$results = [];
	if ($db->num_rows() > 0)
		while ($db->next_record(MYSQL_ASSOC))
			$results[] = $db->Record;
	else
		return false;
	return $results;
}

/**
 * delete_dns_record()
 * deletes a single dns record from a domain
 *
 * @see API
 * @param integer $domain_id The ID of the domain in question.
 * @param integer $record_id The ID of the domains record to remove.
 * @return bool will return true if it succeeded, or false if there was some type of error.
 */
function delete_dns_record($domain_id, $record_id) {
	$domain_id = (int)$domain_id;
	$record_id = (int)$record_id;
	$db = new db_mdb2(POWERDNS_DB, POWERDNS_USER, POWERDNS_PASSWORD, POWERDNS_HOST);
	if (get_dns_domain($domain_id) !== false) {
		$db->query("delete from records where domain_id='{$domain_id}' and id='{$record_id}'");
		if ($db->affected_rows() == 1)
			return true;
	}
	return false;
}

/**
 * add_dns_record()
 * Adds a DNS Record for a given domain id.  It will take care of updating the SOA/Serial number
 * after the update so you dont need to worry about that.
 *
 * @see API
 * @param integer $domain_id The ID of the domain in question.
 * @param string $name the hostname being set on the dns record.
 * @param string $content the value of the dns record, or what its set to.
 * @param string $type  dns record type.
 * @param integer $ttl dns record time to live, or update time.
 * @param integer $prio dns record priority
 * @param bool $bypass defaults to false, whether ot not to bypass domain ownership check
 * @return int|false The ID of the newly added record, or false on error..
 */
function add_dns_record($domain_id, $name, $content, $type, $ttl, $prio, $bypass = false) {
	$domain_id = (int)$domain_id;
	if (!validate_input(-1, $domain_id, $type, $content, $name, $prio, $ttl))
		return false;
	if (!$domain_info = get_dns_domain($domain_id, $bypass))
		return false;
	if ($type == 'SPF')
		$content = '\"' . $content . '\"';
	if (preg_match('/^(?P<ordername>.*)\.' . str_replace('.', '\\.', $domain_info['name']) . '$/', $name, $matches)) {
		$ordername = str_replace('.', ' ', strrev($matches['ordername']));
	} else {
		$ordername = '';
	}
	$db = new db_mdb2(POWERDNS_DB, POWERDNS_USER, POWERDNS_PASSWORD, POWERDNS_HOST);
	$query = make_insert_query('records', [
		'domain_id' => $domain_id,
		'name' => $name,
		'content' => $content,
		'type' => $type,
		'ttl' => $ttl,
		'prio' => $prio,
		'ordername' => $ordername,
		'auth' => 1
										]
	);
	$db->query($query);
	$id = $db->getLastInsertId('records', 'id');
	update_soa_serial($domain_id);
	return $id;
}

/**
 * update_dns_record()
 * Update a DNS Record for a given domain id.  It will take care of updating the SOA/Serial number
 * after the update so you dont need to worry about that.
 *
 * @see API
 * @param integer $domain_id The ID of the domain in question.
 * @param integer $record_id The ID of the record to update
 * @param string $name the hostname being set on the dns record.
 * @param string $content the value of the dns record, or what its set to.
 * @param string $type  dns record type.
 * @param int $ttl dns record time to live, or update time.
 * @param int $prio dns record priority
 * @return bool True on success, False on failure.
 */
function update_dns_record($domain_id, $record_id, $name, $content, $type, $ttl, $prio) {
	$domain_id = (int)$domain_id;
	$record_id = (int)$record_id;
	if (!validate_input($record_id, $domain_id, $type, $content, $name, $prio, $ttl))
		return false;
	if (!$domain_info = get_dns_domain($domain_id))
		return false;
	$db = new db_mdb2(POWERDNS_DB, POWERDNS_USER, POWERDNS_PASSWORD, POWERDNS_HOST);
	if (preg_match('/^(?P<ordername>.*)\.' . str_replace('.', '\\.', $domain_info['name']) . '$/', $name, $matches)) {
		$ordername = str_replace('.', ' ', strrev($matches['ordername']));
	} else {
		$ordername = '';
	}
	$name = $db->real_escape($name);
	$type = $db->real_escape($type);
	$ordername = $db->real_escape($ordername);
	if ($type == 'SPF') {
		$content = $db->real_escape(stripslashes('\"' . $content . '\"'));
	} else {
		$content = $db->real_escape($content);
	}
	$ttl = $db->real_escape($ttl);
	$prio = $db->real_escape($prio);
	$query = "update records set name='{$name}', type='{$type}', content='{$content}', ttl='{$ttl}', prio='{$prio}', ordername='{$ordername}', auth='1', change_date='" . time() . "' where domain_id='{$domain_id}' and id='{$record_id}'";
	$db->query($query);
	update_soa_serial($domain_id);
	if ($db->affected_rows() == 1) {
		return true;
	} else {
		return false;
	}
}

/**
 * delete_dns_record()
 * deletes a domain from the system
 *
 * @see API
 * @param int $domain_id The ID of the domain in question.
 * @return bool will return true if it succeeded, or false if there was some type of error.
 */
function delete_dns_domain($domain_id) {
	$domain_id = (int)$domain_id;
	$db = new db_mdb2(POWERDNS_DB, POWERDNS_USER, POWERDNS_PASSWORD, POWERDNS_HOST);
	if (get_dns_domain($domain_id) !== false) {
		$db->query("delete from records where domain_id=$domain_id");
		$db->query("delete from domains where id=$domain_id");
		if ($db->affected_rows() == 1)
			return true;
	}
	return false;
}

/**
 * add_dns_domain()
 * adds a new domain into our system.
 *
 * status will be "ok" if it added, or "error" if there was any problems
 * status_text will contain a description of the problem if any.
 *
 * @see API
 * @param string $domain domain name to host
 * @param string $ip ip address to assign it to.
 * @return array array with status and status_text
 */
function add_dns_domain($domain, $ip) {
	$return['status'] = 'error';
	$return['status_text'] = '';
	$domain = strtolower($domain);
	//myadmin_log('dns', 'info', "new db_mdb2(" . POWERDNS_DB . ", " . POWERDNS_USER . ", " . POWERDNS_PASSWORD . ", " . POWERDNS_HOST . ");", __LINE__, __FILE__);
	$db = new db_mdb2(POWERDNS_DB, POWERDNS_USER, POWERDNS_PASSWORD, POWERDNS_HOST);
	$db2 = new db_mdb2(POWERDNS_DB, POWERDNS_USER, POWERDNS_PASSWORD, '216.158.234.243');
	$db3 = new db_mdb2(POWERDNS_DB, POWERDNS_USER, POWERDNS_PASSWORD, '199.231.191.75');
	$custid = $GLOBALS['tf']->session->account_id;
	$module = 'default';
	if (isset($GLOBALS['tf']->variables->request['module'])) {
		if (isset($GLOBALS['modules'][$GLOBALS['tf']->variables->request['module']])) {
			$module = $GLOBALS['tf']->variables->request['module'];
			//				$custid = get_custid($custid, $module);
			$GLOBALS['tf']->accounts->set_db_module($module);
			$GLOBALS['tf']->history->set_db_module($module);
		}
	}
	$module = get_module_name($module);
	$settings = get_module_settings($module);
	$data = $GLOBALS['tf']->accounts->read($custid);
	if (!valid_domain($domain)) {
		$return['status_text'] = 'Invalid Domain Name';
		return $return;
	}
	if (!validIp($ip)) {
		$return['status_text'] = 'Invalid IP Address';
		return $return;
	}
	if ($ip == '216.158.228.164') {
		$return['status_text'] = 'I think you meant to add your VPS IP, not the DNS servers IP.';
		return $return;
	}
	$query = "select * from domains where name='" . $db->real_escape($domain) . "'";
	//myadmin_log('dns', 'info', $query, __LINE__, __FILE__);
	$result = $db->query($query, __LINE__, __FILE__);
	if ($result) {
		if ($db->num_rows() > 0) {
			$return['status_text'] = 'That Domain Is Already Setup On Our Servers, Try Another Or Contact john@interserver.net';
			return $return;
		}
	}
	if ($GLOBALS['tf']->ima != 'admin') {
		$query = "select count(*) from domains where domains.account='{$custid}'";
		$result = $db->query($query);
		$db->next_record(MYSQL_NUM);
		$domains = $db->f(0);
		if ($custid != 9110 && $domains >= MAX_DNS_DOMAINS) {
			$return['status_text'] = 'You already have ' . $domains . ' domains hosted here, please contact john@interserver.net if you want more';
			return $return;
		}
	}
	if ($GLOBALS['tf']->ima != 'admin') {
		$tlds = array_merge(get_known_tlds(), get_effective_tld_rules());
		$tldsize = count($tlds);
		$found_tld = false;
		$match_size =0;
		for ($x = 0; $x < $tldsize; $x++) {
			if (preg_match('/\.' . str_replace('.', '\.', $tlds[$x]) . '$/i', $domain)) {
				$tmatch_size = mb_strlen($tlds[$x]);
				if ($tmatch_size > $match_size) {
					$match_size = $tmatch_size;
					$found_tld = true;
					$tld = $tlds[$x];
				}
			}
		}
		$tdomain = str_replace('.' . $tld, '', $domain);
		if (mb_strpos($tdomain, '.') !== false) {
			$return['status_text'] =
				'Subdomains being added has been disabled for now.   You probably meant to add just the domain.  Contact support@interserver.net if you still want to add the subdomain as a DNS entry (matched '.$tdomain.' for '.$tld.')';
			return $return;
		}
		if ($found_tld == false) {
			$return['status_text'] = 'This domain does not appear to have a valid TLD';
			return $return;
		}
	}
	$query = make_insert_query('domains', [
		'name' => $domain,
		'type' => 'MASTER',
		'account' => $custid
	]
	);
	$query2 = make_insert_query('domains', [
		'name' => $domain,
		'master' => POWERDNS_HOST,
		'type' => 'SLAVE',
		'account' => 'admin'
	], [
		'master' => POWERDNS_HOST,
		'type' => 'SLAVE',
		'account' => 'admin'
								]
	);
	$result = $db->query($query);
	if ($result) {
		$domain_id = $db->getLastInsertId('domains', 'id');
		$db2->query($query2);
		//$db3->query($query2);
		$db->query(make_insert_query('records', [
			'domain_id' => $domain_id,
			'name' => $domain,
			'content' => 'cdns1.interserver.net. dns.interserver.net ' . date('Ymd') . '01',
			'type' => 'SOA',
			'ttl' => 86400,
			'ordername' => '',
			'auth' => 1,
			'prio' => null
		]
				   ));
		$db->query(make_insert_query('records', [
			'domain_id' => $domain_id,
			'name' => $domain,
			'content' => 'cdns1.interserver.net',
			'type' => 'NS',
			'ttl' => 86400,
			'ordername' => '',
			'auth' => 1,
			'prio' => null
		]
				   ));
		$db->query(make_insert_query('records', [
			'domain_id' => $domain_id,
			'name' => $domain,
			'content' => 'cdns2.interserver.net',
			'type' => 'NS',
			'ttl' => 86400,
			'ordername' => '',
			'auth' => 1,
			'prio' => null
		]
				   ));
		$db->query(make_insert_query('records', [
			'domain_id' => $domain_id,
			'name' => $domain,
			'content' => 'cdns3.interserver.net',
			'type' => 'NS',
			'ttl' => 86400,
			'ordername' => '',
			'auth' => 1,
			'prio' => null
		]
				   ));
		$db->query(make_insert_query('records', [
			'domain_id' => $domain_id,
			'name' => $domain,
			'content' => $ip,
			'type' => 'A',
			'ttl' => 86400,
			'ordername' => '',
			'auth' => 1,
			'prio' => null
		]
				   ));
		$db->query(make_insert_query('records', [
			'domain_id' => $domain_id,
			'name' => '*.' . $domain,
			'content' => $ip,
			'type' => 'A',
			'ttl' => 86400,
			'ordername' => '*',
			'auth' => 1,
			'prio' => null
		]
				   ));
		$db->query(make_insert_query('records', [
			'domain_id' => $domain_id,
			'name' => 'localhost.' . $domain,
			'content' => '127.0.0.1',
			'type' => 'A',
			'ttl' => 86400,
			'ordername' => 'localhost',
			'auth' => 1,
			'prio' => null
		]
				   ));
		$db->query(make_insert_query('records', [
			'domain_id' => $domain_id,
			'name' => $domain,
			'content' => 'mail.' . $domain,
			'type' => 'MX',
			'ttl' => 86400,
			'ordername' => '',
			'auth' => 1,
			'prio' => 25
		]
				   ));
		$return['status'] = 'ok';
		$return['status_text'] = 'Domain ' . $domain . ' Added!';
	} else {
		$return['status'] = 'error';
		$return['status_text'] = 'Database specific error, please contact john@interserver.net and we can assist you further';
	}
	return $return;
}

/**
 * reverse_dns()
 * sets up reverse dns for a given IP address.
 *
 * @param string $ip the ip address you want reverse changed for.
 * @param string $host the hostname you'd you want to set DNS on the IP to.
 * @param string $action optional, defaults to set_reverse, can also be remove_reverse
 * @return bool true if it was able to make the requested changes, false if it wasn't.
 */
function reverse_dns($ip, $host = '', $action = 'set_reverse') {
	if (!validIp($ip, FALSE))
		return false;
	$actions = ['set_reverse', 'remove_reverse'];
	if (!in_array($action, $actions))
		$action = 'set_reverse';
	if ($action == 'set_reverse') {
		if (!valid_hostname($host)) {
			dialog('Invalid', "Your reverse dns setting for <b>$ip</b> of <b>$host</b> does not appear to be a valid domain name.  Please try again or contact support@interserver.net for assistance.");
			return false;
		}
		if (mb_strpos($host, '_') !== false)
			dialog('Invalid Character _', 'The _ character is not allowed in reverse DNS entries');
	}
	$username = $GLOBALS['tf']->accounts->data['account_lid'];
	if (null === $username || $username == '')
		$username = 'unknown';
	global $dbh_city;
	$db = new db_mdb2('dns', 'dns', 'python', '66.45.228.79');
	$db->query(make_insert_query('changes', [
		'id' => null,
		'username' => $username,
		'ip' => $ip,
		'hostname' => $host,
		'action' => $action
	]
			   ));
	//myadmin_log('dns', 'info', "Reverse DNS $ip => $host", __LINE__, __FILE__);
	if ($db->affected_rows() == 1) {
		return true;
	} else {
		return false;
	}
}
