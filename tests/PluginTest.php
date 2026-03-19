<?php

declare(strict_types=1);

namespace Detain\MyAdminPowerDns\Tests;

use Detain\MyAdminPowerDns\Plugin;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Tests for the Plugin class.
 *
 * Validates class structure, static properties, hook registration,
 * and event handler method signatures.
 */
class PluginTest extends TestCase
{
    /**
     * @var ReflectionClass<Plugin>
     */
    private ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->reflection = new ReflectionClass(Plugin::class);
    }

    /**
     * Test that the Plugin class exists and is instantiable.
     */
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(Plugin::class));
    }

    /**
     * Test that the Plugin class resides in the correct namespace.
     */
    public function testClassNamespace(): void
    {
        $this->assertSame('Detain\\MyAdminPowerDns', $this->reflection->getNamespaceName());
    }

    /**
     * Test that the constructor is public and takes no required parameters.
     */
    public function testConstructorIsPublicAndParameterless(): void
    {
        $constructor = $this->reflection->getConstructor();
        $this->assertNotNull($constructor);
        $this->assertTrue($constructor->isPublic());
        $this->assertSame(0, $constructor->getNumberOfRequiredParameters());
    }

    /**
     * Test that the Plugin can be instantiated without errors.
     */
    public function testCanBeInstantiated(): void
    {
        $plugin = new Plugin();
        $this->assertInstanceOf(Plugin::class, $plugin);
    }

    /**
     * Test that the static $name property exists and has expected value.
     */
    public function testStaticNameProperty(): void
    {
        $this->assertTrue($this->reflection->hasProperty('name'));
        $prop = $this->reflection->getProperty('name');
        $this->assertTrue($prop->isStatic());
        $this->assertTrue($prop->isPublic());
        $this->assertSame('PowerDNS Plugin', Plugin::$name);
    }

    /**
     * Test that the static $description property exists and is a non-empty string.
     */
    public function testStaticDescriptionProperty(): void
    {
        $this->assertTrue($this->reflection->hasProperty('description'));
        $prop = $this->reflection->getProperty('description');
        $this->assertTrue($prop->isStatic());
        $this->assertTrue($prop->isPublic());
        $this->assertIsString(Plugin::$description);
        $this->assertNotEmpty(Plugin::$description);
    }

    /**
     * Test that the static $help property exists.
     */
    public function testStaticHelpProperty(): void
    {
        $this->assertTrue($this->reflection->hasProperty('help'));
        $prop = $this->reflection->getProperty('help');
        $this->assertTrue($prop->isStatic());
        $this->assertTrue($prop->isPublic());
        $this->assertIsString(Plugin::$help);
    }

    /**
     * Test that the static $type property exists and equals 'plugin'.
     */
    public function testStaticTypeProperty(): void
    {
        $this->assertTrue($this->reflection->hasProperty('type'));
        $this->assertSame('plugin', Plugin::$type);
    }

    /**
     * Test that getHooks returns an array with expected event keys.
     */
    public function testGetHooksReturnsArray(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertIsArray($hooks);
        $this->assertNotEmpty($hooks);
    }

    /**
     * Test that getHooks contains the api.register hook.
     */
    public function testGetHooksContainsApiRegister(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertArrayHasKey('api.register', $hooks);
        $this->assertSame([Plugin::class, 'apiRegister'], $hooks['api.register']);
    }

    /**
     * Test that getHooks contains the function.requirements hook.
     */
    public function testGetHooksContainsFunctionRequirements(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertArrayHasKey('function.requirements', $hooks);
        $this->assertSame([Plugin::class, 'getRequirements'], $hooks['function.requirements']);
    }

    /**
     * Test that getHooks contains the system.settings hook.
     */
    public function testGetHooksContainsSystemSettings(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertArrayHasKey('system.settings', $hooks);
        $this->assertSame([Plugin::class, 'getSettings'], $hooks['system.settings']);
    }

    /**
     * Test that all hook callbacks reference valid static methods on Plugin.
     */
    public function testAllHookCallbacksAreCallable(): void
    {
        $hooks = Plugin::getHooks();
        foreach ($hooks as $eventName => $callback) {
            $this->assertIsArray($callback, "Hook '$eventName' callback should be an array");
            $this->assertCount(2, $callback, "Hook '$eventName' callback should have class and method");
            $this->assertSame(Plugin::class, $callback[0]);
            $this->assertTrue(
                $this->reflection->hasMethod($callback[1]),
                "Method '{$callback[1]}' should exist on Plugin class"
            );
            $method = $this->reflection->getMethod($callback[1]);
            $this->assertTrue($method->isStatic(), "Method '{$callback[1]}' should be static");
            $this->assertTrue($method->isPublic(), "Method '{$callback[1]}' should be public");
        }
    }

    /**
     * Test that getHooks is a static method.
     */
    public function testGetHooksIsStatic(): void
    {
        $method = $this->reflection->getMethod('getHooks');
        $this->assertTrue($method->isStatic());
        $this->assertTrue($method->isPublic());
    }

    /**
     * Test that apiRegister accepts a GenericEvent parameter.
     */
    public function testApiRegisterMethodSignature(): void
    {
        $method = $this->reflection->getMethod('apiRegister');
        $this->assertTrue($method->isStatic());
        $this->assertTrue($method->isPublic());
        $params = $method->getParameters();
        $this->assertCount(1, $params);
        $this->assertSame('event', $params[0]->getName());
        $type = $params[0]->getType();
        $this->assertNotNull($type);
        $this->assertSame(GenericEvent::class, $type->getName());
    }

    /**
     * Test that getRequirements accepts a GenericEvent parameter.
     */
    public function testGetRequirementsMethodSignature(): void
    {
        $method = $this->reflection->getMethod('getRequirements');
        $params = $method->getParameters();
        $this->assertCount(1, $params);
        $type = $params[0]->getType();
        $this->assertNotNull($type);
        $this->assertSame(GenericEvent::class, $type->getName());
    }

    /**
     * Test that getSettings accepts a GenericEvent parameter.
     */
    public function testGetSettingsMethodSignature(): void
    {
        $method = $this->reflection->getMethod('getSettings');
        $params = $method->getParameters();
        $this->assertCount(1, $params);
        $type = $params[0]->getType();
        $this->assertNotNull($type);
        $this->assertSame(GenericEvent::class, $type->getName());
    }

    /**
     * Test that getMenu method exists and accepts a GenericEvent parameter.
     */
    public function testGetMenuMethodSignature(): void
    {
        $this->assertTrue($this->reflection->hasMethod('getMenu'));
        $method = $this->reflection->getMethod('getMenu');
        $this->assertTrue($method->isStatic());
        $params = $method->getParameters();
        $this->assertCount(1, $params);
        $type = $params[0]->getType();
        $this->assertNotNull($type);
        $this->assertSame(GenericEvent::class, $type->getName());
    }

    /**
     * Test that the class has exactly the expected set of public static properties.
     */
    public function testExpectedStaticProperties(): void
    {
        $expected = ['name', 'description', 'help', 'type'];
        $staticProps = [];
        foreach ($this->reflection->getProperties(\ReflectionProperty::IS_STATIC) as $prop) {
            $staticProps[] = $prop->getName();
        }
        sort($expected);
        sort($staticProps);
        $this->assertSame($expected, $staticProps);
    }
}
