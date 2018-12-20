<?php

/**
 * The DNS Editor
 *
 * @return void
 * @throws \Exception
 * @throws \SmartyException
 */
function dns_editor()
{
	page_title(_('DNS Editor'));
	$domain_id = isset($GLOBALS['tf']->variables->request['id']) ? (int)$GLOBALS['tf']->variables->request['id'] : (int)$GLOBALS['tf']->variables->request['edit'];
	$table = new TFTable;
	function_requirements('get_dns_domain');
	$domain = get_dns_domain($domain_id, false, 'view_service');
	if ($domain === false) {
		add_output("There was an error with the query, or you dont have access to that domain or it doesn't exist");
		return;
	}
	if (isset($GLOBALS['tf']->variables->request['update']) || isset($GLOBALS['tf']->variables->request['delete'])) {
		$verify_csrf = verify_csrf('dns_editor');
	}
	$csrf_token = $GLOBALS['tf']->session->get_csrf('dns_editor');
	add_js('bootstrap');
	add_js('select2');
	$GLOBALS['tf']->add_html_head_css_file('/css/dns.css');
	$GLOBALS['tf']->add_html_head_js_file('/js/dns_editor.js');
	$smarty = new TFSmarty();
	$smarty->assign('id', $domain_id);
	$smarty->assign('csrf_token', $csrf_token);
	add_output($smarty->fetch('dns/dns_editor.tpl'));
	add_output($table->make_link('choice=none.basic_dns_editor&amp;edit=' . $domain_id, 'Go To Basic DNS Editor') . '<br>');
	add_output($table->make_link('choice=none.dns_editor2&amp;edit=' . $domain_id, 'Go Back to Old DNS Editor') . '<br>');
	add_output($table->make_link('choice=none.dns_manager', 'Go Back To DNS Manager'));
}
