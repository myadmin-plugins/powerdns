<?php

	/**
	 * dns_editor()
	 * The DNS Editor
	 *
	 * @return void
	 */
	function dns_editor() {
		page_title('DNS Editor');
		$db = new db(POWERDNS_DB, POWERDNS_USER, POWERDNS_PASSWORD, POWERDNS_HOST);
		$custid = $GLOBALS['tf']->session->account_id;
		$domain_id = (int)$GLOBALS['tf']->variables->request['edit'];
		$table = new TFTable;
		$domain = get_dns_domain($domain_id, false, 'view_service');
		if (isset($GLOBALS['tf']->variables->request['update']) || isset($GLOBALS['tf']->variables->request['delete']))
			$verify_csrf = verify_csrf('dns_editor');
		$csrf_token = $table->csrf('dns_editor');
		if ($domain !== false) {
			if (isset($GLOBALS['tf']->variables->request['update']) && $verify_csrf) {
				if (validate_input($GLOBALS['tf']->variables->request['update'], $domain_id, $GLOBALS['tf']->variables->request['type'], $GLOBALS['tf']->variables->request['content'], $GLOBALS['tf']->variables->request['name'], $GLOBALS['tf']->variables->request['prio'], $GLOBALS['tf']->variables->request['ttl'])) {
					$record = $GLOBALS['tf']->variables->request['update'];
					$name = $GLOBALS['tf']->variables->request['name'];
					$type = $GLOBALS['tf']->variables->request['type'];
					if ($type == 'SPF') {
						$content = $GLOBALS['tf']->variables->request['content'];
					} else {
						$content = $GLOBALS['tf']->variables->request['content'];
					}
					$ttl = $GLOBALS['tf']->variables->request['ttl'];
					$prio = $GLOBALS['tf']->variables->request['prio'];
					if (isset($GLOBALS['tf']->variables->request['update']) && $GLOBALS['tf']->variables->request['update'] == -1) {
						add_dns_record($domain_id, $name, $content, $type, $ttl, $prio);
						add_output('Record Added');
					} else {
						add_output('Record Updated');
						update_dns_record($domain_id, $record, $name, $content, $type, $ttl, $prio);
					}
				} else {
					add_output('There were errors validating your data');
				}
				unset($GLOBALS['tf']->variables->request['update']);
				unset($GLOBALS['tf']->variables->request['record']);
			}
			if (isset($GLOBALS['tf']->variables->request['delete']) && $GLOBALS['tf']->variables->request['delete'] == 1 && $verify_csrf) {
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
			if ($records !== false)
				foreach ($records as $idx => $record) {
					if (isset($GLOBALS['tf']->variables->request['record']) && $GLOBALS['tf']->variables->request['record'] == $record['id']) {
						$table->add_hidden('update', $record['id']);
						$table->add_field("<table cellspacing=0 cellpadding=0><tr><td><input type=\"text\" name=\"name\" value=\"" . trim(str_replace($domain['name'], '', $record['name']), '.') . "\" class=\"input\"></td><td>." . $domain['name'] . '</td></tr></table>');
						$sel = "<select name=\"type\">\n";
						foreach (get_record_types() as $type_available) {
							if ($type_available == $record['type'])
								$add = ' SELECTED';
							else
								$add = '';
							$sel .= ' <option' . $add . " value=\"" . $type_available . "\" >" . $type_available . "</option>\n";
						}
						$sel .= "</select>\n";
						$table->add_field($sel);
						$table->add_field($table->make_input('content', htmlspecial($record['content']), 25));
						$table->add_field($table->make_input('ttl', $record['ttl'], 5));
						$table->add_field($table->make_input('prio', $record['prio'], 3));
						$table->add_field($table->make_submit('Update') . $table->make_link('choice=none.dns_editor&amp;edit=' . $domain_id, '<input type=button value=Cancel>'));
						$table->add_row();
					} else {
						$table->add_field($record['name']);
						$table->add_field($record['type']);
						if (strlen($record['content']) > 30)
							$table->add_field('<a href="#" title="' . htmlspecial($record['content']) . '">' . substr($record['content'], 0, 30) . '...</a>');
						else
							$table->add_field($record['content']);
						$table->add_field($record['ttl']);
						if (in_array($record['type'], array('MX', 'SRV')))
							$table->add_field($record['prio']);
						else
							$table->add_field();
						if ($record['type'] != 'SOA')
						{
							$table->add_field($table->make_link('choice=none.dns_editor&edit=' . $domain_id . '&record=' . $record['id'], 'Edit'). ' '. $table->make_link('choice=none.dns_editor&edit=' . $domain_id . '&record=' . $record['id'] . '&delete=1&csrf_token=' . $csrf_token, 'Delete'));
						}
						else
						{
							$table->add_field($table->make_link('choice=none.dns_editor&edit=' . $domain_id . '&record=' . $record['id'], 'Edit'));
						}
						$table->add_row();
					}
				}
			if (!isset($GLOBALS['tf']->variables->request['record'])) {
				$table->add_hidden('update', -1);
				$table->add_field("<table cellspacing=0 cellpadding=0><tr><td><input type=\"text\" name=\"name\" value=\"\" class=\"input\"></td><td>." . $domain['name'] . '</td></tr></table>');
				$sel = "<select name=\"type\">\n";
				foreach (get_record_types() as $type_available) {
					if ($type_available == 'A') {
						$add = ' SELECTED';
					} else {
						$add = '';
					}
					$sel .= ' <option' . $add . " value=\"" . $type_available . "\" >" . $type_available . "</option>\n";
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
		} else {
			add_output("There was an error with the query, or you dont have access to that domain or it doesn't exist");
		}
		add_output($table->make_link('choice=none.basic_dns_editor&amp;edit=' . $domain_id, 'Go To Basic DNS Editor') . '<br>');
		add_output($table->make_link('choice=none.dns_manager', 'Go Back To DNS Manager'));
	}