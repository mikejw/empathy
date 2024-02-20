<?php

namespace ESuite\MVC;

use Empathy\MVC\DBC;
use ESuite\ESuiteTest;
use ESuite\Util\DB;


class DBCTest extends ESuiteTest
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
