<?php

use \MyDb\Mdb2\Db as db_mdb2;

/**
 * dns_delete()
 * deletes a domain from the DNS server
 * @return void
 */
function dns_delete() {
	page_title('Delete DNS Record');
	$db = new db_mdb2(POWERDNS_DB, POWERDNS_USER, POWERDNS_PASSWORD, POWERDNS_HOST);
	$custid = $GLOBALS['tf']->session->account_id;
	$domain_id = $db->real_escape($GLOBALS['tf']->variables->request['id']);
	$table = new TFTable;
	$result = get_dns_domain($domain_id);
	if ($result !== false) {
		if (isset($GLOBALS['tf']->variables->request['confirm']) && $GLOBALS['tf']->variables->request['confirm'] == 'yes' && verify_csrf('dns_delete')) {
			delete_dns_domain($domain_id);
			add_output('Domain Removed');
			$GLOBALS['tf']->redirect($GLOBALS['tf']->link('index.php', 'choice=none.dns_manager'));
		} else {
			$table = new TFTable;
			$table->csrf('dns_delete');
			$table->set_title('Confirm Domain Delete');
			$table->add_hidden('id', $domain_id);
			$table->add_field('<select name=confirm><option value=no>No</option><option value=yes>Yes</option></select>');
			$table->add_field($table->make_submit('Continue With Delete'));
			$table->add_row();
			add_output($table->get_table());
		}
	}
}
