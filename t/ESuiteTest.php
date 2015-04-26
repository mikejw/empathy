<?php

namespace ESuite;

use Empathy\MVC\Util\CLI,
    Empathy\MVC\Util\CLIMode;


abstract class ESuiteTest extends \PHPUnit_Framework_TestCase
{
   
    protected function setUp()
    {
                
        //
    }    
    


    protected function tearDown()
    {
        global $suite;
        if (Config::get('reset_db')) {
            $suite->dbReset();
        }
    }


    public static function setUpBeforeClass()
    {
        //
    }

    public static function tearDownAfterClass()
    {
        //
    }


    protected function appRequest($uri, $mode=CLIMode::FAKED)
    {
        global $boot;       
        CLI::setReqMode($mode);
        return CLI::request($boot, $uri);
    }
}
