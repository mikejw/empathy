<?php

namespace ESuite\MVC;

use ESuite\ESuiteTest;


class BootstrapTest extends ESuiteTest
{
    private $bootstrap;
    

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->bootstrap === null) {
           $this->bootstrap = $this->makeFakeBootstrap();
        }
    }


    public function testNew()
    {
        $this->assertInstanceOf('Empathy\MVC\Bootstrap', $this->bootstrap);
    }


    public function testDispatch()
    {
        $this->expectException('Empathy\MVC\RequestException');
        $this->expectExceptionMessage('Not found');

        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/eaa/public_html/foo';
        $this->bootstrap->dispatch();
    }


    public function testDispatchException()
    {           
        $this->expectOutputRegex('/html/');
        $this->bootstrap->dispatchException(new \Exception());    
    }

}
