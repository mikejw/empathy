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

        $this->bootstrap->dispatch();
    }


    public function testDispatchException()
    {           
        $this->expectOutputRegex('/html/');
        $this->bootstrap->dispatchException(new \Exception());    
    }

}
