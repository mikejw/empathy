<?php

namespace ESuite\MVC;

use Empathy\MVC\EntityManager;
use Empathy\MVC\EntityPopulator;
use Empathy\MVC\Config as EmpConfig;
use ESuite\ESuiteTest;

use Nelmio\Alice\Fixtures;
use Nelmio\Alice\Fixtures\Loader;


// also testing new EntityManager class



class EntityTest extends ESuiteTest
{
    
    protected function setUp(): void
    {
        \ESuite\Util\DB::loadDefDBCreds();
    }


    private function loadFixtures($reset, $file)
    {
        $populator = new EntityPopulator();
        \ESuite\Util\DB::reset($reset);
        $objectManager = new EntityManager();

        $file = \ESuite\Util\Config::get('base').$file;
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
        $this->loadFixtures('fixtures/dd.sql', '/fixtures/fixtures1.yml');
        $objectManager = new EntityManager();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Entity class does not exist');
        $fake = $objectManager->find('Esuite\FakeEntityz', 1);
    }

    public function testFind()
    {
        $this->loadFixtures('fixtures/dd.sql', '/fixtures/fixtures1.yml');
        $objectManager = new EntityManager();
        $fake = $objectManager->find('Esuite\FakeEntity', 1);
        $this->assertEquals($fake->foo, 'bar');
    }


    /* Actual entity tests */


    public function testSave()
    {
        $this->loadFixtures('fixtures/dd.sql', '/fixtures/fixtures1.yml');
        $objectManager = new EntityManager();
        $fake = $objectManager->find('Esuite\FakeEntity', 1);
        $fake->foo = 'new';
        $fake->save();
        $fake = $objectManager->find('Esuite\FakeEntity', 1);
        $this->assertEquals($fake->foo, 'new');
    }    

    public function testGetAll()
    {
        $this->loadFixtures('fixtures/dd.sql', '/fixtures/fixtures1.yml');
        $objectManager = new EntityManager();
        $fake = $objectManager->find('Esuite\FakeEntity', 1);
        $this->assertEquals(10, sizeof($fake->getAll()));
    }

    public function testGetAllCustom()
    {
        $this->loadFixtures('fixtures/dd.sql', '/fixtures/fixtures1.yml');
        $objectManager = new EntityManager();
        $fake = $objectManager->find('Esuite\FakeEntity', 1);
        $this->assertEquals(10, sizeof($fake->getAllCustom($fake::TABLE, ' where foo like \'bar\'')));
    }







}
