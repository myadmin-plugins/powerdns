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
    $domain_id = isset(\MyAdmin\App::variables()->request['id']) ? (int)\MyAdmin\App::variables()->request['id'] : (int)\MyAdmin\App::variables()->request['edit'];
    function_requirements('get_dns_domain');
    $domain = get_dns_domain($domain_id, false, 'view_service');
    if ($domain === false) {
        add_output("There was an error with the query, or you dont have access to that domain or it doesn't exist");
        return;
    }
    if (isset(\MyAdmin\App::variables()->request['update']) || isset(\MyAdmin\App::variables()->request['delete'])) {
        $verify_csrf = verify_csrf('dns_editor');
    }
    $csrf_token = \MyAdmin\App::session()->get_csrf('dns_editor', false);
    add_js('bootstrap');
    add_js('select2');
    $smarty = new TFSmarty();
    $smarty->assign('id', $domain_id);
    $smarty->assign('csrf_token', $csrf_token);
    page_heading('Advanced DNS Editor');
    breadcrums(['home' => 'Home', 'dns_editor' => 'Advanced DNS Editor']);
    if (\MyAdmin\App::ima() == 'admin') {
        add_output(_('Domain Owner') . ':' . \MyAdmin\App::accounts()->cross_reference($domain['account']) . '<br>');
    }
    add_output($smarty->fetch('dns/dns_editor.tpl'));
}
