<?php

declare(strict_types=1);

namespace ESuite\MVC;

use Empathy\MVC\Util\Testing\ESuiteTestCase;

class BootstrapTest extends ESuiteTestCase
{
    private mixed $bootstrap = null;


    protected function setUp(): void
    {
        parent::setUp();

        if ($this->bootstrap === null) {
            $this->bootstrap = $this->makeFakeBootstrap();
        }
    }


    public function testNew(): void
    {
        $this->assertInstanceOf(\Empathy\MVC\Bootstrap::class, $this->bootstrap);
    }


    public function testDispatch(): void
    {
        $this->expectException(\Empathy\MVC\RequestException::class);
        $_SERVER['HTTP_HOST'] = 'www.dev.org';
        $_SERVER['REQUEST_URI'] = '/foo';
        $this->bootstrap->dispatch();
    }


    public function testDispatchException(): void
    {
        $this->expectOutputRegex('/html/');
        $this->bootstrap->dispatchException(new \Exception());
    }

}
