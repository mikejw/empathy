<?php

namespace ESuite\MVC;

use Empathy\MVC\DBPool;
use Empathy\MVC\Config;
use ESuite\ESuiteTest;


class DBPoolTest extends ESuiteTest
{

    protected function setUp()
    {
        $creds = $this->getDefDBCreds();
        Config::store('DB_SERVER', $creds['db_host']);
        Config::store('DB_NAME', $creds['db_name']);
        Config::store('DB_USER', $creds['db_user']);
        Config::store('DB_PASS', $creds['db_pass']);
        Config::store('DB_PORT', $creds['db_port']);
    }

    
    public function testPool()
    {
        $this->assertInstanceOf('PDO', DBPool::getDefCX());
        DBPool::reset();
        Config::store('DB_PORT', false);
        $this->assertInstanceOf('PDO', DBPool::getDefCX());
    }

}
