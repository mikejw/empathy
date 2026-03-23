<?php

declare(strict_types=1);

namespace ESuite\MVC;

use Empathy\MVC\Config;
use Empathy\MVC\DBPool;
use Empathy\MVC\Util\Testing\ESuiteTestCase;
use Empathy\MVC\Util\Testing\Util\DB;

class DBPoolTest extends ESuiteTestCase
{
    protected function setUp(): void
    {
        DB::loadDefDBCreds();
    }

    public function testPool()
    {
        $this->assertInstanceOf('PDO', DBPool::getDefCX());
        DBPool::reset();
        Config::store('DB_PORT', false);
        $this->assertInstanceOf('PDO', DBPool::getDefCX());
    }

}
