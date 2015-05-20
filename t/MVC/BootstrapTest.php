<?php

namespace ESuite\MVC;

use Empathy\MVC\Config as EmpConfig;
use ESuite\ESuiteTest;
use \Mockery as m;


class BootstrapTest extends ESuiteTest
{
    private $mvc;
    private $bootstrap;


    protected function setUp()
    {
        parent::setUp();

        $dummyBootOptions = array(
            'default_module' => 'foo',
            'dynamic_module' => null,
            'debug_mode' => false,
            'environment' => 'dev',
            'handle_errors' => false
        );

        // ensure there is a default view plugin
        $plugins = array(
            array(
                'name' => 'ELibs',
                'version' => '1.0'
            ),
            array(
                'name' => 'Smarty',
                'version' => '1.0',
                'class_path' => 'Smarty/Smarty.class.php',
                'class_name' => '\Smarty',
                'loader' => ''
            )
        );

        $this->mvc = m::mock('Empathy\MVC\Empathy');
        $this->mvc->shouldReceive('getPersistentMode')->times(1)->andReturn(true);
        $this->bootstrap = new \Empathy\MVC\Bootstrap($dummyBootOptions, $plugins, $this->mvc);
    }


    public function tearDown()
    {
        m::close();
    }


    public function testNew()
    {
        $this->assertInstanceOf('Empathy\MVC\Bootstrap', $this->bootstrap);
    }


    public function testDispatch()
    {       
        $this->setConfig('WEB_ROOT', '');
        $this->setConfig('PUBLIC_DIR', '');
        $this->setConfig('DOC_ROOT', '');

        $this->setExpectedException(
             'Empathy\MVC\RequestException', 'Not found'
        );
        $this->bootstrap->dispatch();
    }


    public function testDispatchException()
    {
        $doc_root = realpath(dirname(realpath(__FILE__)).'/../../');
        $this->setConfig('DOC_ROOT', $doc_root);
        $this->bootstrap->initPlugins();

        $foo = $this->expectOutputRegex('/html/');
        $this->bootstrap->dispatchException(new \Exception());    
    }



}
