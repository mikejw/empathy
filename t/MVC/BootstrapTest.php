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
            //'environment' => 'dev'
            'handle_errors' => false
        );

        $this->mvc = m::mock('Empathy\MVC\Empathy');
        $this->mvc->shouldReceive('getPersistentMode')->times(1)->andReturn(true);
        $this->bootstrap = new \Empathy\MVC\Bootstrap($dummyBootOptions, array(), $this->mvc);
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
        // $_SERVER['HTTP_HOST'] = 'localhost';
        // $_SERVER['REQUEST_URI'] = '/empathy';

        $this->setConfig('WEB_ROOT', 'localhost');
        $this->setConfig('PUBLIC_DIR', '');
        $this->setConfig('DOC_ROOT', '');



        $this->bootstrap->dispatch();
        $this->assertTrue(true);
    }
}
