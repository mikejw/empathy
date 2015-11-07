<?php

namespace ESuite\MVC;

use Empathy\MVC\EntityManager;
use Empathy\MVC\Config as EmpConfig;
use ESuite\ESuiteTest;
use Nelmio\Alice\Fixtures;


class EntityTest extends ESuiteTest
{
    
    protected function setUp()
    {
        \ESuite\Util\DB::loadDefDBCreds();
    }

    
    public function testAlice()
    {
        //$this->markTestSkipped();

        \ESuite\Util\DB::reset('fixtures/dd.sql');
        $objectManager = new EntityManager();
        $objects = Fixtures::load(\ESuite\Util\Config::get('base').'/fixtures/fixtures1.yml', $objectManager);


        \ESuite\Util\DB::reset('fixtures/dd2.sql');
        $objectManager = new EntityManager();
        $objects = Fixtures::load(\ESuite\Util\Config::get('base').'/fixtures/fixtures2.yml', $objectManager);



    }

}
