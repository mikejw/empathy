<?php

namespace ESuite\MVC;

use Empathy\MVC\Entity;
use ESuite\ESuiteTest;


class EntityTest extends ESuiteTest
{
    private $entity;

    protected function setUp()
    {
        $creds = $this->getDefDBCreds();

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
        //
        $this->assertTrue(true);
    }

}
