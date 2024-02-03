#!/usr/bin/env php
<?php
/**
* This is a script to find licenses that kept getting billed after they expired
* @author Joe Huss <detain@interserver.net>
* @package MyAdmin
* @category map_everything_to_my
* @copyright 2018
*/
    $_SERVER['HTTP_HOST'] = 'my.interserver.net';

    require_once __DIR__.'/../../../../include/functions.inc.php';

    $db = clone $GLOBALS['tf']->db;
    $db->query("select history_owner, history_new_value from history_log where history_type='dns_manager' and history_new_value like '%new%'");
    while ($db->next_record(MYSQL_ASSOC)) {
        $data = myadmin_unstringify($db->Record['history_new_value']);
        if ($data['new'] == 1 && valid_domain($data['domain'])) {
            echo "update domains set account={$db->Record['history_owner']} where name='".$data['domain']."';\n";
        }
    }
