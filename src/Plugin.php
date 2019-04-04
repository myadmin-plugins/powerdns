<?php

namespace Detain\MyAdminPowerDns;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class Plugin
 *
 * @package Detain\MyAdminPowerDns
 */
class Plugin
{
	public static $name = 'PowerDNS Plugin';
	public static $description = 'Allows handling of PowerDNS Ticket Support/Helpdesk System';
	public static $help = '';
	public static $type = 'plugin';

	/**
	 * Plugin constructor.
	 */
	public function __construct()
	{
	}

	/**
	 * @return array
	 */
	public static function getHooks()
	{
		return [
			'api.register' => [__CLASS__, 'apiRegister'],
			'function.requirements' => [__CLASS__, 'getRequirements'],
			'system.settings' => [__CLASS__, 'getSettings'],
			//'ui.menu' => [__CLASS__, 'getMenu'],
		];
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function apiRegister(GenericEvent $event)
	{
		/**
		 * @var \ServiceHandler $subject
		 */
		//$subject = $event->getSubject();
		api_register('get_dns_domain', ['domain_id' => 'int'], ['return' => 'sql_row:dns:domains'], 'Gets the DNS entry for a given Domain ID');
		api_register('get_dns_records', ['domain_id' => 'int'], ['return' => 'array:sql_row:dns:records'], 'Gets the DNS records associated with given Domain ID');
		api_register('delete_dns_record', ['domain_id' => 'int', 'record_id' => 'int'], ['return' => 'boolean'], 'Deletes a single DNS record');
		api_register('delete_dns_domain', ['domain_id' => 'int'], ['return' => 'boolean'], 'Deletes a Domain from our DNS servers');
		api_register('add_dns_record', ['domain_id' => 'int', 'name' => 'string', 'content' => 'string', 'type' => 'string', 'ttl' => 'int', 'prio' => 'int'], ['return' => 'int'], 'Adds a single DNS record');
		api_register('update_dns_record', ['domain_id' => 'int', 'record_id' => 'int', 'name' => 'string', 'content' => 'string', 'type' => 'string', 'ttl' => 'int', 'prio' => 'int'], ['return' => 'boolean'], 'Updates a single DNS record');
		api_register('add_dns_domain', ['domain' => 'string', 'ip' => 'string'], ['return' => 'result_status'], 'Adds a new domain into our system.  The status will be "ok" if it added, or "error" if there was any problems status_text will contain a description of the problem if any.');
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getMenu(GenericEvent $event)
	{
		$menu = $event->getSubject();
		if ($GLOBALS['tf']->ima == 'admin') {
			function_requirements('has_acl');
		}
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getRequirements(GenericEvent $event)
	{
		/**
		 * @var \MyAdmin\Plugins\Loader $loader
		 */
		$loader = $event->getSubject();
		$loader->add_page_requirement('get_hostname', '/../vendor/detain/myadmin-powerdns/src/dns.functions.inc.php');
		$loader->add_page_requirement('get_dns_domain', '/../vendor/detain/myadmin-powerdns/src/dns.functions.inc.php');
		$loader->add_page_requirement('get_dns_records', '/../vendor/detain/myadmin-powerdns/src/dns.functions.inc.php');
		$loader->add_page_requirement('delete_dns_record', '/../vendor/detain/myadmin-powerdns/src/dns.functions.inc.php');
		$loader->add_page_requirement('add_dns_record', '/../vendor/detain/myadmin-powerdns/src/dns.functions.inc.php');
		$loader->add_page_requirement('update_dns_record', '/../vendor/detain/myadmin-powerdns/src/dns.functions.inc.php');
		$loader->add_page_requirement('delete_dns_domain', '/../vendor/detain/myadmin-powerdns/src/dns.functions.inc.php');
		$loader->add_page_requirement('add_dns_domain', '/../vendor/detain/myadmin-powerdns/src/dns.functions.inc.php');
		$loader->add_page_requirement('reverse_dns', '/../vendor/detain/myadmin-powerdns/src/dns.functions.inc.php');
		$loader->add_page_requirement('get_db_mdb2', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('get_zone_name_from_id', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('endsWith', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('is_valid_email', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('get_record_types', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('set_timezone', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('isError', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('get_soa_record', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('get_soa_serial', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('get_next_date', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('get_next_serial', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('set_soa_serial', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('update_soa_record', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('update_soa_serial', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('validate_input', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('is_valid_hostname_fqdn', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('is_valid_ipv4', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('is_valid_ipv6', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('are_multipe_valid_ips', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('is_valid_printable', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('is_valid_rr_cname_name', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('is_valid_rr_cname_exists', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('is_valid_rr_cname_unique', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('is_not_empty_cname_rr', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('is_valid_non_alias_target', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('is_valid_rr_hinfo_content', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('is_valid_rr_soa_content', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('is_valid_rr_soa_name', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('is_valid_rr_prio', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('is_valid_rr_srv_name', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('is_valid_rr_srv_content', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('is_valid_rr_ttl', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('is_valid_search', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('is_valid_spf', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('is_valid_loc', '/../vendor/detain/myadmin-powerdns/src/pdns.functions.inc.php');
		$loader->add_page_requirement('add_domain', '/../vendor/detain/myadmin-powerdns/src/add_domain.php');
		$loader->add_page_requirement('basic_dns_editor', '/../vendor/detain/myadmin-powerdns/src/basic_dns_editor.php');
		$loader->add_page_requirement('dns_add', '/../vendor/detain/myadmin-powerdns/src/dns_add.php');
		$loader->add_requirement('add_dns_default_domain', '/../vendor/detain/myadmin-powerdns/src/dns_default_domains.php');
		$loader->add_page_requirement('dns_delete', '/../vendor/detain/myadmin-powerdns/src/dns_delete.php');
		$loader->add_page_requirement('dns_editor2', '/../vendor/detain/myadmin-powerdns/src/dns_editor2.php');
		$loader->add_page_requirement('dns_editor', '/../vendor/detain/myadmin-powerdns/src/dns_editor.php');
		$loader->add_page_requirement('dns_list', '/../vendor/detain/myadmin-powerdns/src/dns_list.php');
		$loader->add_page_requirement('dns_manager', '/../vendor/detain/myadmin-powerdns/src/dns_manager.php');
		$loader->add_page_requirement('dns_resolvers', '/../vendor/detain/myadmin-powerdns/src/dns_resolvers.php');
		$loader->add_page_requirement('list_domains', '/../vendor/detain/myadmin-powerdns/src/list_domains.php');
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getSettings(GenericEvent $event)
	{
		/**
		 * @var \MyAdmin\Settings $settings
		 **/
		$settings = $event->getSubject();
//		$settings->add_text_setting(_('Support'), _('PowerDNS'), 'kayako_api_url', _('PowerDNS API URL'), _('PowerDNS API URL'), POWERDNS_API_URL);
//		$settings->add_text_setting(_('Support'), _('PowerDNS'), 'kayako_api_key', _('PowerDNS API Key'), _('PowerDNS API Key'), POWERDNS_API_KEY);
//		$settings->add_text_setting(_('Support'), _('PowerDNS'), 'kayako_api_secret', _('PowerDNS API Secret'), _('PowerDNS API Secret'), POWERDNS_API_SECRET);
	}
}
