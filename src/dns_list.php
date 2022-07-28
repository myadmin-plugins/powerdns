<?php

/**
 * The DNS Editor
 *
 * @return void
 * @throws \Exception
 * @throws \SmartyException
 */
function dns_list()
{
    page_title(_('DNS Editor'));
    $table = new TFTable();
    if (isset($GLOBALS['tf']->variables->request['update']) || isset($GLOBALS['tf']->variables->request['delete'])) {
        $verify_csrf = verify_csrf('dns_list');
    }
    $csrf_token = $GLOBALS['tf']->session->get_csrf('dns_list');
    add_js('bootstrap');
    add_js('select2');
    $GLOBALS['tf']->add_html_head_css_file('/css/dns.css');
    $GLOBALS['tf']->add_html_head_js_file('/js/dns_list.js');
    $smarty = new TFSmarty();
    $smarty->assign('csrf_token', $csrf_token);
    add_output($smarty->fetch('dns/dns_list.tpl'));
}
