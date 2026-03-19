<?php

declare(strict_types=1);

namespace Detain\MyAdminPowerDns\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests that all expected source files exist in the package.
 *
 * Ensures the package structure is complete and no source files
 * are missing from the distribution.
 */
class FileExistenceTest extends TestCase
{
    /**
     * @var string
     */
    private string $srcDir;

    protected function setUp(): void
    {
        $this->srcDir = dirname(__DIR__) . '/src';
    }

    /**
     * Test that src/Plugin.php exists.
     */
    public function testPluginPhpExists(): void
    {
        $this->assertFileExists($this->srcDir . '/Plugin.php');
    }

    /**
     * Test that src/basic_dns_editor.php exists.
     */
    public function testBasicDnsEditorPhpExists(): void
    {
        $this->assertFileExists($this->srcDir . '/basic_dns_editor.php');
    }

    /**
     * Test that src/dns.functions.inc.php exists.
     */
    public function testDnsFunctionsIncPhpExists(): void
    {
        $this->assertFileExists($this->srcDir . '/dns.functions.inc.php');
    }

    /**
     * Test that src/dns_add.php exists.
     */
    public function testDnsAddPhpExists(): void
    {
        $this->assertFileExists($this->srcDir . '/dns_add.php');
    }

    /**
     * Test that src/dns_default_domains.php exists.
     */
    public function testDnsDefaultDomainsPhpExists(): void
    {
        $this->assertFileExists($this->srcDir . '/dns_default_domains.php');
    }

    /**
     * Test that src/dns_delete.php exists.
     */
    public function testDnsDeletePhpExists(): void
    {
        $this->assertFileExists($this->srcDir . '/dns_delete.php');
    }

    /**
     * Test that src/dns_editor.php exists.
     */
    public function testDnsEditorPhpExists(): void
    {
        $this->assertFileExists($this->srcDir . '/dns_editor.php');
    }

    /**
     * Test that src/dns_editor2.php exists.
     */
    public function testDnsEditor2PhpExists(): void
    {
        $this->assertFileExists($this->srcDir . '/dns_editor2.php');
    }

    /**
     * Test that src/dns_list.php exists.
     */
    public function testDnsListPhpExists(): void
    {
        $this->assertFileExists($this->srcDir . '/dns_list.php');
    }

    /**
     * Test that src/dns_manager.php exists.
     */
    public function testDnsManagerPhpExists(): void
    {
        $this->assertFileExists($this->srcDir . '/dns_manager.php');
    }

    /**
     * Test that src/dns_resolvers.php exists.
     */
    public function testDnsResolversPhpExists(): void
    {
        $this->assertFileExists($this->srcDir . '/dns_resolvers.php');
    }

    /**
     * Test that src/pdns.functions.inc.php exists.
     */
    public function testPdnsFunctionsIncPhpExists(): void
    {
        $this->assertFileExists($this->srcDir . '/pdns.functions.inc.php');
    }

    /**
     * Test that composer.json exists.
     */
    public function testComposerJsonExists(): void
    {
        $this->assertFileExists(dirname(__DIR__) . '/composer.json');
    }

    /**
     * Test that README.md exists.
     */
    public function testReadmeExists(): void
    {
        $this->assertFileExists(dirname(__DIR__) . '/README.md');
    }
}
