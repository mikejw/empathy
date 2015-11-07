<?php

namespace ESuite;

use Empathy\MVC\Util\CLI;
use Empathy\MVC\Util\CLIMode;
use Empathy\MVC\Config as EmpConfig;
use \Mockery as m;


abstract class ESuiteTest extends \PHPUnit_Framework_TestCase
{
   
    protected function setUp()
    {      
        //
    }    
    

    protected function makeFakeBootstrap()
    {
        // use eaa archive as root
        $doc_root = realpath(dirname(realpath(__FILE__)).'/../eaa/');
        
        $this->setConfig('NAME', 'empathytest');
        $this->setConfig('TITLE', 'empathy testing');
        $this->setConfig('DOC_ROOT', $doc_root);
        $this->setConfig('WEB_ROOT' , 'localhost/empathytest');
        $this->setConfig('PUBLIC_DIR', '/public_html');

        $dummyBootOptions = array(
            'default_module' => 'foo',
            'dynamic_module' => null,
            'debug_mode' => false,
            'environment' => 'dev',
            'handle_errors' => false
        );
        $plugins = array(
            array(
                'name' => 'ELibs',
                'version' => '1.0',
                'config' => '{ "testing": true }'
            ),
            array(
                'name' => 'Smarty',
                'version' => '1.0',
                'class_path' => 'Smarty/Smarty.class.php',
                'class_name' => '\Smarty',
                'loader' => ''
            )
        );

        $mvc = m::mock('Empathy\MVC\Empathy');
        $mvc->shouldReceive('getPersistentMode')->times(1)->andReturn(true);
        $bootstrap = new \Empathy\MVC\Bootstrap($dummyBootOptions, $plugins, $mvc);

        $bootstrap->initPlugins();
        return $bootstrap;
    }




    protected function setConfig($key, $value)
    {
        EmpConfig::store($key, $value);
    }


    protected function tearDown()
    {
        m::close();

        global $suite;
        if (Util\Config::get('reset_db')) {
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
