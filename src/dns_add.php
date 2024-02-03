<?php

/**
 * The DNS Editor
 *
 * @return void
 * @throws \Exception
 * @throws \SmartyException
 */
function dns_add()
{
    page_title(_('DNS Editor'));
    $table = new TFTable;
    if (isset($GLOBALS['tf']->variables->request['update']) || isset($GLOBALS['tf']->variables->request['delete'])) {
        $verify_csrf = verify_csrf('dns_add');
    }
    $csrf_token = $GLOBALS['tf']->session->get_csrf('dns_add');
    add_js('bootstrap');
    add_js('select2');
    $GLOBALS['tf']->add_html_head_css_file('/css/dns.css');
    $GLOBALS['tf']->add_html_head_js_file('/js/dns_add.js');
    $smarty = new TFSmarty();
    $smarty->assign('csrf_token', $csrf_token);
    add_output($smarty->fetch('dns/dns_add.tpl'));
    add_output($table->make_link('choice=none.dns_manager', 'Go Back To DNS Manager'));
}
