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
    $table = new TFTable;
    if (isset(\MyAdmin\App::variables()->request['update']) || isset(\MyAdmin\App::variables()->request['delete'])) {
        $verify_csrf = verify_csrf('dns_list');
    }
    $csrf_token = \MyAdmin\App::session()->get_csrf('dns_list');
    add_js('bootstrap');
    add_js('select2');
    \MyAdmin\App::output()->addHeadCssFile('/css/dns.css');
    \MyAdmin\App::output()->addHeadJsFile('/js/dns_list.js');
    $smarty = new TFSmarty();
    $smarty->assign('csrf_token', $csrf_token);
    add_output($smarty->fetch('dns/dns_list.tpl'));
}
