<?php

use \MyDb\Mdb2\Db as db_mdb2;

/**
 * @param null $domain_id
 */
function add_dns_default_domain($domain_id = null) {
	if ($domain_id && htmlspecial($domain_id)) {
		$pdb = new db_mdb2(POWERDNS_DB, POWERDNS_USER, POWERDNS_PASSWORD, POWERDNS_HOST);
		$db_domains = get_module_db('domains');
		$db_domains->query('select domain_id,domain_username,domain_password,domain_hostname,domain_invoice,domain_type,domain_cost,domain_custid from domains where domain_id = $domain_id and domain_status = "active" and domain_custid not in (select account_id from accounts where account_ima="admin") limit 1');
		function_requirements('dns_manager');
		while ($db_domains->next_record(MYSQL_ASSOC)) {
			$pdb->query("select records.* from domains left join records on domains.id=records.domain_id and (domains.name = records.name or records.name='') and (records.type='A' OR records.type IS NULL ) where domains.name = '{$db_domains->Record['domain_hostname']}'");
			$pdb->next_record(MYSQL_ASSOC);
			if (empty($pdb->Record)) {
				$domain = trim($db_domains->Record['domain_hostname']);
				$ip = '66.45.228.100';
				$result = add_dns_domain($domain, $ip);
				myadmin_log('dns', 'info', "For domain - $domain default dns entry added is added. Response - ".json_encode($result), __LINE__, __FILE__);
				$pdb->query("update domains set account={$db_domains->Record['domain_custid']} where name='{$db->Record['domain_hostname']}'");
			}
		}
	}
}
