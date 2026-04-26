<?php

use \MyDb\Mdb2\Db as db_mdb2;

/**
 * dns_editor2()
 * The DNS Editor
 *
 * @return void
 * @throws \Exception
 * @throws \SmartyException
 */
function dns_editor2()
{
    page_title(_('DNS Editor'));
    $db = new db_mdb2(POWERDNS_DB, POWERDNS_USER, POWERDNS_PASSWORD, POWERDNS_HOST);
    $custid = \MyAdmin\App::session()->account_id;
    $domain_id = isset(\MyAdmin\App::variables()->request['edit']) ? (int)\MyAdmin\App::variables()->request['edit'] : (int)\MyAdmin\App::variables()->request['id'];
    $table = new TFTable;
    function_requirements('get_dns_domain');
    $domain = get_dns_domain($domain_id, false, 'view_service');
    if (!isset(\MyAdmin\App::variables()->request['update']) && !isset(\MyAdmin\App::variables()->request['delete'])) {
    } else {
        $verify_csrf = verify_csrf('dns_editor');
    }
    $csrf_token = $table->csrf('dns_editor', false);
    if ($domain !== false) {
        if (\MyAdmin\App::ima() == 'admin') {
            add_output(_('Domain Owner') . ':' . \MyAdmin\App::accounts()->cross_reference($domain['account']) . '<br>');
        }
        if (isset(\MyAdmin\App::variables()->request['update']) && $verify_csrf) {
            if (validate_input(\MyAdmin\App::variables()->request['update'], $domain_id, \MyAdmin\App::variables()->request['type'], \MyAdmin\App::variables()->request['content'], \MyAdmin\App::variables()->request['name'], \MyAdmin\App::variables()->request['prio'], \MyAdmin\App::variables()->request['ttl'], $error)) {
                $record = \MyAdmin\App::variables()->request['update'];
                $name = trim(\MyAdmin\App::variables()->request['name']);
                $type = \MyAdmin\App::variables()->request['type'];
                if ($type == 'SPF') {
                    $content = \MyAdmin\App::variables()->request['content'];
                } else {
                    $content = \MyAdmin\App::variables()->request['content'];
                }
                $ttl = \MyAdmin\App::variables()->request['ttl'];
                $prio = \MyAdmin\App::variables()->request['prio'];
                if (isset(\MyAdmin\App::variables()->request['update']) && \MyAdmin\App::variables()->request['update'] == -1) {
                    function_requirements('add_dns_record');
                    add_dns_record($domain_id, $name, $content, $type, $ttl, $prio);
                    add_output('Record Added');
                } else {
                    add_output('Record Updated');
                    function_requirements('update_dns_record');
                    update_dns_record($domain_id, $record, $name, $content, $type, $ttl, $prio);
                }
            } else {
                add_output('There were errors validating your data: '.$error);
            }
            unset(\MyAdmin\App::variables()->request['update']);
            unset(\MyAdmin\App::variables()->request['record']);
        }
        if (isset(\MyAdmin\App::variables()->request['delete']) && \MyAdmin\App::variables()->request['delete'] == 1 && $verify_csrf) {
            delete_dns_record($domain_id, \MyAdmin\App::variables()->request['record']);
            unset(\MyAdmin\App::variables()->request['delete']);
            unset(\MyAdmin\App::variables()->request['record']);
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
        if ($records !== false) {
            foreach ($records as $idx => $record) {
                if (isset(\MyAdmin\App::variables()->request['record']) && \MyAdmin\App::variables()->request['record'] == $record['id']) {
                    $table->add_hidden('update', $record['id']);
                    $table->add_field('<table cellspacing=0 cellpadding=0><tr><td><input type="text" name="name" value="' . trim(str_replace($domain['name'], '', $record['name']), '.') . '" class="input"></td><td>.' . $domain['name'] . '</td></tr></table>');
                    $sel = "<select name=\"type\">\n";
                    foreach (get_record_types() as $type_available) {
                        if ($type_available == $record['type']) {
                            $add = ' SELECTED';
                        } else {
                            $add = '';
                        }
                        $sel .= ' <option' . $add . ' value="' . $type_available . '" >' . $type_available . "</option>\n";
                    }
                    $sel .= "</select>\n";
                    $table->add_field($sel);
                    $table->add_field($table->make_input('content', htmlspecial($record['content']), 25));
                    $table->add_field($table->make_input('ttl', $record['ttl'], 5));
                    $table->add_field($table->make_input('prio', $record['prio'], 3));
                    $table->add_field($table->make_submit('Update') . $table->make_link('choice=none.dns_editor2&amp;edit=' . $domain_id, '<input type=button value=Cancel>'));
                    $table->add_row();
                } else {
                    $table->add_field($record['name']);
                    $table->add_field($record['type']);
                    if (mb_strlen($record['content']) > 30) {
                        $table->add_field('<a href="#" title="' . htmlspecial($record['content']) . '">' . mb_substr($record['content'], 0, 30) . '...</a>');
                    } else {
                        $table->add_field($record['content']);
                    }
                    $table->add_field($record['ttl']);
                    if (in_array($record['type'], ['MX', 'SRV'])) {
                        $table->add_field($record['prio']);
                    } else {
                        $table->add_field();
                    }
                    if ($record['type'] != 'SOA') {
                        $table->add_field($table->make_link('choice=none.dns_editor2&edit=' . $domain_id . '&record=' . $record['id'], 'Edit') . ' ' . $table->make_link('choice=none.dns_editor2&edit=' . $domain_id . '&record=' . $record['id'] . '&delete=1&csrf_token=' . $csrf_token, 'Delete'));
                    } else {
                        $table->add_field($table->make_link('choice=none.dns_editor2&edit=' . $domain_id . '&record=' . $record['id'], 'Edit'));
                    }
                    $table->add_row();
                }
            }
        }
        if (!isset(\MyAdmin\App::variables()->request['record'])) {
            $table->add_hidden('update', -1);
            $table->add_field('<table cellspacing=0 cellpadding=0><tr><td><input type="text" name="name" value="" class="input"></td><td>.' . $domain['name'] . '</td></tr></table>');
            $sel = "<select name=\"type\">\n";
            foreach (get_record_types() as $type_available) {
                if ($type_available == 'A') {
                    $add = ' SELECTED';
                } else {
                    $add = '';
                }
                $sel .= ' <option' . $add . ' value="' . $type_available . '" >' . $type_available . "</option>\n";
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
}
