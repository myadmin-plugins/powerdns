<?php
	/**
	 * DNS Related Functionality
	 * Last Changed: $LastChangedDate$
	 * @author $Author$
	 * @version $Revision$
	 * @copyright 2012
	 * @package MyAdmin
	 * @category DNS
	 */

	include (dirname(__file__) . '/pdns.functions.inc.php');

	/**
	 * max domains hosted on our dns server per client
	 */
	define('MAX_DNS_DOMAINS', 250);

	/**
	 * get_hostname()
	 * ok this is a fucking awesome insanely fast way to lookup reverse dns settings for ips
	 * basically what I did was i gave My and Nucleus   ACL (AXFR) permission on City,
	 * so that instead of having to lookup ips one at a time they can load an entire 256
	 * IPs at a time.   It caches all the IPs, and only does another query if it doesnt
	 * already have the IP cached.   This allows us to do lookups 2600% Faster than most
	 * any other way.    This was needed because as we're looking at reverse dns settings for
	 * clients with multiple vlans and potentially tons of IPs, thats a TON of queries to
	 * be making normally to get all the reverse dns settings for them from city, but this way
	 * it will only be a couple queries no matter how many ips.   It also caches all results.
	 *
	 * @see API
	 * @param string $ip IP Address
	 * @return string|false Hostname
	 */
	function get_hostname($ip)
	{
		global $cached_zones;
		$parts = explode('.', $ip);
		$zone = $parts[2] . '.' . $parts[1] . '.' . $parts[0] . '.in-addr.arpa';
		if (is_local_ip($ip))
		{
			@include_once ('Net/DNS2.php');
			if (class_exists('Net_DNS2_Resolver'))
			{
				$resolver = new Net_DNS2_Resolver(array('nameservers' => array('66.45.228.79')));
				//$resolver->nameservers = array('66.45.228.79');
				if (!isset($cached_zones[$zone]))
				{
					$tzone = array();
					try {
						$response = $resolver->query($zone, 'AXFR');
					} catch(Net_DNS2_Exception $e) {
						//echo "::query() failed: ", $e->getMessage(), "\n";
						$host = gethostbyaddr($ip);
						if ($host != $ip)
						{
							$cached_zones[$zone][$ip] = $host;
							unset($host);
							return $cached_zones[$zone][$ip];
						}
						return false;
					}
					//billingd_log(print_r($response, true));
					if (count($response->answer))
					{
						foreach ($response->answer as $rr)
						{
							if ($rr->type == 'PTR')
							{
								$tzone[implode(".", array_reverse(explode(".", str_replace('.in-addr.arpa', '', $rr->name))))] = $rr->ptrdname;
							}
						}
						$cached_zones[$zone] = $tzone;
						//billingd_log("City AXFR Loaded $zone with " . sizeof($tzone) . " IPs", __line__, __file__);
					}
				}
				if (isset($cached_zones[$zone]))
				{
					if (isset($cached_zones[$zone][$ip]))
					{
						return $cached_zones[$zone][$ip];
					}
				}
			}
			else
			{
				if ($GLOBALS['tf']->session->appsession('emailed_no_net_dns') != 1)
				{
					$subject = 'My Install Missing Net/DNS';
					$headers = '';
					$headers .= "MIME-Version: 1.0" . EMAIL_NEWLINE;
					$headers .= "Content-type: text/html; charset=iso-8859-1" . EMAIL_NEWLINE;
					$headers .= "From: " . TITLE . " <" . EMAIL_FROM . ">" . EMAIL_NEWLINE;
					//				$headers .= "To: Joe Huss <detain@interserver.net>" . EMAIL_NEWLINE;
					$email = 'The pear module Net/DNS is missing on server ' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['HOSTNAME']) . '<br>';
					$email .= 'login as root and type:<br>   pear install Net/DNS<br>to fix<br>';
					admin_mail($subject, $email, $headers, false, 'admin_email_problems.tpl');
					$GLOBALS['tf']->session->appsession('emailed_no_net_dns', 1);
				}
				if (!isset($cached_zones[$zone]))
				{
					$cached_zones[$zone] = array();
				}
				if (isset($cached_zones[$zone][$ip]))
				{
					return $cached_zones[$zone][$ip];
				}
				$host = gethostbyaddr($ip);
				if ($host != $ip)
				{
					$cached_zones[$zone][$ip] = $host;
					unset($host);
					return $cached_zones[$zone][$ip];
				}
			}
		}
		else
		{
			$host = gethostbyaddr($ip);
			if ($host != $ip)
			{
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
	 * @return array|false Either an array containing some information about the domain or false on failure.
	 */
	function get_dns_domain($domain_id)
	{
		$domain_id = intval($domain_id);
		$db = new db('poweradmin', 'poweradmin', POWERADMIN_PASSWORD, POWERADMIN_HOST);
		$custid = $GLOBALS['tf']->session->account_id;
		if ($GLOBALS['tf']->ima == 'admin')
		{
			$db->query("select * from domains where id='$domain_id'");
		}
		else
		{
			$db->query("select * from domains where id='$domain_id' and account='$custid'");
		}
		if ($db->num_rows() > 0)
		{
			$db->next_record(MYSQL_ASSOC);
			return $db->Record;
		}
		return false;
	}

	/**
	 * get_dns_records()
	 * To be used in combination with {@}get_dns_domain
	 * This gets all the records for a given domain.
	 * @todo add in some custid check here will have to do a join w/ the domains table.
	 *
	 * @see API
	 * @param int $domain_id The ID of the domain in question.
	 * @return array|false Either an array containing some information about the domain or false on failure.
	 */
	function get_dns_records($domain_id)
	{
		$domain_id = intval($domain_id);
		if (get_dns_domain($domain_id) === false)
		{
			return false;
		}
		$db = new db('poweradmin', 'poweradmin', POWERADMIN_PASSWORD, POWERADMIN_HOST);
		$custid = $GLOBALS['tf']->session->account_id;
		$db->query("select * from records where domain_id='$domain_id'");
		$results = array();
		if ($db->num_rows() > 0)
		{
			while ($db->next_record(MYSQL_ASSOC))
			{
				$results[] = $db->Record;
			}
		}
		else
		{
			return false;
		}
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
	function delete_dns_record($domain_id, $record_id)
	{
		$domain_id = intval($domain_id);
		$record_id = intval($record_id);
		$db = new db('poweradmin', 'poweradmin', POWERADMIN_PASSWORD, POWERADMIN_HOST);
		if (get_dns_domain($domain_id) !== false)
		{
			$db->query("delete from records where domain_id='$domain_id' and id='$record_id'");
			if ($db->affected_rows() == 1)
			{
				return true;
			}
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
	 * @return int|false The ID of the newly added record, or false on error..
	 */
	function add_dns_record($domain_id, $name, $content, $type, $ttl, $prio)
	{
		$domain_id = intval($domain_id);
		if (!validate_input(-1, $domain_id, $type, $content, $name, $prio, $ttl))
		{
			return false;
		}
		if (get_dns_domain($domain_id) === false)
		{
			return false;
		}
		if ($type == 'SPF')
		{
			$content = '\"' . $content . '\"';
		}
		$db = new db('poweradmin', 'poweradmin', POWERADMIN_PASSWORD, POWERADMIN_HOST);
		$query = make_insert_query('records', array(
			'domain_id' => $domain_id,
			'name' => $name,
			'content' => $content,
			'type' => $type,
			'ttl' => $ttl,
			'prio' => $prio));
		$db->query($query);
		$id = $db->get_last_insert_id('records', 'id');
		update_soa_serial($domain_id);
		return $id;
	}

	/**
	 * update_dns_record()
	 * Updte a DNS Record for a given domain id.  It will take care of updating the SOA/Serial number
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
	function update_dns_record($domain_id, $record_id, $name, $content, $type, $ttl, $prio)
	{
		$domain_id = intval($domain_id);
		$record_id = intval($record_id);
		if (!validate_input($record_id, $domain_id, $type, $content, $name, $prio, $ttl))
		{
			return false;
		}
		if (get_dns_domain($domain_id) === false)
		{
			return false;
		}
		$db = new db('poweradmin', 'poweradmin', POWERADMIN_PASSWORD, POWERADMIN_HOST);
		$name = $db->real_escape($name);
		$type = $db->real_escape($type);
		if ($type == 'SPF')
		{
			$content = $db->real_escape(stripslashes('\"' . $content . '\"'));
		}
		else
		{
			$content = $db->real_escape($content);
		}
		$ttl = $db->real_escape($ttl);
		$prio = $db->real_escape($prio);
		$query = "update records set name='$name', type='$type', content='$content', ttl='$ttl', prio='$prio', change_date='" . mysql_now() . "' where domain_id='$domain_id' and id='$record_id'";
		$db->query($query);
		update_soa_serial($domain_id);
		if ($db->affected_rows() == 1)
		{
			return true;
		}
		else
		{
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
	function delete_dns_domain($domain_id)
	{
		$domain_id = intval($domain_id);
		$db = new db('poweradmin', 'poweradmin', POWERADMIN_PASSWORD, POWERADMIN_HOST);
		if (get_dns_domain($domain_id) !== false)
		{
			$db->query("delete from records where domain_id=$domain_id");
			$db->query("delete from domains where id=$domain_id");
			if ($db->affected_rows() == 1)
			{
				return true;
			}
		}
		return false;
	}

	function basic_dns_editor()
	{
		page_title('Basic DNS Editor');
		$db = new db('poweradmin', 'poweradmin', POWERADMIN_PASSWORD, POWERADMIN_HOST);
		$custid = $GLOBALS['tf']->session->account_id;
		$domain_id = intval($GLOBALS['tf']->variables->request['edit']);
		$types = array(
			'A' => 'Point To IP',
			'CNAME' => 'Points To Hostname',
			'MX' => 'Send Mail To');
		$table = new TFTable;
		$domain = get_dns_domain($domain_id);
		if ($domain !== false)
		{
			if (isset($GLOBALS['tf']->variables->request['update']))
			{
				if ($GLOBALS['tf']->variables->request['type'] == 'MX' && $GLOBALS['tf']->variables->request['prio'] == '')
				{
					$GLOBALS['tf']->variables->request['prio'] = 10;
				}
				if (validate_input($GLOBALS['tf']->variables->request['update'], $domain_id, $GLOBALS['tf']->variables->request['type'], $GLOBALS['tf']->variables->request['content'], $GLOBALS['tf']->variables->request['name'], $GLOBALS['tf']->variables->request['prio'], $GLOBALS['tf']->variables->request['ttl']))
				{
					$record = $GLOBALS['tf']->variables->request['update'];
					$name = $GLOBALS['tf']->variables->request['name'];
					$type = $GLOBALS['tf']->variables->request['type'];
					if ($type == 'SPF')
					{
						$content = $GLOBALS['tf']->variables->request['content'];
					}
					else
					{
						$content = $GLOBALS['tf']->variables->request['content'];
					}
					$ttl = $GLOBALS['tf']->variables->request['ttl'];
					$prio = $GLOBALS['tf']->variables->request['prio'];
					if (isset($GLOBALS['tf']->variables->request['update']) && $GLOBALS['tf']->variables->request['update'] == -1)
					{
						add_dns_record($domain_id, $name, $content, $type, $ttl, $prio);
						add_output('Record Added');
					}
					else
					{
						add_output('Record Updated');
						update_dns_record($domain_id, $record, $name, $content, $type, $ttl, $prio);
					}
				}
				else
				{
					add_output('There were errors validating your data');
				}
				unset($GLOBALS['tf']->variables->request['update']);
				unset($GLOBALS['tf']->variables->request['record']);
			}
			if (isset($GLOBALS['tf']->variables->request['delete']) && $GLOBALS['tf']->variables->request['delete'] == 1)
			{
				delete_dns_record($domain_id, $GLOBALS['tf']->variables->request['record']);
				unset($GLOBALS['tf']->variables->request['delete']);
				unset($GLOBALS['tf']->variables->request['record']);
			}
			$table->add_hidden('edit', $domain_id);
			$table->set_title('Basic DNS Domain Editor ' . $table->make_link('choice=none.dns_editor&amp;edit=' . $domain_id, '(Advanced)'));
			$table->add_field('Hostname');
			$table->add_field('Type');
			$table->add_field('Address');
			//$table->add_field('TTL');
			//$table->add_field('Priority');
			$table->add_field();
			$table->add_row();
			$records = get_dns_records($domain_id);
			foreach ($records as $idx => $record)
			{
				if (in_array($record['type'], array('SOA', 'NS')))
				{
					continue;
				}
				if (isset($GLOBALS['tf']->variables->request['record']) && $GLOBALS['tf']->variables->request['record'] == $record['id'])
				{
					$table->add_hidden('update', $record['id']);
					$table->add_field("<table cellspacing=0 cellpadding=0><tr><td><input type=\"text\" name=\"name\" value=\"" . trim(str_replace($domain['name'], '', $record["name"]), '.') . "\" class=\"input\"></td><td>." . $domain['name'] . "</td></tr></table>");
					$sel = "<select name=\"type\">\n";
					foreach ($types as $type_available => $type_desc)
					{
						if ($type_available == $record["type"])
						{
							$add = " SELECTED";
						}
						else
						{
							$add = "";
						}
						$sel .= " <option" . $add . " value=\"" . $type_available . "\" >" . $type_desc . "</option>\n";
					}
					$sel .= "</select>\n";
					$table->add_field($sel);
					$table->add_field($table->make_input('content', htmlentities($record['content']), 25));
					$table->add_hidden('ttl', $record['ttl']);
					$table->add_hidden('prio', $record['prio']);
					//$table->add_field($table->make_input('ttl', $record['ttl'], 5));
					//$table->add_field($table->make_input('prio', $record['prio'], 3));
					$table->add_field($table->make_submit('Update') . $table->make_link('choice=none.basic_dns_editor&amp;edit=' . $domain_id, '<input type=button value=Cancel>'));
					$table->add_row();
				}
				else
				{
					$table->add_field($record['name']);
					if (isset($types[$record['type']]))
					{
						$type = $types[$record['type']];
					}
					$table->add_field($type);
					$table->add_field($record['content']);
					//$table->add_field($record['ttl']);
					if (in_array($record['type'], array('MX', 'SRV')))
					{
						//$table->add_field($record['prio']);
					}
					else
					{
						//$table->add_field();
					}
					if ($record['type'] != 'SOA')
					{
						$table->add_field($table->make_link('choice=none.basic_dns_editor&edit=' . $domain_id . '&record=' . $record['id'], 'Edit') . ' ' . (($record['type'] == 'A' && $record['name'] == $domain['name']) ? '' : $table->make_link('choice=none.dns_editor&edit=' . $domain_id . '&record=' . $record['id'] . '&delete=1', 'Delete')));
					}
					else
					{
						$table->add_field();
					}
					$table->add_row();
				}
			}
			if (!isset($GLOBALS['tf']->variables->request['record']))
			{
				$table->add_hidden('update', -1);
				$table->add_field("<table cellspacing=0 cellpadding=0><tr><td><input type=\"text\" name=\"name\" value=\"\" class=\"input\"></td><td>." . $domain['name'] . "</td></tr></table>");
				$sel = "<select name=\"type\">\n";
				foreach ($types as $type_available => $type_desc)
				{
					if ($type_available == 'A')
					{
						$add = " SELECTED";
					}
					else
					{
						$add = "";
					}
					$sel .= " <option" . $add . " value=\"" . $type_available . "\" >" . $type_desc . "</option>\n";
				}
				$sel .= "</select>\n";
				$table->add_field($sel);
				$table->add_field($table->make_input('content', '', 25));
				$table->add_hidden('ttl', 86400);
				$table->add_hidden('prio', '');
				//$table->add_field($table->make_input('ttl', '86400', 5));
				//$table->add_field($table->make_input('prio', '', 3));
				$table->add_field($table->make_submit('Add Record'));
				$table->add_row();
			}
			add_output($table->get_table());
		}
		else
		{
			add_output('There was an error with the query, or you dont have access to that domain or it doesnt exist');
		}
		add_output($table->make_link('choice=none.dns_editor&amp;edit=' . $domain_id, 'Go To Advanced DNS Editor') . '<br>');
		add_output($table->make_link('choice=none.dns_manager', 'Go Back To DNS Manager'));
	}

	/**
	 * dns_editor()
	 * The DNS Editor
	 *
	 * @return void
	 */
	function dns_editor()
	{
		page_title('DNS Editor');
		$db = new db('poweradmin', 'poweradmin', POWERADMIN_PASSWORD, POWERADMIN_HOST);
		$custid = $GLOBALS['tf']->session->account_id;
		$domain_id = intval($GLOBALS['tf']->variables->request['edit']);
		$table = new TFTable;
		$domain = get_dns_domain($domain_id);
		if ($domain !== false)
		{
			if (isset($GLOBALS['tf']->variables->request['update']))
			{
				if (validate_input($GLOBALS['tf']->variables->request['update'], $domain_id, $GLOBALS['tf']->variables->request['type'], $GLOBALS['tf']->variables->request['content'], $GLOBALS['tf']->variables->request['name'], $GLOBALS['tf']->variables->request['prio'], $GLOBALS['tf']->variables->request['ttl']))
				{
					$record = $GLOBALS['tf']->variables->request['update'];
					$name = $GLOBALS['tf']->variables->request['name'];
					$type = $GLOBALS['tf']->variables->request['type'];
					if ($type == 'SPF')
					{
						$content = $GLOBALS['tf']->variables->request['content'];
					}
					else
					{
						$content = $GLOBALS['tf']->variables->request['content'];
					}
					$ttl = $GLOBALS['tf']->variables->request['ttl'];
					$prio = $GLOBALS['tf']->variables->request['prio'];
					if (isset($GLOBALS['tf']->variables->request['update']) && $GLOBALS['tf']->variables->request['update'] == -1)
					{
						add_dns_record($domain_id, $name, $content, $type, $ttl, $prio);
						add_output('Record Added');
					}
					else
					{
						add_output('Record Updated');
						update_dns_record($domain_id, $record, $name, $content, $type, $ttl, $prio);
					}
				}
				else
				{
					add_output('There were errors validating your data');
				}
				unset($GLOBALS['tf']->variables->request['update']);
				unset($GLOBALS['tf']->variables->request['record']);
			}
			if (isset($GLOBALS['tf']->variables->request['delete']) && $GLOBALS['tf']->variables->request['delete'] == 1)
			{
				delete_dns_record($domain_id, $GLOBALS['tf']->variables->request['record']);
				unset($GLOBALS['tf']->variables->request['delete']);
				unset($GLOBALS['tf']->variables->request['record']);
			}
			$table->add_hidden('edit', $domain_id);
			$table->set_title('DNS Domain Editor ' . $table->make_link('choice=none.basic_dns_editor&amp;edit=' . $domain_id, '(Basic)'));
			$table->add_field('Hostname');
			$table->add_field('Type');
			$table->add_field('Address');
			$table->add_field('TTL');
			$table->add_field('Priority');
			$table->add_field();
			$table->add_row();
			$records = get_dns_records($domain_id);
			foreach ($records as $idx => $record)
			{
				if (isset($GLOBALS['tf']->variables->request['record']) && $GLOBALS['tf']->variables->request['record'] == $record['id'])
				{
					$table->add_hidden('update', $record['id']);
					$table->add_field("<table cellspacing=0 cellpadding=0><tr><td><input type=\"text\" name=\"name\" value=\"" . trim(str_replace($domain['name'], '', $record["name"]), '.') . "\" class=\"input\"></td><td>." . $domain['name'] . "</td></tr></table>");
					$sel = "<select name=\"type\">\n";
					foreach (get_record_types() as $type_available)
					{
						if ($type_available == $record["type"])
						{
							$add = " SELECTED";
						}
						else
						{
							$add = "";
						}
						$sel .= " <option" . $add . " value=\"" . $type_available . "\" >" . $type_available . "</option>\n";
					}
					$sel .= "</select>\n";
					$table->add_field($sel);
					$table->add_field($table->make_input('content', htmlentities($record['content']), 25));
					$table->add_field($table->make_input('ttl', $record['ttl'], 5));
					$table->add_field($table->make_input('prio', $record['prio'], 3));
					$table->add_field($table->make_submit('Update') . $table->make_link('choice=none.dns_editor&amp;edit=' . $domain_id, '<input type=button value=Cancel>'));
					$table->add_row();
				}
				else
				{
					$table->add_field($record['name']);
					$table->add_field($record['type']);
					$table->add_field($record['content']);
					$table->add_field($record['ttl']);
					if (in_array($record['type'], array('MX', 'SRV')))
					{
						$table->add_field($record['prio']);
					}
					else
					{
						$table->add_field();
					}
					//if ($record['type'] != 'SOA')
					//{
						$table->add_field($table->make_link('choice=none.dns_editor&edit=' . $domain_id . '&record=' . $record['id'], 'Edit') . ' ' . $table->make_link('choice=none.dns_editor&edit=' . $domain_id . '&record=' . $record['id'] . '&delete=1', 'Delete'));
					//}
					//else
					//{
						//$table->add_field();
					//}
					$table->add_row();
				}
			}
			if (!isset($GLOBALS['tf']->variables->request['record']))
			{
				$table->add_hidden('update', -1);
				$table->add_field("<table cellspacing=0 cellpadding=0><tr><td><input type=\"text\" name=\"name\" value=\"\" class=\"input\"></td><td>." . $domain['name'] . "</td></tr></table>");
				$sel = "<select name=\"type\">\n";
				foreach (get_record_types() as $type_available)
				{
					if ($type_available == 'A')
					{
						$add = " SELECTED";
					}
					else
					{
						$add = "";
					}
					$sel .= " <option" . $add . " value=\"" . $type_available . "\" >" . $type_available . "</option>\n";
				}
				$sel .= "</select>\n";
				$table->add_field($sel);
				$table->add_field($table->make_input('content', '', 25));
				$table->add_field($table->make_input('ttl', '86400', 5));
				$table->add_field($table->make_input('prio', '', 3));
				$table->add_field($table->make_submit('Add Record'));
				$table->add_row();
			}
			add_output($table->get_table());
		}
		else
		{
			add_output('There was an error with the query, or you dont have access to that domain or it doesnt exist');
		}
		add_output($table->make_link('choice=none.basic_dns_editor&amp;edit=' . $domain_id, 'Go To Basic DNS Editor') . '<br>');
		add_output($table->make_link('choice=none.dns_manager', 'Go Back To DNS Manager'));
	}

	/**
	 * dns_delete()
	 * deletes a domain from the DNS server
	 *
	 * @return
	 */
	function dns_delete()
	{
		page_title('Delete DNS Record');
		$db = new db('poweradmin', 'poweradmin', POWERADMIN_PASSWORD, POWERADMIN_HOST);
		$custid = $GLOBALS['tf']->session->account_id;
		$domain_id = $db->real_escape($GLOBALS['tf']->variables->request['id']);
		$table = new TFTable;
		$result = get_dns_domain($domain_id);
		if ($result !== false)
		{
			if (isset($GLOBALS['tf']->variables->request['confirm']) && $GLOBALS['tf']->variables->request['confirm'] == 'yes')
			{
				delete_dns_domain($domain_id);
				add_output('Domain Removed');
				$GLOBALS['tf']->redirect($GLOBALS['tf']->link('index.php', 'choice=none.dns_manager'));
			}
			else
			{
				$table = new TFTable;
				$table->set_title('Confirm Domain Deletee');
				$table->add_hidden('id', $domain_id);
				$table->add_field('<select name=confirm><option value=no>No</option><option value=yes>Yes</option></select>');
				$table->add_field($table->make_submit('Continue With Delete'));
				$table->add_row();
				add_output($table->get_table());
			}
		}
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
	function add_dns_domain($domain, $ip)
	{
		$return['status'] = 'error';
		$return['status_text'] = '';
		$domain = strtolower($domain);
		$db = new db('poweradmin', 'poweradmin', POWERADMIN_PASSWORD, POWERADMIN_HOST);
		$db2 = new db('poweradmin', 'poweradmin', POWERADMIN_PASSWORD, '66.45.228.248');
		$db3 = new db('poweradmin', 'poweradmin', POWERADMIN_PASSWORD, '173.214.160.195');
		$custid = $GLOBALS['tf']->session->account_id;
		$module = 'default';
		if (isset($GLOBALS['tf']->variables->request['module']))
		{
			if (isset($GLOBALS['modules'][$GLOBALS['tf']->variables->request['module']]))
			{
				$module = $GLOBALS['tf']->variables->request['module'];
				//				$custid = get_custid($custid, $module);
				$GLOBALS['tf']->accounts->set_db_module($module);
				$GLOBALS['tf']->history->set_db_module($module);
			}
		}
		$module = get_module_name($module);
		$settings = get_module_settings($module);
		$data = $GLOBALS['tf']->accounts->read($custid);
		if (!valid_domain($domain))
		{
			$return['status_text'] = 'Invalid Domain Name';
			return $return;
		}
		if (!valid_ip($ip))
		{
			$return['status_text'] = 'Invalid IP Address';
			return $return;
		}
		if ($ip == '209.159.155.28')
		{
			$return['status_text'] = 'I think you meant to add your VPS IP, not the DNS servers IP.';
			return $return;
		}
		$result = $db->query("select * from domains where name='" . $db->real_escape($domain) . "'");
		if ($result)
		{
			if ($db->num_rows() > 0)
			{
				$return['status_text'] = 'That Domain Is Already Setup On Our Servers, Try Another Or Contact john@interserver.net';
				return $return;
			}
		}
		if ($GLOBALS['tf']->ima != 'admin')
		{
			$query = "select count(*) from domains where domains.account='$custid'";
			$result = $db->query($query);
			$db->next_record(MYSQL_NUM);
			$domains = $db->f(0);
			if ($domains >= MAX_DNS_DOMAINS)
			{
				$return['status_text'] = 'You already have ' . $domains . ' domains hosted here, please contact john@interserver.net if you want more';
				return $return;
			}
		}
		if ($GLOBALS['tf']->ima != 'admin')
		{
			$tlds = get_known_tlds();
			$tldsize = sizeof($tlds);
			$found_tld = false;
			for ($x = 0; $x < $tldsize; $x++)
			{
				if (preg_match('/\.' . str_replace('.', '\.', $tlds[$x]) . '$/i', $domain))
				{
					$found_tld = true;
					$tld = $tlds[$x];
					$tdomain = str_replace('.' . $tld, '', $domain);
					if (strpos($tdomain, '.') !== false)
					{
						$return['status_text'] = 'Subdomains being added has been disabled for now.   You probably meant to add just the domain.  Contact support@interserver.net if you still want to add the subdomain as a DNS entry';
						return $return;
					}
					break;
				}
			}
			if ($found_tld == false)
			{
				$return['status_text'] = 'This domain does not appear to have a valid TLD';
				return $return;
			}
		}
		$query = make_insert_query('domains', array(
			'name' => $domain,
			'type' => 'MASTER',
			'account' => $custid));
		$query2 = make_insert_query('domains', array(
			'name' => $domain,
			'master' => POWERADMIN_HOST,
			'type' => 'SLAVE',
			'account' => 'admin'));
		$result = $db->query($query);
		if ($result)
		{
			$domain_id = $db->get_last_insert_id('domains', 'id');
			$db2->query($query2);
			$db3->query($query2);
			$db->query(make_insert_query('records', array(
				'domain_id' => $domain_id,
				'name' => $domain,
				'content' => 'cdns1.interserver.net. dns.interserver.net ' . date('Ymd') . '01',
				'type' => 'SOA',
				'ttl' => 86400,
				'prio' => NULL)));
			$db->query(make_insert_query('records', array(
				'domain_id' => $domain_id,
				'name' => $domain,
				'content' => 'cdns1.interserver.net',
				'type' => 'NS',
				'ttl' => 86400,
				'prio' => NULL)));
			$db->query(make_insert_query('records', array(
				'domain_id' => $domain_id,
				'name' => $domain,
				'content' => 'cdns2.interserver.net',
				'type' => 'NS',
				'ttl' => 86400,
				'prio' => NULL)));
			$db->query(make_insert_query('records', array(
				'domain_id' => $domain_id,
				'name' => $domain,
				'content' => 'cdns3.interserver.net',
				'type' => 'NS',
				'ttl' => 86400,
				'prio' => NULL)));
			$db->query(make_insert_query('records', array(
				'domain_id' => $domain_id,
				'name' => $domain,
				'content' => $ip,
				'type' => 'A',
				'ttl' => 86400,
				'prio' => NULL)));
			$db->query(make_insert_query('records', array(
				'domain_id' => $domain_id,
				'name' => '*.' . $domain,
				'content' => $ip,
				'type' => 'A',
				'ttl' => 86400,
				'prio' => NULL)));
			$db->query(make_insert_query('records', array(
				'domain_id' => $domain_id,
				'name' => 'localhost.' . $domain,
				'content' => '127.0.0.1',
				'type' => 'A',
				'ttl' => 86400,
				'prio' => NULL)));
			$db->query(make_insert_query('records', array(
				'domain_id' => $domain_id,
				'name' => $domain,
				'content' => 'mail.' . $domain,
				'type' => 'MX',
				'ttl' => 86400,
				'prio' => 25)));
			$return['status'] = 'ok';
			$return['status_text'] = 'Domain ' . $domain . ' Added!';
		}
		else
		{
			$return['status'] = 'error';
			$return['status_text'] = 'Database specific error, please contact john@interserver.net and we can assist you further';
		}
		return $return;
	}

	/**
	 * dns_manager()
	 *
	 * @return
	 */
	function dns_manager()
	{
		page_title('DNS Manager');
		$custid = $GLOBALS['tf']->session->account_id;
		$module = 'default';
		if (isset($GLOBALS['tf']->variables->request['module']))
		{
			if (isset($GLOBALS['modules'][$GLOBALS['tf']->variables->request['module']]))
			{
				$module = $GLOBALS['tf']->variables->request['module'];
				//				$custid = get_custid($custid, $module);
				$GLOBALS['tf']->accounts->set_db_module($module);
				$GLOBALS['tf']->history->set_db_module($module);
			}
		}
		$module = get_module_name($module);
		$settings = get_module_settings($module);
		$data = $GLOBALS['tf']->accounts->read($custid);

		if (isset($GLOBALS['tf']->variables->request['new']) && $GLOBALS['tf']->variables->request['new'] == 1)
		{
			$domain = trim($db->real_escape($GLOBALS['tf']->variables->request['domain']));
			$ip = trim($db->real_escape($GLOBALS['tf']->variables->request['ip']));
			$result = add_dns_domain($domain, $ip);
			add_output($result['status_text']);
		}

		if ($GLOBALS['tf']->ima == 'admin')
		{
			add_output(render_form('dns_manager'));
		}
		else
		{
			add_output(render_form('dns_manager', array('custid' => get_custid($GLOBALS['tf']->session->account_id, 'domains'))));
		}

		$table = new TFTable;
		$table->set_title('DNS Servers');
		$table->add_field('Primary DNS');
		$table->add_field('&nbsp;');
		$table->add_field('cdns1.interserver.net');
		$table->add_field('&nbsp;');
		$table->add_field(POWERADMIN_HOST);
		$table->add_row();
		$table->add_field('Secondary DNS');
		$table->add_field('&nbsp;');
		$table->add_field('cdns2.interserver.net');
		$table->add_field('&nbsp;');
		$table->add_field('66.45.228.248');
		$table->add_row();
		$table->add_field('Tertiary DNS');
		$table->add_field('&nbsp;');
		$table->add_field('cdns3.interserver.net');
		$table->add_field('&nbsp;');
		$table->add_field('173.214.160.195');
		$table->add_row();
		add_output('<br><br><br>');
		add_output($table->get_table());
	}

	/**
	 * dns_resolvers()
	 *
	 * @return
	 */
	function dns_resolvers()
	{
		$table = new TFTable;
		$table->set_title('DNS Servers');
		$table->add_field('Primary DNS', 'l');
		$table->add_field('64.20.34.50', 'r');
		$table->add_row();
		$table->add_field('Secondary DNS', 'l');
		$table->add_field('66.45.228.250', 'r');
		$table->add_row();
		$table->set_colspan(2);
		$table->add_field('Use these DNS settings in your VPS so your server can resolve domains');
		$table->add_row();
		add_output('<br><br><br>');
		add_output($table->get_table());
	}

	/**
	 * reverse_dns()
	 * sets up reverse dns for a given IP address.
	 *
	 * @param string $ip the ip address you want reverse changed for.
	 * @param string $host the hostname youd you want to set DNS on the IP to.
	 * @return bool true if it was able to make the requested changes, false if it wasnt.
	 */
	function reverse_dns($ip, $host)
	{
		if (!valid_hostname($host))
		{
			dialog('Invalid', "Your reverse dns setting for <b>$ip</b> of <b>$host</b> does not appear to be a valid domain name.  Please try again or contact support@interserver.net for assistance.");
			return false;
		}
		if (strpos($host, '_') !== false)
		{
			dialog('Invalid Character _', 'The _ character is not allowed in reverse DNS entries');
		}
		$username = $GLOBALS['tf']->accounts->data['account_lid'];
		if (is_null($username) || $username == '')
		{
			$username = 'unknown';
		}
		global $dbh_city;
		$db = new db('dns', 'dns', 'python', '66.45.228.79');
		$db->query(make_insert_query('changes', array(
			'id' => NULL,
			'username' => $username,
			'ip' => $ip,
			'hostname' => $host)));
		//billingd_log("Reverse DNS $ip => $host", __line__, __file__);
		if ($db->affected_rows() == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
?>
