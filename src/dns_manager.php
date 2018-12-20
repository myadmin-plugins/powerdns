<?php
/**
 * dns_manager()
 *
 * @return void
 * @throws \Exception
 * @throws \SmartyException
 */
function dns_manager()
{
	page_title(_('DNS Manager'));
	function_requirements('crud_dns_manager');
	crud_dns_manager();
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
