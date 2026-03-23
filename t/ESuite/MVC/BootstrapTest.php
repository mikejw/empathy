<?php

declare(strict_types=1);

namespace ESuite\MVC;

use Empathy\MVC\Util\Testing\ESuiteTestCase;

class BootstrapTest extends ESuiteTestCase
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
        $this->assertInstanceOf(\Empathy\MVC\Bootstrap::class, $this->bootstrap);
    }


    public function testDispatch()
    {
        $this->expectException(\Empathy\MVC\RequestException::class);
        $_SERVER['HTTP_HOST'] = 'www.dev.org';
        $_SERVER['REQUEST_URI'] = '/foo';
        $this->bootstrap->dispatch();
    }


    public function testDispatchException()
    {
        $this->expectOutputRegex('/html/');
        $this->bootstrap->dispatchException(new \Exception());
    }

}
