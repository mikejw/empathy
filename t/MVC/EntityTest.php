<?php

namespace ESuite\MVC;

use Empathy\MVC\EntityManager;
use Empathy\MVC\EntityPopulator;
use Empathy\MVC\Config as EmpConfig;
use ESuite\ESuiteTest;

use Nelmio\Alice\Fixtures;
use Nelmio\Alice\Fixtures\Loader;


class EntityTest extends ESuiteTest
{
    
    protected function setUp()
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

    
    public function testAlice()
    {

        $this->loadFixtures('fixtures/dd.sql', '/fixtures/fixtures1.yml');

        $this->loadFixtures('fixtures/dd2.sql', '/fixtures/fixtures2.yml');
        
        //$this->loadFixtures('fixtures/dd3.sql', '/fixtures/fixtures3.yml');
        
        
       

    }

}
