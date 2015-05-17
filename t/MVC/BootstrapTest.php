<?php

namespace ESuite\MVC;
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
            'environment' => 'dev'
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
        define('WEB_ROOT', 'localhost');
        define('PUBLIC_DIR', '');
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/empathy';

        $this->bootstrap->dispatch();
        $this->assertTrue(true);
    }


}
