<?php

namespace ESuite;

use Empathy\MVC\Util\CLI;
use Empathy\MVC\Util\CLIMode;
use Empathy\MVC\Config as EmpConfig;
use Empathy\MVC\DI;


abstract class ESuiteTest extends \PHPUnit\Framework\TestCase
{
   
    protected function setUp(): void
    {
        //
    }    
    

    protected function makeFakeBootstrap($persistentMode=true)
    {
        // use eaa archive as root
        $doc_root = realpath(dirname(realpath(__FILE__)).'/../eaa/');
        
        $this->setConfig('NAME', 'empathytest');
        $this->setConfig('TITLE', 'empathy testing');
        $this->setConfig('DOC_ROOT', $doc_root);
        $this->setConfig('WEB_ROOT' , 'localhost/empathytest');
        $this->setConfig('PUBLIC_DIR', '/public_html');

        $dummyBootOptions = array(
            'default_module' => 'front',
            'dynamic_module' => false,
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

        $container = DI::init($doc_root, $persistentMode);
        $empathy = $container->get('Empathy');
        $empathy->setBootOptions($dummyBootOptions);
        $empathy->setPlugins($plugins);
        $empathy->init();

        $bootstrap = $container->get('Bootstrap');
        return $bootstrap;
    }




    protected function setConfig($key, $value)
    {
        EmpConfig::store($key, $value);
    }


    protected function tearDown(): void
    {
        global $suite;
        if (Util\Config::get('reset_db')) {
            $suite->dbReset();
        }
    }


    public static function setUpBeforeClass(): void
    {
        //
    }

    public static function tearDownAfterClass(): void
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
