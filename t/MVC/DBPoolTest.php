<?php

namespace ESuite\MVC;

use Empathy\MVC\DBPool;
use Empathy\MVC\Config;
use ESuite\ESuiteTest;


class DBPoolTest extends ESuiteTest
{

    protected function setUp()
    {
        \ESuite\Util\DB::loadDefDBCreds();
    }

    
    public function testPool()
    {
        $this->assertInstanceOf('PDO', DBPool::getDefCX());
        DBPool::reset();
        Config::store('DB_PORT', false);
        $this->assertInstanceOf('PDO', DBPool::getDefCX());
    }

}
