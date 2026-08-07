<?php

declare(strict_types=1);

namespace Detain\MyAdminPowerDns\Tests;

use Detain\MyAdminPowerDns\Plugin;
use MyAdmin\Plugins\Testing\PluginContractTestCase;

/** Contract assertions for the PowerDNS plugin. */
class PluginTest extends PluginContractTestCase
{
    /** @return string */
    protected function pluginClass()
    {
        return Plugin::class;
    }

    /**
     * Pins this plugin's identity and its hook registrations.
     *
     * The shared harness deliberately cannot do this: every catalogue assertion is
     * conditional on the registration existing, so emptying getHooks() leaves the suite
     * byte-identical -- which would silently unregister every DNS page requirement and
     * the whole api.register surface. $type is read by the harness but never pinned.
     *
     * These lines are the per-repo half of the contract. Keep them.
     *
     * @return void
     */
    public function testRegistersItsIdentityAndHooks(): void
    {
        $this->assertSame('plugin', Plugin::$type, 'changing $type silently changes which assertions apply');
        foreach (['api.register', 'function.requirements'] as $hook) {
            $this->assertArrayHasKey($hook, Plugin::getHooks(), $hook.' is no longer registered');
            $this->assertIsCallable(Plugin::getHooks()[$hook], $hook.' resolves to nothing callable');
        }
        $this->assertArrayNotHasKey(
            'system.settings',
            Plugin::getHooks(),
            'getSettings() was removed; re-registering the hook without the method fatals every'
            .' page that dispatches system.settings, because include/tf.php only guards existence'
            .' of the class'
        );
    }
}
