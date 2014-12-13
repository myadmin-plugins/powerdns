<?php

require_once (dirname(__FILE__) . '/../../include/config.tlds.php');

function get_effective_tld($url_or_domain)
{
    $domain = $url_or_domain;
    preg_match('/^[a-z]+:\/\//i', $domain) and 
        $domain = parse_url($domain, PHP_URL_HOST);
    $domain = mb_strtolower($domain, 'UTF-8');
    if (strpos($domain, '.') === false)	
		return null;
	$rules = get_effective_tld_rules();
        $segments = '';
		$domain_parts = array_reverse(explode('.', $domain));
        foreach ($domain_parts as $s)
        {
            $wildcard = rtrim('*.'.$segments, '.');
            $segments = rtrim($s.'.'.$segments, '.');

            if (in_array('!'.$segments, $rules))
            {
                $tld = substr($wildcard, 2);
                break;
            }
            elseif (in_array($wildcard, $rules) or 
                    in_array($segments, $rules))
            {
                $tld = $segments;
            }
        }

    if (isset($tld)) 
		return $tld;
    return false;
}



function checkPublicSuffix($domain , $expected_public_domain)
{
	$tld = get_effective_tld($domain);
	$public = NULL;
	if (!is_null($tld))
		if (preg_match('/^([^\.].*\.)?(?P<public>[^\.]+\.'.str_replace('.','\\.', $tld).')$/i', $domain, $matches))
			$public = $matches['public'];
		//else
			//echo "Not sure what to do here, Domain $domain got TLD $tld with no public domain match\n";
	if (is_null($public))
		if (is_null($expected_public_domain))
			echo "GOOD	$domain (TLD ".var_export($tld, true).") (NULL == NULL)\n";
		else
			echo "BAD	$domain (TLD ".var_export($tld, true).") (NULL != $expected_public_domain)\n";
	else
		if (is_null($expected_public_domain))
			echo "BAD	$domain (TLD ".var_export($tld, true).") ($public != NULL)\n";
		else
			echo "GOOD	$domain (TLD ".var_export($tld, true).") ($public == $expected_public_domain)\n";
	
}

?>
