<?php

declare(strict_types=1);

namespace Detain\MyAdminPowerDns\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests for pure functions in pdns.functions.inc.php.
 *
 * These tests cover functions that have no database or global state
 * dependencies and can be tested in isolation.
 */
class PdnsPureFunctionsTest extends TestCase
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
    // endsWith()
    // ---------------------------------------------------------------

    /**
     * Test endsWith returns true when haystack ends with needle.
     */
    public function testEndsWithMatchingSubstring(): void
    {
        $this->assertTrue(endsWith('world', 'hello world'));
    }

    /**
     * Test endsWith returns false when haystack does not end with needle.
     */
    public function testEndsWithNonMatchingSubstring(): void
    {
        $this->assertFalse(endsWith('hello', 'hello world'));
    }

    /**
     * Test endsWith with identical strings returns true.
     */
    public function testEndsWithIdenticalStrings(): void
    {
        $this->assertTrue(endsWith('test', 'test'));
    }

    /**
     * Test endsWith returns true when needle is empty.
     */
    public function testEndsWithEmptyNeedle(): void
    {
        $this->assertTrue(endsWith('', 'anything'));
    }

    /**
     * Test endsWith returns false when needle is longer than haystack.
     */
    public function testEndsWithNeedleLongerThanHaystack(): void
    {
        $this->assertFalse(endsWith('longerneedle', 'short'));
    }

    /**
     * Test endsWith with domain name scenario.
     */
    public function testEndsWithDomainName(): void
    {
        $this->assertTrue(endsWith('example.com', 'sub.example.com'));
    }

    // ---------------------------------------------------------------
    // get_soa_serial()
    // ---------------------------------------------------------------

    /**
     * Test get_soa_serial extracts serial from SOA record.
     */
    public function testGetSoaSerialExtractsThirdField(): void
    {
        $soa = 'ns1.example.com. admin.example.com. 2025010100 10800 3600 604800 3600';
        $this->assertSame('2025010100', get_soa_serial($soa));
    }

    /**
     * Test get_soa_serial with a minimal SOA record.
     */
    public function testGetSoaSerialMinimalRecord(): void
    {
        $soa = 'ns1.example.com. admin.example.com. 1234567890 10800 3600 604800 3600';
        $this->assertSame('1234567890', get_soa_serial($soa));
    }

    // ---------------------------------------------------------------
    // get_next_date()
    // ---------------------------------------------------------------

    /**
     * Test get_next_date returns the next day.
     */
    public function testGetNextDateReturnsNextDay(): void
    {
        $this->assertSame('20250102', get_next_date('20250101'));
    }

    /**
     * Test get_next_date handles month boundary.
     */
    public function testGetNextDateMonthBoundary(): void
    {
        $this->assertSame('20250201', get_next_date('20250131'));
    }

    /**
     * Test get_next_date handles year boundary.
     */
    public function testGetNextDateYearBoundary(): void
    {
        $this->assertSame('20260101', get_next_date('20251231'));
    }

    /**
     * Test get_next_date handles leap year.
     */
    public function testGetNextDateLeapYear(): void
    {
        $this->assertSame('20240229', get_next_date('20240228'));
    }

    // ---------------------------------------------------------------
    // get_next_serial()
    // ---------------------------------------------------------------

    /**
     * Test get_next_serial returns 0 when current serial is 0.
     */
    public function testGetNextSerialZeroReturnsZero(): void
    {
        $this->assertSame(0, get_next_serial(0));
    }

    /**
     * Test get_next_serial increments non-date serial.
     */
    public function testGetNextSerialIncrementsSmallSerial(): void
    {
        $this->assertSame(43, get_next_serial(42));
    }

    /**
     * Test get_next_serial resets at 1979999999 boundary.
     */
    public function testGetNextSerialResetsAtBoundary(): void
    {
        $this->assertSame(1, get_next_serial(1979999999));
    }

    /**
     * Test get_next_serial increments date-based serial revision on same day.
     */
    public function testGetNextSerialIncrementsSameDayRevision(): void
    {
        $today = '20250315';
        $result = get_next_serial($today . '05', $today);
        $this->assertSame($today . '06', $result);
    }

    /**
     * Test get_next_serial resets revision for new day.
     */
    public function testGetNextSerialResetsRevisionOnNewDay(): void
    {
        $result = get_next_serial('2025031405', '20250315');
        $this->assertSame('2025031500', $result);
    }

    /**
     * Test get_next_serial handles max daily revision (99).
     */
    public function testGetNextSerialMaxDailyRevision(): void
    {
        $today = '20250315';
        $result = get_next_serial($today . '99', $today);
        $this->assertSame('2025031600', $result);
    }

    /**
     * Test get_next_serial handles future serial date.
     */
    public function testGetNextSerialFutureDate(): void
    {
        $result = get_next_serial('2030010105', '20250315');
        $this->assertSame('2030010106', $result);
    }

    // ---------------------------------------------------------------
    // set_soa_serial()
    // ---------------------------------------------------------------

    /**
     * Test set_soa_serial replaces the serial in an SOA record.
     */
    public function testSetSoaSerialReplacesSerial(): void
    {
        $soa = 'ns1.example.com. admin.example.com. 2025010100 10800 3600 604800 3600';
        $result = set_soa_serial($soa, '2025031500');
        $this->assertSame('ns1.example.com. admin.example.com. 2025031500 10800 3600 604800 3600', $result);
    }

    /**
     * Test set_soa_serial preserves all other SOA fields.
     */
    public function testSetSoaSerialPreservesOtherFields(): void
    {
        $soa = 'ns1.test.com. admin.test.com. 1111111111 7200 1800 302400 1800';
        $result = set_soa_serial($soa, '9999999999');
        $parts = explode(' ', $result);
        $this->assertSame('ns1.test.com.', $parts[0]);
        $this->assertSame('admin.test.com.', $parts[1]);
        $this->assertSame('9999999999', $parts[2]);
        $this->assertSame('7200', $parts[3]);
        $this->assertSame('1800', $parts[4]);
        $this->assertSame('302400', $parts[5]);
        $this->assertSame('1800', $parts[6]);
    }

    // ---------------------------------------------------------------
    // is_valid_ipv4()
    // ---------------------------------------------------------------

    /**
     * Test is_valid_ipv4 with valid addresses.
     *
     * @dataProvider validIpv4Provider
     */
    public function testIsValidIpv4WithValidAddresses(string $ip): void
    {
        $this->assertTrue(is_valid_ipv4($ip));
    }

    /**
     * Provides valid IPv4 addresses.
     *
     * @return array<string, array{string}>
     */
    public function validIpv4Provider(): array
    {
        return [
            'loopback' => ['127.0.0.1'],
            'private' => ['192.168.1.1'],
            'zeros' => ['0.0.0.0'],
            'max' => ['255.255.255.255'],
            'public' => ['8.8.8.8'],
        ];
    }

    /**
     * Test is_valid_ipv4 with invalid addresses.
     *
     * @dataProvider invalidIpv4Provider
     */
    public function testIsValidIpv4WithInvalidAddresses(string $ip): void
    {
        $this->assertFalse(is_valid_ipv4($ip));
    }

    /**
     * Provides invalid IPv4 addresses.
     *
     * @return array<string, array{string}>
     */
    public function invalidIpv4Provider(): array
    {
        return [
            'too_many_octets' => ['1.2.3.4.5'],
            'out_of_range' => ['256.1.1.1'],
            'alpha' => ['abc.def.ghi.jkl'],
            'empty' => [''],
            'ipv6' => ['::1'],
            'partial' => ['192.168.1'],
        ];
    }

    // ---------------------------------------------------------------
    // is_valid_ipv6()
    // ---------------------------------------------------------------

    /**
     * Test is_valid_ipv6 with valid addresses.
     *
     * @dataProvider validIpv6Provider
     */
    public function testIsValidIpv6WithValidAddresses(string $ip): void
    {
        $this->assertTrue(is_valid_ipv6($ip));
    }

    /**
     * Provides valid IPv6 addresses.
     *
     * @return array<string, array{string}>
     */
    public function validIpv6Provider(): array
    {
        return [
            'loopback' => ['::1'],
            'full' => ['2001:0db8:85a3:0000:0000:8a2e:0370:7334'],
            'compressed' => ['2001:db8::1'],
            'all_zeros' => ['::'],
        ];
    }

    /**
     * Test is_valid_ipv6 with invalid addresses.
     *
     * @dataProvider invalidIpv6Provider
     */
    public function testIsValidIpv6WithInvalidAddresses(string $ip): void
    {
        $this->assertFalse(is_valid_ipv6($ip));
    }

    /**
     * Provides invalid IPv6 addresses.
     *
     * @return array<string, array{string}>
     */
    public function invalidIpv6Provider(): array
    {
        return [
            'ipv4' => ['192.168.1.1'],
            'garbage' => ['not-an-ip'],
            'empty' => [''],
            'too_many_groups' => ['2001:db8:85a3:0:0:8a2e:370:7334:extra'],
        ];
    }

    // ---------------------------------------------------------------
    // are_multipe_valid_ips()
    // ---------------------------------------------------------------

    /**
     * Test are_multipe_valid_ips with single valid IPv4.
     */
    public function testAreMultipleValidIpsSingleIpv4(): void
    {
        $this->assertTrue(are_multipe_valid_ips('192.168.1.1'));
    }

    /**
     * Test are_multipe_valid_ips with multiple valid IPv4 addresses.
     */
    public function testAreMultipleValidIpsMultipleIpv4(): void
    {
        $this->assertTrue(are_multipe_valid_ips('192.168.1.1, 10.0.0.1'));
    }

    /**
     * Test are_multipe_valid_ips with mixed IPv4 and IPv6.
     */
    public function testAreMultipleValidIpsMixed(): void
    {
        $this->assertTrue(are_multipe_valid_ips('192.168.1.1, 2001:db8::1'));
    }

    // ---------------------------------------------------------------
    // is_valid_printable()
    // ---------------------------------------------------------------

    /**
     * Test is_valid_printable with normal ASCII text.
     */
    public function testIsValidPrintableWithAsciiText(): void
    {
        $this->assertTrue(is_valid_printable('Hello World 123!'));
    }

    /**
     * Test is_valid_printable rejects control characters.
     */
    public function testIsValidPrintableRejectsControlChars(): void
    {
        $this->assertFalse(is_valid_printable("\x00\x01\x02"));
    }

    /**
     * Test is_valid_printable rejects empty string.
     */
    public function testIsValidPrintableRejectsEmptyString(): void
    {
        $this->assertFalse(is_valid_printable(''));
    }

    // ---------------------------------------------------------------
    // is_not_empty_cname_rr()
    // ---------------------------------------------------------------

    /**
     * Test is_not_empty_cname_rr returns false when name equals zone.
     */
    public function testIsNotEmptyCnameRrReturnsFalseWhenNameEqualsZone(): void
    {
        $this->assertFalse(is_not_empty_cname_rr('example.com', 'example.com'));
    }

    /**
     * Test is_not_empty_cname_rr returns true when name differs from zone.
     */
    public function testIsNotEmptyCnameRrReturnsTrueWhenNameDiffers(): void
    {
        $this->assertTrue(is_not_empty_cname_rr('www.example.com', 'example.com'));
    }

    // ---------------------------------------------------------------
    // is_valid_rr_soa_name()
    // ---------------------------------------------------------------

    /**
     * Test is_valid_rr_soa_name returns true when name matches zone.
     */
    public function testIsValidRrSoaNameMatchesZone(): void
    {
        $this->assertTrue(is_valid_rr_soa_name('example.com', 'example.com'));
    }

    /**
     * Test is_valid_rr_soa_name returns false when name does not match zone.
     */
    public function testIsValidRrSoaNameDoesNotMatchZone(): void
    {
        $this->assertFalse(is_valid_rr_soa_name('other.com', 'example.com'));
    }

    // ---------------------------------------------------------------
    // is_valid_rr_prio()
    // ---------------------------------------------------------------

    /**
     * Test is_valid_rr_prio accepts valid MX priority.
     */
    public function testIsValidRrPrioValidMx(): void
    {
        $prio = 10;
        $this->assertTrue(is_valid_rr_prio($prio, 'MX'));
        $this->assertSame(10, $prio);
    }

    /**
     * Test is_valid_rr_prio accepts valid SRV priority.
     */
    public function testIsValidRrPrioValidSrv(): void
    {
        $prio = 0;
        $this->assertTrue(is_valid_rr_prio($prio, 'SRV'));
    }

    /**
     * Test is_valid_rr_prio rejects negative MX priority.
     */
    public function testIsValidRrPrioRejectsNegativeMx(): void
    {
        $prio = -1;
        $this->assertFalse(is_valid_rr_prio($prio, 'MX'));
    }

    /**
     * Test is_valid_rr_prio rejects MX priority exceeding 65535.
     */
    public function testIsValidRrPrioRejectsOverMaxMx(): void
    {
        $prio = 65536;
        $this->assertFalse(is_valid_rr_prio($prio, 'MX'));
    }

    /**
     * Test is_valid_rr_prio sets prio to 0 for non-MX/SRV types.
     */
    public function testIsValidRrPrioSetsZeroForNonPrioTypes(): void
    {
        $prio = 999;
        $this->assertTrue(is_valid_rr_prio($prio, 'A'));
        $this->assertSame(0, $prio);
    }

    /**
     * Test is_valid_rr_prio accepts max valid value.
     */
    public function testIsValidRrPrioAcceptsMaxValue(): void
    {
        $prio = 65535;
        $this->assertTrue(is_valid_rr_prio($prio, 'MX'));
    }

    // ---------------------------------------------------------------
    // is_valid_rr_ttl()
    // ---------------------------------------------------------------

    /**
     * Test is_valid_rr_ttl accepts valid TTL.
     */
    public function testIsValidRrTtlAcceptsValidValue(): void
    {
        $ttl = 86400;
        $this->assertTrue(is_valid_rr_ttl($ttl));
    }

    /**
     * Test is_valid_rr_ttl accepts zero.
     */
    public function testIsValidRrTtlAcceptsZero(): void
    {
        $ttl = 0;
        $this->assertTrue(is_valid_rr_ttl($ttl));
    }

    /**
     * Test is_valid_rr_ttl accepts maximum valid value.
     */
    public function testIsValidRrTtlAcceptsMaxValue(): void
    {
        $ttl = 2147483647;
        $this->assertTrue(is_valid_rr_ttl($ttl));
    }

    /**
     * Test is_valid_rr_ttl rejects negative value.
     */
    public function testIsValidRrTtlRejectsNegative(): void
    {
        $ttl = -1;
        $this->assertFalse(is_valid_rr_ttl($ttl));
    }

    /**
     * Test is_valid_rr_ttl rejects value exceeding maximum.
     */
    public function testIsValidRrTtlRejectsOverMax(): void
    {
        $ttl = 2147483648;
        $this->assertFalse(is_valid_rr_ttl($ttl));
    }

    /**
     * Test is_valid_rr_ttl rejects non-numeric string.
     */
    public function testIsValidRrTtlRejectsNonNumeric(): void
    {
        $ttl = 'abc';
        $this->assertFalse(is_valid_rr_ttl($ttl));
    }

    // ---------------------------------------------------------------
    // is_valid_search()
    // ---------------------------------------------------------------

    /**
     * Test is_valid_search with valid search strings.
     *
     * @dataProvider validSearchProvider
     */
    public function testIsValidSearchAcceptsValid(string $search): void
    {
        $this->assertSame(1, is_valid_search($search));
    }

    /**
     * Provides valid search strings.
     *
     * @return array<string, array{string}>
     */
    public function validSearchProvider(): array
    {
        return [
            'simple_domain' => ['example.com'],
            'with_wildcard' => ['%.example.com'],
            'alphanumeric' => ['test123'],
            'with_dash' => ['my-domain.com'],
            'with_underscore' => ['_srv.example.com'],
        ];
    }

    /**
     * Test is_valid_search rejects invalid search strings.
     *
     * @dataProvider invalidSearchProvider
     */
    public function testIsValidSearchRejectsInvalid(string $search): void
    {
        $this->assertSame(0, is_valid_search($search));
    }

    /**
     * Provides invalid search strings.
     *
     * @return array<string, array{string}>
     */
    public function invalidSearchProvider(): array
    {
        return [
            'spaces' => ['hello world'],
            'special_chars' => ['test@domain'],
            'semicolon' => ['test;drop'],
            'quotes' => ["test'quote"],
        ];
    }

    // ---------------------------------------------------------------
    // is_valid_rr_hinfo_content()
    // ---------------------------------------------------------------

    /**
     * Test is_valid_rr_hinfo_content with valid unquoted content.
     */
    public function testIsValidRrHinfoContentValidUnquoted(): void
    {
        $this->assertTrue(is_valid_rr_hinfo_content('INTEL-386 LINUX'));
    }

    /**
     * Test is_valid_rr_hinfo_content with valid quoted content.
     */
    public function testIsValidRrHinfoContentValidQuoted(): void
    {
        $this->assertTrue(is_valid_rr_hinfo_content('"Intel Pentium" "Linux 5.x"'));
    }

    // ---------------------------------------------------------------
    // get_record_types()
    // ---------------------------------------------------------------

    /**
     * Test get_record_types returns an array.
     */
    public function testGetRecordTypesReturnsArray(): void
    {
        $types = get_record_types();
        $this->assertIsArray($types);
        $this->assertNotEmpty($types);
    }

    /**
     * Test get_record_types includes common record types.
     */
    public function testGetRecordTypesIncludesCommonTypes(): void
    {
        $types = get_record_types();
        $this->assertContains('A', $types);
        $this->assertContains('AAAA', $types);
        $this->assertContains('CNAME', $types);
        $this->assertContains('MX', $types);
        $this->assertContains('NS', $types);
        $this->assertContains('TXT', $types);
        $this->assertContains('SOA', $types);
        $this->assertContains('SRV', $types);
        $this->assertContains('PTR', $types);
    }

    /**
     * Test get_record_types excludes SPF.
     */
    public function testGetRecordTypesExcludesSpf(): void
    {
        $types = get_record_types();
        $this->assertNotContains('SPF', $types);
    }

    /**
     * Test get_record_types returns sequentially indexed array.
     */
    public function testGetRecordTypesIsSequentiallyIndexed(): void
    {
        $types = get_record_types();
        $this->assertSame(array_values($types), $types);
    }

    // ---------------------------------------------------------------
    // is_valid_email()
    // ---------------------------------------------------------------

    /**
     * Test is_valid_email with valid email addresses.
     *
     * @dataProvider validEmailProvider
     */
    public function testIsValidEmailAcceptsValid(string $email): void
    {
        $this->assertTrue(is_valid_email($email));
    }

    /**
     * Provides valid email addresses.
     *
     * @return array<string, array{string}>
     */
    public function validEmailProvider(): array
    {
        return [
            'simple' => ['user@example.com'],
            'with_dots' => ['first.last@example.com'],
            'with_subdomain' => ['user@sub.example.com'],
        ];
    }

    /**
     * Test is_valid_email rejects invalid email addresses.
     *
     * @dataProvider invalidEmailProvider
     */
    public function testIsValidEmailRejectsInvalid(string $email): void
    {
        $this->assertFalse(is_valid_email($email));
    }

    /**
     * Provides invalid email addresses.
     *
     * @return array<string, array{string}>
     */
    public function invalidEmailProvider(): array
    {
        return [
            'no_at' => ['userexample.com'],
            'no_domain' => ['user@'],
            'no_local' => ['@example.com'],
            'empty' => [''],
        ];
    }

    // ---------------------------------------------------------------
    // is_valid_hostname_fqdn()
    // ---------------------------------------------------------------

    /**
     * Test is_valid_hostname_fqdn with valid hostnames.
     *
     * @dataProvider validHostnameProvider
     */
    public function testIsValidHostnameFqdnAcceptsValid(string $hostname, int $wildcard): void
    {
        $this->assertTrue(is_valid_hostname_fqdn($hostname, $wildcard));
    }

    /**
     * Provides valid hostnames.
     *
     * @return array<string, array{string, int}>
     */
    public function validHostnameProvider(): array
    {
        return [
            'simple_domain' => ['example.com', 0],
            'subdomain' => ['www.example.com', 0],
            'multi_level' => ['a.b.c.example.com', 0],
            'with_numbers' => ['server1.example.com', 0],
            'wildcard_allowed' => ['*.example.com', 1],
        ];
    }

    /**
     * Test is_valid_hostname_fqdn rejects hostnames starting with dash.
     */
    public function testIsValidHostnameFqdnRejectsDashStart(): void
    {
        $hostname = '-example.com';
        $this->assertFalse(is_valid_hostname_fqdn($hostname, 0));
    }

    /**
     * Test is_valid_hostname_fqdn rejects hostnames ending with dash.
     */
    public function testIsValidHostnameFqdnRejectsDashEnd(): void
    {
        $hostname = 'example-.com';
        $this->assertFalse(is_valid_hostname_fqdn($hostname, 0));
    }

    /**
     * Test is_valid_hostname_fqdn rejects hostnames exceeding max length.
     */
    public function testIsValidHostnameFqdnRejectsTooLongHostname(): void
    {
        $hostname = str_repeat('a', 254) . '.com';
        $this->assertFalse(is_valid_hostname_fqdn($hostname, 0));
    }

    /**
     * Test is_valid_hostname_fqdn rejects label exceeding 63 chars.
     */
    public function testIsValidHostnameFqdnRejectsTooLongLabel(): void
    {
        $hostname = str_repeat('a', 64) . '.com';
        $this->assertFalse(is_valid_hostname_fqdn($hostname, 0));
    }

    /**
     * Test is_valid_hostname_fqdn strips trailing dot.
     */
    public function testIsValidHostnameFqdnStripsTrailingDot(): void
    {
        $hostname = 'example.com.';
        $this->assertTrue(is_valid_hostname_fqdn($hostname, 0));
        $this->assertSame('example.com', $hostname);
    }

    // ---------------------------------------------------------------
    // is_valid_rr_srv_name()
    // ---------------------------------------------------------------

    /**
     * Test is_valid_rr_srv_name with valid SRV name.
     */
    public function testIsValidRrSrvNameValid(): void
    {
        $name = '_http._tcp.example.com';
        $this->assertTrue(is_valid_rr_srv_name($name));
    }

    /**
     * Test is_valid_rr_srv_name with valid SRV name using dashes.
     */
    public function testIsValidRrSrvNameWithDashes(): void
    {
        $name = '_my-service._tcp.example.com';
        $this->assertTrue(is_valid_rr_srv_name($name));
    }

    /**
     * Test is_valid_rr_srv_name rejects missing service underscore.
     */
    public function testIsValidRrSrvNameRejectsMissingServiceUnderscore(): void
    {
        $name = 'http._tcp.example.com';
        $this->assertFalse(is_valid_rr_srv_name($name));
    }

    /**
     * Test is_valid_rr_srv_name rejects missing protocol underscore.
     */
    public function testIsValidRrSrvNameRejectsMissingProtocolUnderscore(): void
    {
        $name = '_http.tcp.example.com';
        $this->assertFalse(is_valid_rr_srv_name($name));
    }

    /**
     * Test is_valid_rr_srv_name rejects name exceeding 255 chars.
     */
    public function testIsValidRrSrvNameRejectsTooLong(): void
    {
        $name = '_http._tcp.' . str_repeat('a', 250) . '.com';
        $this->assertFalse(is_valid_rr_srv_name($name));
    }

    // ---------------------------------------------------------------
    // is_valid_rr_srv_content()
    // ---------------------------------------------------------------

    /**
     * Test is_valid_rr_srv_content with valid content.
     */
    public function testIsValidRrSrvContentValid(): void
    {
        $content = '10 5 example.com';
        $this->assertTrue(is_valid_rr_srv_content($content));
    }

    /**
     * Test is_valid_rr_srv_content with dot target.
     */
    public function testIsValidRrSrvContentDotTarget(): void
    {
        $content = '0 0 .';
        $this->assertTrue(is_valid_rr_srv_content($content));
    }

    /**
     * Test is_valid_rr_srv_content rejects invalid priority.
     */
    public function testIsValidRrSrvContentRejectsInvalidPriority(): void
    {
        $content = '-1 5 example.com';
        $this->assertFalse(is_valid_rr_srv_content($content));
    }

    /**
     * Test is_valid_rr_srv_content rejects out-of-range weight.
     */
    public function testIsValidRrSrvContentRejectsInvalidWeight(): void
    {
        $content = '10 70000 example.com';
        $this->assertFalse(is_valid_rr_srv_content($content));
    }

    // ---------------------------------------------------------------
    // is_valid_loc()
    // ---------------------------------------------------------------

    /**
     * Test is_valid_loc with valid LOC content.
     */
    public function testIsValidLocValid(): void
    {
        $this->assertTrue(is_valid_loc('52 22 23.000 N 4 53 32.000 E -2.00m'));
    }

    /**
     * Test is_valid_loc rejects invalid content.
     */
    public function testIsValidLocRejectsInvalid(): void
    {
        $this->assertFalse(is_valid_loc('not a location'));
    }
}
