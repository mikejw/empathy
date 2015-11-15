<?php

namespace ESuite\MVC;

use Empathy\MVC\RedbeanManager;
use Empathy\MVC\RedbeanPopulator;

use Empathy\MVC\Config as EmpConfig;
use ESuite\ESuiteTest;

use Nelmio\Alice\Fixtures;
use Nelmio\Alice\Fixtures\Loader;


class RedbeanTest extends ESuiteTest
{
    
    protected function setUp()
    {
        \ESuite\Util\DB::loadDefDBCreds();
    }


    private function loadFixtures($reset, $file)
    {
        $populator = new RedbeanPopulator();
        \ESuite\Util\DB::reset($reset);
        $objectManager = new RedbeanManager();

        $file = \ESuite\Util\Config::get('base').$file;
        $loader = new Loader();        
        $loader->addPopulator($populator);

        $objects = $loader->load($file);
        $objectManager->persist($objects);
    }


   
    public function testAlice()
    {
        $this->loadFixtures('fixtures/dd.sql', '/fixtures/fixtures1.yml');
    }






}
