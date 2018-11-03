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
    }    

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getMenu(GenericEvent $event)
	{
		$menu = $event->getSubject();
		if ($GLOBALS['tf']->ima == 'admin') {
			function_requirements('has_acl');
//				$menu->add_link('admin', 'choice=none.abuse_admin', '/lib/webhostinghub-glyphs-icons/icons/development-16/Black/icon-spam.png', 'PowerDNS');
		}
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getRequirements(GenericEvent $event)
	{
		$loader = $event->getSubject();
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getSettings(GenericEvent $event)
	{
		$settings = $event->getSubject();
//		$settings->add_text_setting('Support', 'PowerDNS', 'kayako_api_url', 'PowerDNS API URL:', 'PowerDNS API URL', POWERDNS_API_URL);
//		$settings->add_text_setting('Support', 'PowerDNS', 'kayako_api_key', 'PowerDNS API Key:', 'PowerDNS API Key', POWERDNS_API_KEY);
//		$settings->add_text_setting('Support', 'PowerDNS', 'kayako_api_secret', 'PowerDNS API Secret:', 'PowerDNS API Secret', POWERDNS_API_SECRET);
	}
}
