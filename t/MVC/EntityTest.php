<?php

declare(strict_types=1);

namespace ESuite\MVC;

use Empathy\MVC\EntityManager;
use Empathy\MVC\EntityPopulator;
use Nelmio\Alice\Fixtures;
use Nelmio\Alice\Fixtures\Loader;
use Empathy\MVC\Util\Testing\ESuiteTestCase;
use Empathy\MVC\Util\Testing\Util\DB;
use Empathy\MVC\Util\Testing\Util\Config;
use Empathy\MVC\Util\Testing\Util\FakeEntity;

// also testing new EntityManager class


class EntityTest extends ESuiteTestCase
{
    protected function setUp(): void
    {
        DB::loadDefDBCreds();
    }


    private function loadFixtures($reset, $file)
    {
        $populator = new EntityPopulator();
        DB::reset($reset);
        $objectManager = new EntityManager();

        $file = Config::get('util_dir') . '/' . $file;
        $loader = new Loader();
        $loader->addPopulator($populator);

        $objects = $loader->load($file);
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

    public function testFindBadClass()
    {
        $this->loadFixtures('fixtures/dd.sql', 'fixtures/fixtures1.yml');


        $objectManager = new EntityManager();
        $this->expectException(\Exception::class);
        $objectManager->find('Esuite\FakeEntityz', 1);
    }

    public function testFind()
    {
        $this->loadFixtures('fixtures/dd.sql', 'fixtures/fixtures1.yml');
        $objectManager = new EntityManager();
        $fake = $objectManager->find(FakeEntity::class, 1);
        $this->assertEquals($fake->foo, 'bar');
    }


    /* Actual entity tests */


    public function testSave()
    {
        $this->loadFixtures('fixtures/dd.sql', 'fixtures/fixtures1.yml');
        $objectManager = new EntityManager();
        $fake = $objectManager->find(FakeEntity::class, 1);
        $fake->foo = 'new';
        $fake->save();
        $fake = $objectManager->find(FakeEntity::class, 1);
        $this->assertEquals($fake->foo, 'new');
    }

    public function testGetAll()
    {
        $this->loadFixtures('fixtures/dd.sql', 'fixtures/fixtures1.yml');
        $objectManager = new EntityManager();
        $fake = $objectManager->find(FakeEntity::class, 1);
        $this->assertEquals(10, count($fake->getAll()));
    }

    public function testGetAllCustom()
    {
        $this->loadFixtures('fixtures/dd.sql', 'fixtures/fixtures1.yml');
        $objectManager = new EntityManager();
        $fake = $objectManager->find(FakeEntity::class, 1);
        $this->assertEquals(10, count($fake->getAllCustom(' where foo like \'bar\'')));
    }
}
