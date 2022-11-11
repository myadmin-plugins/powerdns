<?php

function basic_dns_editor()
{
	$smarty = new TFSmarty();
	page_title(_('Basic DNS Editor'));
	$custid = $GLOBALS['tf']->session->account_id;
	$domain_id = (int)$GLOBALS['tf']->variables->request['edit'];
	$types = [
		'A' => 'Point To IP',
		'CNAME' => 'Points To Hostname',
		'MX' => 'Send Mail To'
	];
	$table = new TFTable;
	if (isset($GLOBALS['tf']->variables->request['update']) || isset($GLOBALS['tf']->variables->request['delete'])) {
		$verify_csrf = verify_csrf('dns_editor');
	}
	$csrf_token = $table->csrf('dns_editor', false);
	function_requirements('get_dns_domain');
	$domain = get_dns_domain($domain_id, false, 'view_service');
	if ($domain !== false) {
		if ($GLOBALS['tf']->ima == 'admin') {
			add_output(_('Domain Owner') . ':' . $GLOBALS['tf']->accounts->cross_reference($domain['account']) . '<br>');
		}
		if (isset($GLOBALS['tf']->variables->request['update']) && $verify_csrf) {
			if ($GLOBALS['tf']->variables->request['type'] == 'MX' && $GLOBALS['tf']->variables->request['prio'] == '') {
				$GLOBALS['tf']->variables->request['prio'] = 10;
			}
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
					function_requirements('add_dns_record');
					add_dns_record($domain_id, $name, $content, $type, $ttl, $prio);
					add_output('Record Added');
				} else {
					add_output('Record Updated');
					function_requirements('update_dns_record');
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
		$table->set_title('Basic DNS Domain Editor ' . $table->make_link('choice=none.dns_editor&amp;id=' . $domain_id, '(Advanced)'));
		$table->add_field('Hostname');
		$table->add_field('Type');
		$table->add_field('Address');
		//$table->add_field('TTL');
		//$table->add_field('Priority');
		$table->add_field();
		$table->add_row();
		$records = get_dns_records($domain_id);
		foreach ($records as $idx => $record) {
			if (in_array($record['type'], ['SOA', 'NS'])) {
				continue;
			}
			if (isset($GLOBALS['tf']->variables->request['record']) && $GLOBALS['tf']->variables->request['record'] == $record['id']) {
				$table->add_hidden('update', $record['id']);
				$table->add_field('<table cellspacing=0 cellpadding=0><tr><td><input type="text" name="name" value="' . trim(str_replace($domain['name'], '', $record['name']), '.') . '" class="input"></td><td>.' . $domain['name'] . '</td></tr></table>');
				$sel = "<select name=\"type\">\n";
				foreach ($types as $type_available => $type_desc) {
					if ($type_available == $record['type']) {
						$add = ' SELECTED';
					} else {
						$add = '';
					}
					$sel .= ' <option' . $add . ' value="' . $type_available . '" >' . $type_desc . "</option>\n";
				}
				$sel .= "</select>\n";
				$table->add_field($sel);
				$table->add_field($table->make_input('content', htmlspecial($record['content']), 25));
				$table->add_hidden('ttl', $record['ttl']);
				$table->add_hidden('prio', $record['prio']);
				//$table->add_field($table->make_input('ttl', $record['ttl'], 5));
				//$table->add_field($table->make_input('prio', $record['prio'], 3));
				$table->add_field($table->make_submit('Update') . $table->make_link('choice=none.basic_dns_editor&amp;edit=' . $domain_id, '<input type=button value=Cancel>'));
				$table->add_row();
			} else {
				$table->add_field($record['name']);
				if (isset($types[$record['type']])) {
					$type = $types[$record['type']];
				}
				$table->add_field($type);
				if (mb_strlen($record['content']) > 30) {
					$table->add_field('<a href="#" title="' . htmlspecial($record['content']) . '">' . mb_substr($record['content'], 0, 30) . '...</a>');
				} else {
					$table->add_field($record['content']);
				}
				//$table->add_field($record['ttl']);
				if (in_array($record['type'], ['MX', 'SRV'])) {
					//$table->add_field($record['prio']);
				} else {
					//$table->add_field();
				}
				if ($record['type'] != 'SOA') {
					$table->add_field($table->make_link('choice=none.basic_dns_editor&edit=' . $domain_id . '&record=' . $record['id'], 'Edit') . ' ' . (($record['type'] == 'A' && $record['name'] == $domain['name']) ? '' : $table->make_link('choice=none.dns_editor2&edit=' . $domain_id . '&record=' . $record['id'] . '&delete=1&csrf_token=' . $csrf_token, 'Delete')));
				} else {
					$table->add_field();
				}
				$table->add_row();
			}
		}
		if (!isset($GLOBALS['tf']->variables->request['record'])) {
			$table->add_hidden('update', -1);
			$table->add_field('<table cellspacing=0 cellpadding=0><tr><td><input type="text" name="name" value="" class="input"></td><td>.' . $domain['name'] . '</td></tr></table>');
			$sel = "<select name=\"type\">\n";
			foreach ($types as $type_available => $type_desc) {
				if ($type_available == 'A') {
					$add = ' SELECTED';
				} else {
					$add = '';
				}
				$sel .= ' <option' . $add . ' value="' . $type_available . '" >' . $type_desc . "</option>\n";
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
	} else {
		add_output('<div class="container alert alert-danger">There was an error with the query, or you do not have access to that domain or it does not exist.</div>');
	}
	add_output($smarty->fetch('dns/basic_dns_editor.tpl'));
	page_heading('Basic DNS Editor');
	breadcrums(['home' => 'Home', 'basic_dns_editor' => 'Basic DNS Editor']);
}
