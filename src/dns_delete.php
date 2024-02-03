<?php

use \MyDb\Mdb2\Db as db_mdb2;

/**
 * dns_delete()
 * deletes a domain from the DNS server
 *
 * @return void
 * @throws \Exception
 * @throws \SmartyException
 */
function dns_delete()
{
    page_title(_('Delete DNS Record'));
    $db = new db_mdb2(POWERDNS_DB, POWERDNS_USER, POWERDNS_PASSWORD, POWERDNS_HOST);
    $domain_id = $db->real_escape($GLOBALS['tf']->variables->request['id']);
    $table = new TFTable;
    function_requirements('get_dns_domain');
    $result = get_dns_domain($domain_id);
    if ($result !== false) {
        if (isset($GLOBALS['tf']->variables->request['confirm']) && $GLOBALS['tf']->variables->request['confirm'] == 'yes' && verify_csrf('dns_delete')) {
            function_requirements('delete_dns_domain');
            delete_dns_domain($domain_id);
            flash_message('success', 'Domain DNS removed.', 'dns_manager');
        } else {
            $table = new TFTable;
            $table->csrf('dns_delete');
            $table->set_title('Confirm Domain Delete');
            $table->set_options('style="width: 50%;"');
            $table->add_hidden('id', $domain_id);
            $table->add_field('Are you sure want to delete ?');
            $table->add_row();
            $table->add_field('
				<label class="radio-inline">
					<input type="radio" name="confirm" checked value="no">No
				</label>
				<label class="radio-inline">
					<input type="radio" name="confirm" value="yes">Yes
				</label>');
            $table->add_row();
            $table->add_field($table->make_submit('Submit'));
            $table->add_row();
            add_output($table->get_table());
        }
    }
}
