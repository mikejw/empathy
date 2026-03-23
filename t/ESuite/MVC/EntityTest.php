<?php

declare(strict_types=1);

namespace ESuite\MVC;

use Empathy\MVC\EntityManager;
use Empathy\MVC\EntityPopulator;
use Empathy\MVC\Util\Testing\ESuiteTestCase;
use Empathy\MVC\Util\Testing\Util\Config;
use Empathy\MVC\Util\Testing\Util\DB;
use Empathy\MVC\Util\Testing\Util\FakeEntity;
use Nelmio\Alice\Fixtures;
use Nelmio\Alice\Fixtures\Loader;

// also testing new EntityManager class


class EntityTest extends ESuiteTestCase
{
    protected function setUp(): void
    {
        DB::loadDefDBCreds();
    }


    private function loadFixtures(string $reset, string $file): void
    {
        $populator = new EntityPopulator();
        DB::reset($reset);
        $objectManager = new EntityManager();

        $file = Config::get('util_dir') . '/' . $file;
        $loader = new Loader();
        $loader->addPopulator($populator);

        /** @var list<object> $objects */
        $objects = array_values($loader->load($file));
        $objectManager->persist($objects);
    }


    /*
     * Commented out - no tests to run
     public function testAlice()
     {
         $this->loadFixtures('fixtures/dd.sql', '/fixtures/fixtures1.yml');
         $this->loadFixtures('fixtures/dd2.sql', '/fixtures/fixtures2.yml');
         $this->loadFixtures('fixtures/dd3.sql', '/fixtures/fixtures3.yml');
     }
     */

    public function testFindBadClass(): void
    {
        $this->loadFixtures('fixtures/dd.sql', 'fixtures/fixtures1.yml');


        $objectManager = new EntityManager();
        $this->expectException(\Exception::class);
        $objectManager->find('Esuite\FakeEntityz', 1);
    }

    public function testFind(): void
    {
        $this->loadFixtures('fixtures/dd.sql', 'fixtures/fixtures1.yml');
        $objectManager = new EntityManager();
        $fake = $objectManager->find(FakeEntity::class, 1);
        $this->assertInstanceOf(FakeEntity::class, $fake);
        $this->assertEquals($fake->foo, 'bar');
    }


    /* Actual entity tests */


    public function testSave(): void
    {
        $this->loadFixtures('fixtures/dd.sql', 'fixtures/fixtures1.yml');
        $objectManager = new EntityManager();
        $fake = $objectManager->find(FakeEntity::class, 1);
        $this->assertInstanceOf(FakeEntity::class, $fake);
        $fake->foo = 'new';
        $fake->save();
        $fake = $objectManager->find(FakeEntity::class, 1);
        $this->assertInstanceOf(FakeEntity::class, $fake);
        $this->assertEquals($fake->foo, 'new');
    }

    public function testGetAll(): void
    {
        $this->loadFixtures('fixtures/dd.sql', 'fixtures/fixtures1.yml');
        $objectManager = new EntityManager();
        $fake = $objectManager->find(FakeEntity::class, 1);
        $this->assertInstanceOf(FakeEntity::class, $fake);
        $this->assertEquals(10, count($fake->getAll()));
    }

    public function testGetAllCustom(): void
    {
        $this->loadFixtures('fixtures/dd.sql', 'fixtures/fixtures1.yml');
        $objectManager = new EntityManager();
        $fake = $objectManager->find(FakeEntity::class, 1);
        $this->assertInstanceOf(FakeEntity::class, $fake);
        $this->assertEquals(10, count($fake->getAllCustom(' where foo like \'bar\'')));
    }
}
