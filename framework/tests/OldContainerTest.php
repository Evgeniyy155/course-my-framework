<?php

namespace Web\Framework\Tests;

use PHPUnit\Framework\TestCase;
use Web\Framework\OldSelfContainer\Exceptions\ContainerException;
use Web\Framework\OldSelfContainer\OldContainer;

class OldContainerTest extends TestCase
{
    public function test_getting_service_from_container()
    {
        $container = new OldContainer();
        $container->add('somecode-class', SomecodeClass::class);
        $this->assertInstanceOf(SomecodeClass::class, $container->get('somecode-class'));
    }

    public function test_container_has_exception_ContainerException_if_add_wrong_exception()
    {
        $container = new OldContainer();
        $this->expectException(ContainerException::class);
        $container->add('no-class');

    }

    public function test_has_method()
    {
        $container = new OldContainer();
        $container->add('somecode-class',SomecodeClass::class);
        $this->assertTrue($container->has('somecode-class'));
        $this->assertFalse($container->has('no-class'));

    }

    public function test_()
    {
        $container = new OldContainer();
        $container->add('somecode-class',SomecodeClass::class);
        /** @var SomecodeClass $somecode */
        $somecode = $container->get('somecode-class');
        $this->assertInstanceOf(WebClass::class, $somecode->getWebClass());

    }
}