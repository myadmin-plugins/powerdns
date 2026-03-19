<?php

declare(strict_types=1);

namespace Detain\MyAdminPowerDns\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Static analysis tests for DNS functions.
 *
 * These tests verify function existence, parameter counts, signatures,
 * and global variable state for functions defined in pdns.functions.inc.php.
 * Functions from dns.functions.inc.php are not tested here as they require
 * database classes that are unavailable in the test environment.
 */
class DnsFunctionsStaticAnalysisTest extends TestCase
{
    /**
     * Load the functions file once before the test suite runs.
     */
    public static function setUpBeforeClass(): void
    {
        if (!function_exists('endsWith')) {
            require_once __DIR__ . '/../src/pdns.functions.inc.php';
        }
    }

    // ---------------------------------------------------------------
    // Function existence checks for pdns.functions.inc.php
    // ---------------------------------------------------------------

    /**
     * Test that get_db_mdb2 function is defined.
     */
    public function testGetDbMdb2FunctionExists(): void
    {
        $this->assertTrue(function_exists('get_db_mdb2'));
    }

    /**
     * Test that get_zone_name_from_id function is defined.
     */
    public function testGetZoneNameFromIdFunctionExists(): void
    {
        $this->assertTrue(function_exists('get_zone_name_from_id'));
    }

    /**
     * Test that endsWith function is defined.
     */
    public function testEndsWithFunctionExists(): void
    {
        $this->assertTrue(function_exists('endsWith'));
    }

    /**
     * Test that is_valid_email function is defined.
     */
    public function testIsValidEmailFunctionExists(): void
    {
        $this->assertTrue(function_exists('is_valid_email'));
    }

    /**
     * Test that get_record_types function is defined.
     */
    public function testGetRecordTypesFunctionExists(): void
    {
        $this->assertTrue(function_exists('get_record_types'));
    }

    /**
     * Test that get_soa_record function is defined.
     */
    public function testGetSoaRecordFunctionExists(): void
    {
        $this->assertTrue(function_exists('get_soa_record'));
    }

    /**
     * Test that get_soa_serial function is defined.
     */
    public function testGetSoaSerialFunctionExists(): void
    {
        $this->assertTrue(function_exists('get_soa_serial'));
    }

    /**
     * Test that get_next_date function is defined.
     */
    public function testGetNextDateFunctionExists(): void
    {
        $this->assertTrue(function_exists('get_next_date'));
    }

    /**
     * Test that get_next_serial function is defined.
     */
    public function testGetNextSerialFunctionExists(): void
    {
        $this->assertTrue(function_exists('get_next_serial'));
    }

    /**
     * Test that set_soa_serial function is defined.
     */
    public function testSetSoaSerialFunctionExists(): void
    {
        $this->assertTrue(function_exists('set_soa_serial'));
    }

    /**
     * Test that update_soa_record function is defined.
     */
    public function testUpdateSoaRecordFunctionExists(): void
    {
        $this->assertTrue(function_exists('update_soa_record'));
    }

    /**
     * Test that update_soa_serial function is defined.
     */
    public function testUpdateSoaSerialFunctionExists(): void
    {
        $this->assertTrue(function_exists('update_soa_serial'));
    }

    /**
     * Test that validate_input function is defined.
     */
    public function testValidateInputFunctionExists(): void
    {
        $this->assertTrue(function_exists('validate_input'));
    }

    /**
     * Test that is_valid_hostname_fqdn function is defined.
     */
    public function testIsValidHostnameFqdnFunctionExists(): void
    {
        $this->assertTrue(function_exists('is_valid_hostname_fqdn'));
    }

    /**
     * Test that is_valid_ipv4 function is defined.
     */
    public function testIsValidIpv4FunctionExists(): void
    {
        $this->assertTrue(function_exists('is_valid_ipv4'));
    }

    /**
     * Test that is_valid_ipv6 function is defined.
     */
    public function testIsValidIpv6FunctionExists(): void
    {
        $this->assertTrue(function_exists('is_valid_ipv6'));
    }

    /**
     * Test that are_multipe_valid_ips function is defined.
     */
    public function testAreMultipleValidIpsFunctionExists(): void
    {
        $this->assertTrue(function_exists('are_multipe_valid_ips'));
    }

    /**
     * Test that is_valid_printable function is defined.
     */
    public function testIsValidPrintableFunctionExists(): void
    {
        $this->assertTrue(function_exists('is_valid_printable'));
    }

    /**
     * Test that is_valid_rr_cname_name function is defined.
     */
    public function testIsValidRrCnameNameFunctionExists(): void
    {
        $this->assertTrue(function_exists('is_valid_rr_cname_name'));
    }

    /**
     * Test that is_valid_rr_cname_exists function is defined.
     */
    public function testIsValidRrCnameExistsFunctionExists(): void
    {
        $this->assertTrue(function_exists('is_valid_rr_cname_exists'));
    }

    /**
     * Test that is_valid_rr_cname_unique function is defined.
     */
    public function testIsValidRrCnameUniqueFunctionExists(): void
    {
        $this->assertTrue(function_exists('is_valid_rr_cname_unique'));
    }

    /**
     * Test that is_not_empty_cname_rr function is defined.
     */
    public function testIsNotEmptyCnameRrFunctionExists(): void
    {
        $this->assertTrue(function_exists('is_not_empty_cname_rr'));
    }

    /**
     * Test that is_valid_non_alias_target function is defined.
     */
    public function testIsValidNonAliasTargetFunctionExists(): void
    {
        $this->assertTrue(function_exists('is_valid_non_alias_target'));
    }

    /**
     * Test that is_valid_rr_hinfo_content function is defined.
     */
    public function testIsValidRrHinfoContentFunctionExists(): void
    {
        $this->assertTrue(function_exists('is_valid_rr_hinfo_content'));
    }

    /**
     * Test that is_valid_rr_soa_content function is defined.
     */
    public function testIsValidRrSoaContentFunctionExists(): void
    {
        $this->assertTrue(function_exists('is_valid_rr_soa_content'));
    }

    /**
     * Test that is_valid_rr_soa_name function is defined.
     */
    public function testIsValidRrSoaNameFunctionExists(): void
    {
        $this->assertTrue(function_exists('is_valid_rr_soa_name'));
    }

    /**
     * Test that is_valid_rr_prio function is defined.
     */
    public function testIsValidRrPrioFunctionExists(): void
    {
        $this->assertTrue(function_exists('is_valid_rr_prio'));
    }

    /**
     * Test that is_valid_rr_srv_name function is defined.
     */
    public function testIsValidRrSrvNameFunctionExists(): void
    {
        $this->assertTrue(function_exists('is_valid_rr_srv_name'));
    }

    /**
     * Test that is_valid_rr_srv_content function is defined.
     */
    public function testIsValidRrSrvContentFunctionExists(): void
    {
        $this->assertTrue(function_exists('is_valid_rr_srv_content'));
    }

    /**
     * Test that is_valid_rr_ttl function is defined.
     */
    public function testIsValidRrTtlFunctionExists(): void
    {
        $this->assertTrue(function_exists('is_valid_rr_ttl'));
    }

    /**
     * Test that is_valid_search function is defined.
     */
    public function testIsValidSearchFunctionExists(): void
    {
        $this->assertTrue(function_exists('is_valid_search'));
    }

    /**
     * Test that is_valid_spf function is defined.
     */
    public function testIsValidSpfFunctionExists(): void
    {
        $this->assertTrue(function_exists('is_valid_spf'));
    }

    /**
     * Test that is_valid_loc function is defined.
     */
    public function testIsValidLocFunctionExists(): void
    {
        $this->assertTrue(function_exists('is_valid_loc'));
    }

    // ---------------------------------------------------------------
    // Parameter count / signature checks for pdns.functions.inc.php
    // ---------------------------------------------------------------

    /**
     * Test validate_input function signature.
     */
    public function testValidateInputSignature(): void
    {
        $ref = new \ReflectionFunction('validate_input');
        $this->assertSame(7, $ref->getNumberOfRequiredParameters());
        $this->assertSame(8, $ref->getNumberOfParameters());
    }

    /**
     * Test is_valid_hostname_fqdn function signature.
     */
    public function testIsValidHostnameFqdnSignature(): void
    {
        $ref = new \ReflectionFunction('is_valid_hostname_fqdn');
        $this->assertSame(2, $ref->getNumberOfRequiredParameters());
        // hostname is passed by reference
        $this->assertTrue($ref->getParameters()[0]->isPassedByReference());
    }

    /**
     * Test is_valid_rr_prio function signature.
     */
    public function testIsValidRrPrioSignature(): void
    {
        $ref = new \ReflectionFunction('is_valid_rr_prio');
        $this->assertSame(2, $ref->getNumberOfRequiredParameters());
        // prio is passed by reference
        $this->assertTrue($ref->getParameters()[0]->isPassedByReference());
    }

    /**
     * Test is_valid_rr_ttl function signature.
     */
    public function testIsValidRrTtlSignature(): void
    {
        $ref = new \ReflectionFunction('is_valid_rr_ttl');
        $this->assertSame(1, $ref->getNumberOfRequiredParameters());
        // ttl is passed by reference
        $this->assertTrue($ref->getParameters()[0]->isPassedByReference());
    }

    /**
     * Test endsWith function signature.
     */
    public function testEndsWithSignature(): void
    {
        $ref = new \ReflectionFunction('endsWith');
        $this->assertSame(2, $ref->getNumberOfRequiredParameters());
        $this->assertSame(2, $ref->getNumberOfParameters());
    }

    /**
     * Test is_valid_email function signature.
     */
    public function testIsValidEmailSignature(): void
    {
        $ref = new \ReflectionFunction('is_valid_email');
        $this->assertSame(1, $ref->getNumberOfRequiredParameters());
    }

    /**
     * Test get_soa_serial function signature.
     */
    public function testGetSoaSerialSignature(): void
    {
        $ref = new \ReflectionFunction('get_soa_serial');
        $this->assertSame(1, $ref->getNumberOfRequiredParameters());
    }

    /**
     * Test get_next_date function signature.
     */
    public function testGetNextDateSignature(): void
    {
        $ref = new \ReflectionFunction('get_next_date');
        $this->assertSame(1, $ref->getNumberOfRequiredParameters());
    }

    /**
     * Test get_next_serial function signature.
     */
    public function testGetNextSerialSignature(): void
    {
        $ref = new \ReflectionFunction('get_next_serial');
        $this->assertSame(1, $ref->getNumberOfRequiredParameters());
        $this->assertSame(2, $ref->getNumberOfParameters());
    }

    /**
     * Test set_soa_serial function signature.
     */
    public function testSetSoaSerialSignature(): void
    {
        $ref = new \ReflectionFunction('set_soa_serial');
        $this->assertSame(2, $ref->getNumberOfRequiredParameters());
    }

    /**
     * Test is_valid_rr_srv_name function signature.
     */
    public function testIsValidRrSrvNameSignature(): void
    {
        $ref = new \ReflectionFunction('is_valid_rr_srv_name');
        $this->assertSame(1, $ref->getNumberOfRequiredParameters());
        // name is passed by reference
        $this->assertTrue($ref->getParameters()[0]->isPassedByReference());
    }

    /**
     * Test is_valid_rr_srv_content function signature.
     */
    public function testIsValidRrSrvContentSignature(): void
    {
        $ref = new \ReflectionFunction('is_valid_rr_srv_content');
        $this->assertSame(1, $ref->getNumberOfRequiredParameters());
        // content is passed by reference
        $this->assertTrue($ref->getParameters()[0]->isPassedByReference());
    }

    // ---------------------------------------------------------------
    // Global variables checks
    // ---------------------------------------------------------------

    /**
     * Test that $rtypes global is defined and is an array.
     */
    public function testRtypesGlobalExists(): void
    {
        global $rtypes;
        $this->assertIsArray($rtypes);
        $this->assertNotEmpty($rtypes);
        $this->assertContains('A', $rtypes);
        $this->assertContains('AAAA', $rtypes);
        $this->assertContains('SOA', $rtypes);
    }

    /**
     * Test that $server_types global is defined and contains expected values.
     */
    public function testServerTypesGlobalExists(): void
    {
        global $server_types;
        $this->assertIsArray($server_types);
        $this->assertContains('MASTER', $server_types);
        $this->assertContains('SLAVE', $server_types);
        $this->assertContains('NATIVE', $server_types);
    }

    /**
     * Test that $valid_tlds global is defined and is a non-empty array.
     */
    public function testValidTldsGlobalExists(): void
    {
        global $valid_tlds;
        $this->assertIsArray($valid_tlds);
        $this->assertNotEmpty($valid_tlds);
        $this->assertContains('com', $valid_tlds);
        $this->assertContains('net', $valid_tlds);
        $this->assertContains('org', $valid_tlds);
    }

    /**
     * Test that dns.functions.inc.php source file exists for reference.
     */
    public function testDnsFunctionsSourceFileExists(): void
    {
        $this->assertFileExists(__DIR__ . '/../src/dns.functions.inc.php');
    }

    /**
     * Test that dns.functions.inc.php contains expected function declarations.
     */
    public function testDnsFunctionsFileContainsExpectedFunctions(): void
    {
        $content = file_get_contents(__DIR__ . '/../src/dns.functions.inc.php');
        $this->assertStringContainsString('function get_hostname(', $content);
        $this->assertStringContainsString('function get_dns_domain(', $content);
        $this->assertStringContainsString('function get_dns_records(', $content);
        $this->assertStringContainsString('function delete_dns_record(', $content);
        $this->assertStringContainsString('function add_dns_record(', $content);
        $this->assertStringContainsString('function update_dns_record(', $content);
        $this->assertStringContainsString('function delete_dns_domain(', $content);
        $this->assertStringContainsString('function add_dns_domain(', $content);
        $this->assertStringContainsString('function reverse_dns(', $content);
    }

    /**
     * Test that dns.functions.inc.php defines MAX_DNS_DOMAINS constant.
     */
    public function testDnsFunctionsFileDefinesMaxDnsDomainsConstant(): void
    {
        $content = file_get_contents(__DIR__ . '/../src/dns.functions.inc.php');
        $this->assertStringContainsString("define('MAX_DNS_DOMAINS', 500)", $content);
    }
}
