<?php

/**
 * The DNS Editor
 *
 * @return void
 * @throws \Exception
 * @throws \SmartyException
 */
function edit_domain() {
	page_title('DNS Editor');
	$custid = $GLOBALS['tf']->session->account_id;
	$domain_id = (int)$GLOBALS['tf']->variables->request['id'];
	$table = new TFTable;
	$domain = get_dns_domain($domain_id, FALSE, 'view_service');
	if ($domain === false) {
		add_output("There was an error with the query, or you dont have access to that domain or it doesn't exist");
		return;
	}
	if (isset($GLOBALS['tf']->variables->request['update']) || isset($GLOBALS['tf']->variables->request['delete']))
		$verify_csrf = verify_csrf('edit_domain');
	$csrf_token = $GLOBALS['tf']->session->get_csrf('edit_domain');
	add_js('bootstrap');
	add_js('select2');
	$GLOBALS['tf']->add_html_head_css_file('/css/dns.css');
	$GLOBALS['tf']->add_html_head_js_file('/js/edit_domain.js');        
	$smarty = new TFSmarty();
	$smarty->assign('id', $domain_id);
	$smarty->assign('csrf_token', $csrf_token);
	add_output($smarty->fetch('dns/edit_domain.tpl'));
	add_output($table->make_link('choice=none.basic_dns_editor&amp;edit=' . $domain_id, 'Go To Basic DNS Editor') . '<br>');
	add_output($table->make_link('choice=none.dns_editor2&amp;edit=' . $domain_id, 'Go Back to Old DNS Editor') . '<br>');
	add_output($table->make_link('choice=none.dns_manager', 'Go Back To DNS Manager'));
}
