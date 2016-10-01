<?php
	/**
	 * dns_manager()
	 * @return void
	 */
	function dns_manager() {
		page_title('DNS Manager');
		$custid = $GLOBALS['tf']->session->account_id;
		$module = 'default';
		$db = $GLOBALS['tf']->db;
		if (isset($GLOBALS['tf']->variables->request['module'])) {
			if (isset($GLOBALS['modules'][$GLOBALS['tf']->variables->request['module']])) {
				$module = $GLOBALS['tf']->variables->request['module'];
				$db = get_module_db($module);
				//				$custid = get_custid($custid, $module);
				$GLOBALS['tf']->accounts->set_db_module($module);
				$GLOBALS['tf']->history->set_db_module($module);
			}
		}
		$module = get_module_name($module);
		$settings = get_module_settings($module);
		$data = $GLOBALS['tf']->accounts->read($custid);

		if (isset($GLOBALS['tf']->variables->request['new']) && $GLOBALS['tf']->variables->request['new'] == 1 && verify_csrf_referer(__LINE__, __FILE__)) {
			if (isset($GLOBALS['tf']->variables->request['ip'])) {
				$ip = trim($db->real_escape($GLOBALS['tf']->variables->request['ip']));
				if (isset($GLOBALS['tf']->variables->request['domain']) && trim($GLOBALS['tf']->variables->request['domain']) != '') {
					$domain = trim($GLOBALS['tf']->variables->request['domain']);
					$result = add_dns_domain($domain, $ip);
					add_output($result['status_text']);
				}
				if (isset($GLOBALS['tf']->variables->request['domains']) && !in_array(trim($GLOBALS['tf']->variables->request['domains']), array('', 'Domain Names...'))) {
					$domains = explode("\n", $GLOBALS['tf']->variables->request['domains']);
					foreach ($domains as $domain) {
						$domain = trim($domain);
						if ($domain != '') {
							$result = add_dns_domain($domain, $ip);
							add_output($result['status_text']);
						}
					}
				}
			}
		}
		if ($GLOBALS['tf']->ima == 'admin')
			add_output(render_form('dns_manager', 'view_service'));
		else
			add_output(render_form('dns_manager', array('custid' => get_custid($GLOBALS['tf']->session->account_id, 'domains'))));
		$table = new TFTable;
		$table->set_title('DNS Servers');
		$table->add_field('Primary DNS');
		$table->add_field('&nbsp;');
		$table->add_field('cdns1.interserver.net');
		$table->add_field('&nbsp;');
		$table->add_field(POWERDNS_HOST);
		$table->add_row();
		$table->add_field('Secondary DNS');
		$table->add_field('&nbsp;');
		$table->add_field('cdns2.interserver.net');
		$table->add_field('&nbsp;');
		$table->add_field('216.158.234.243');
		$table->add_row();
		$table->add_field('Tertiary DNS');
		$table->add_field('&nbsp;');
		$table->add_field('cdns3.interserver.net');
		$table->add_field('&nbsp;');
		$table->add_field('199.231.191.75');
		$table->add_row();
		add_output('<br><br><br>');
		add_output($table->get_table());
	}
?>
