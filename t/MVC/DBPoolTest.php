<?php

declare(strict_types=1);

namespace ESuite\MVC;

use Empathy\MVC\Config;
use Empathy\MVC\DBPool;
use ESuite\ESuiteTest;

class DBPoolTest extends ESuiteTest
{
    protected function setUp(): void
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
