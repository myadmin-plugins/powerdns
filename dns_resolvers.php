<?php

	/**
	 * dns_resolvers()
	 * @return void
	 */
	function dns_resolvers() {
		$table = new TFTable;
		$table->set_title('DNS Servers');
		$table->add_field('Primary DNS', 'l');
		$table->add_field('64.20.34.50', 'r');
		$table->add_row();
		$table->add_field('Secondary DNS', 'l');
		$table->add_field('66.45.228.250', 'r');
		$table->add_row();
		$table->set_colspan(2);
		$table->add_field('Use these DNS settings in your VPS so your server can resolve domains');
		$table->add_row();
		add_output('<br><br><br>');
		add_output($table->get_table());
	}
