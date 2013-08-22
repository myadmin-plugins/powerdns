<?php
	/**
	 * Power DNS Related Functionality
	 * Last Changed: $LastChangedDate$
	 * @author $Author$
	 * @version $Revision$
	 * @copyright 2012
	 * @package MyAdmin
	 * @category DNS 
	 */

	if (!function_exists('_'))
	{
		function _($text)
		{
			return $text;
		}
	}

	include (INCLUDE_ROOT . '/database/class.db_mdb2.functions.inc.php');
	//	include(INCLUDE_ROOT . '/dns/poweradmin/inc/database.inc.php');
	include (INCLUDE_ROOT . '/dns/poweradmin/inc/dns.inc.php');
	include (INCLUDE_ROOT . '/dns/poweradmin/inc/record.inc.php');
	include (INCLUDE_ROOT . '/dns/poweradmin/inc/error.inc.php');

	global $db_mdb2;
	$db_mdb2 = new db_mdb2;

	$valid_tlds = array(
		"ac",
		"ad",
		"ae",
		"aero",
		"af",
		"ag",
		"ai",
		"al",
		"am",
		"an",
		"ao",
		"aq",
		"ar",
		"arpa",
		"as",
		"asia",
		"at",
		"au",
		"aw",
		"ax",
		"az",
		"ba",
		"bb",
		"bd",
		"be",
		"bf",
		"bg",
		"bh",
		"bi",
		"biz",
		"bj",
		"bm",
		"bn",
		"bo",
		"br",
		"bs",
		"bt",
		"bv",
		"bw",
		"by",
		"bz",
		"ca",
		"cat",
		"cc",
		"cd",
		"cf",
		"cg",
		"ch",
		"ci",
		"ck",
		"cl",
		"cm",
		"cn",
		"co",
		"com",
		"coop",
		"cr",
		"cu",
		"cv",
		"cx",
		"cy",
		"cz",
		"de",
		"dj",
		"dk",
		"dm",
		"do",
		"dz",
		"ec",
		"edu",
		"ee",
		"eg",
		"er",
		"es",
		"et",
		"eu",
		"fi",
		"fj",
		"fk",
		"fm",
		"fo",
		"fr",
		"ga",
		"gb",
		"gd",
		"ge",
		"gf",
		"gg",
		"gh",
		"gi",
		"gl",
		"gm",
		"gn",
		"gov",
		"gp",
		"gq",
		"gr",
		"gs",
		"gt",
		"gu",
		"gw",
		"gy",
		"hk",
		"hm",
		"hn",
		"hr",
		"ht",
		"hu",
		"id",
		"ie",
		"il",
		"im",
		"in",
		"info",
		"int",
		"io",
		"iq",
		"ir",
		"is",
		"it",
		"je",
		"jm",
		"jo",
		"jobs",
		"jp",
		"ke",
		"kg",
		"kh",
		"ki",
		"km",
		"kn",
		"kp",
		"kr",
		"kw",
		"ky",
		"kz",
		"la",
		"lb",
		"lc",
		"li",
		"lk",
		"lr",
		"ls",
		"lt",
		"lu",
		"lv",
		"ly",
		"ma",
		"mc",
		"md",
		"me",
		"mg",
		"mh",
		"mil",
		"mk",
		"ml",
		"mm",
		"mn",
		"mo",
		"mobi",
		"mp",
		"mq",
		"mr",
		"ms",
		"mt",
		"mu",
		"museum",
		"mv",
		"mw",
		"mx",
		"my",
		"mz",
		"na",
		"name",
		"nc",
		"ne",
		"net",
		"nf",
		"ng",
		"ni",
		"nl",
		"no",
		"np",
		"nr",
		"nu",
		"nz",
		"om",
		"org",
		"pa",
		"pe",
		"pf",
		"pg",
		"ph",
		"pk",
		"pl",
		"pm",
		"pn",
		"pr",
		"pro",
		"ps",
		"pt",
		"pw",
		"py",
		"qa",
		"re",
		"ro",
		"rs",
		"ru",
		"rw",
		"sa",
		"sb",
		"sc",
		"sd",
		"se",
		"sg",
		"sh",
		"si",
		"sj",
		"sk",
		"sl",
		"sm",
		"sn",
		"so",
		"sr",
		"st",
		"su",
		"sv",
		"sy",
		"sz",
		"tc",
		"td",
		"tel",
		"tf",
		"tg",
		"th",
		"tj",
		"tk",
		"tl",
		"tm",
		"tn",
		"to",
		"tp",
		"tr",
		"travel",
		"tt",
		"tv",
		"tw",
		"tz",
		"ua",
		"ug",
		"uk",
		"us",
		"uy",
		"uz",
		"va",
		"vc",
		"ve",
		"vg",
		"vi",
		"vn",
		"vu",
		"wf",
		"ws",
		"xn--0zwm56d",
		"xn--11b5bs3a9aj6g",
		"xn--80akhbyknj4f",
		"xn--9t4b11yi5a",
		"xn--deba0ad",
		"xn--g6w251d",
		"xn--hgbk6aj7f53bba",
		"xn--hlcj6aya9esc7a",
		"xn--jxalpdlp",
		"xn--kgbechtv",
		"xn--zckzah",
		"ye",
		"yt",
		"yu",
		"za",
		"zm",
		"zw");

	// Array of the available zone types
	$server_types = array(
		"MASTER",
		"SLAVE",
		"NATIVE");

	// $rtypes - array of possible record types
	$rtypes = array(
		'A',
		'AAAA',
		'CNAME',
		'HINFO',
		'MX',
		'NAPTR',
		'NS',
		'PTR',
		'SOA',
		'SPF',
		'SRV',
		'SSHFP',
		'TXT',
		'RP');

	// If fancy records is enabled, extend this field.
	if (isset($dns_fancy) && $dns_fancy)
	{
		$rtypes[14] = 'URL';
		$rtypes[15] = 'MBOXFW';
		$rtypes[16] = 'CURL';
	}

	/**
	 * is_valid_email()
	 * 
	 * @param mixed $address
	 * @return
	 */
	function is_valid_email($address)
	{
		$fields = preg_split("/@/", $address, 2);
		if ((!preg_match("/^[0-9a-z]([-_.]?[0-9a-z])*$/i", $fields[0])) || (!isset($fields[1]) || $fields[1] == '' || !is_valid_hostname_fqdn($fields[1], 0)))
		{
			return false;
		}
		return true;
	}

	// Set timezone (required for PHP5)
	/**
	 * set_timezone()
	 * 
	 * @return
	 */
	function set_timezone()
	{
		global $timezone;

		if (function_exists('date_default_timezone_set'))
		{
			if (isset($timezone))
			{
				date_default_timezone_set($timezone);
			}
			else
				if (!ini_get('date.timezone'))
				{
					date_default_timezone_set('UTC');
				}
		}
	}

	/**
	 * error()
	 * 
	 * @param mixed $msg
	 * @return
	 */
	function error($msg)
	{
		if ($msg)
		{
			add_output("     <div class=\"error\">Error: " . $msg . "</div>\n");
		}
		else
		{
			add - output("     <div class=\"error\">" . 'An unknown error has occurred.' . "</div>\n");
		}
	}

	/**
	 * isError()
	 * 
	 * @param mixed $result
	 * @return
	 */
	function isError($result)
	{
		return $result->error;
	}
?>
