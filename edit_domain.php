<?php

use \MyDb\Mdb2\Db as db_mdb2;

/**
 * edit_domain()
 * The DNS Editor
 *
 * @return void
 * @throws \Exception
 * @throws \SmartyException
 */
function edit_domain() {
	page_title('DNS Editor');
	$db = new db_mdb2(POWERDNS_DB, POWERDNS_USER, POWERDNS_PASSWORD, POWERDNS_HOST);
	$custid = $GLOBALS['tf']->session->account_id;
	$domain_id = (int)$GLOBALS['tf']->variables->request['id'];
	$table = new TFTable;
	$domain = get_dns_domain($domain_id, FALSE, 'view_service');
	if (!isset($GLOBALS['tf']->variables->request['update']) && !isset($GLOBALS['tf']->variables->request['delete'])) {
	} else {
		$verify_csrf = verify_csrf('edit_domain');
	}
	$csrf_token = $table->csrf('edit_domain');
	if ($domain === false) {
		add_output("There was an error with the query, or you dont have access to that domain or it doesn't exist");
		return;
	}
	add_js('bootstrap');
	add_js('select2');
	$GLOBALS['tf']->add_html_head_css_file('/css/dns.css');
	$GLOBALS['tf']->add_html_head_js_file('/js/edit_domain.js');        
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
	$smarty = new TFSmarty();
	$smarty->assign('id', $domain_id);
	$smarty->assign('csrf_token', $csrf_token);
	add_output($smarty->fetch('dns/edit_domain.tpl'));
	add_output($table->make_link('choice=none.basic_dns_editor&amp;edit=' . $domain_id, 'Go To Basic DNS Editor') . '<br>');
	add_output($table->make_link('choice=none.dns_manager', 'Go Back To DNS Manager'));
}
