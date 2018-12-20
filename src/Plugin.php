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
         * @var \MyAdmin\Plugins\Loader $this->loader
         */
        $loader = $event->getSubject();
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
