<?php

declare(strict_types=1);

namespace ESuite\MVC;

use Empathy\MVC\DBC;
use ESuite\Util\DB;
use Empathy\MVC\Util\Testing\ESuiteTestCase;

class DBCTest extends ESuiteTestCase
{
    private $dbc;

    protected function setUp(): void
    {
        DB::loadDefDBCreds();
        DB::reset();

        $creds = DB::getDefDBCreds();

        $this->dbc = new DBC(
            $creds['db_host'],
            $creds['db_name'],
            $creds['db_user'],
            $creds['db_pass'],
            $creds['db_port']
        );
    }


    public function testConnection()
    {
        $this->assertInstanceOf('PDO', $this->dbc->getHandle());
    }

}
