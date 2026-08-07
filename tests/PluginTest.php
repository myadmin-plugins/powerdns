<?php

declare(strict_types=1);

namespace Detain\MyAdminPowerDns\Tests;

use Detain\MyAdminPowerDns\Plugin;
use MyAdmin\Plugins\Testing\PluginContractTestCase;

/** Contract assertions for the PowerDNS plugin. */
class PluginTest extends PluginContractTestCase
{
    use DeferredPhase5Defects;

    /** @return string */
    protected function pluginClass()
    {
        return Plugin::class;
    }
}
