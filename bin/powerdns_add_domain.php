#!/usr/bin/env php
<?php

	require_once __DIR__.'/../../../../include/console.functions.inc.php';
	global $console;
	if ($_SERVER['argc'] != 4 && $_SERVER['argc'] != 3) {
		echo "$console[WHITE]Usage: $console[LIGHTCYAN]" . $_SERVER['argv'][0] . "$console[WHITE] <$console[LIGHTPURPLE]account$console[WHITE]> <$console[LIGHTPURPLE]domain$console[WHITE]> <$console[LIGHTPURPLE]ip$console[WHITE]> [$console[BROWN]confirm$console[WHITE]]\n";
		exit;
	}


	$account = $_SERVER['argv'][1];
	$dns_domain = $_SERVER['argv'][2];
	$dns_ip = $_SERVER['argv'][3];

	require_once __DIR__.'/../../../../include/functions.inc.php';

	function_requirements('add_dns_domain');
	$custid = $GLOBALS['tf']->accounts->cross_reference($account);
	$webpage = false;
	define('VERBOSE_MODE', false);
	$GLOBALS['tf']->session->create($custid, 'admin');
	$sid = $GLOBALS['tf']->session->sessionid;
	function_requirements('add_dns_domain');
	print_r(add_dns_domain($dns_domain, $dns_ip));
	$GLOBALS['tf']->session->destroy();
