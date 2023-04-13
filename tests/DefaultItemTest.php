<?php

namespace App\Tests;

use App\Service\DefaultItemService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DefaultItemTest extends KernelTestCase
{
    public function testSomething(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
        // $routerService = static::getContainer()->get('router');
        $defaultItemService = static::getContainer()->get(DefaultItemService::class);

        $this->assertTrue(true);
    }
}
