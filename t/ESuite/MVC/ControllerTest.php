<?php

declare(strict_types=1);

namespace ESuite\MVC;

use Empathy\MVC\Util\Testing\ESuiteTestCase;

class ControllerTest extends ESuiteTestCase
{
    private mixed $bootstrap = null;
    private mixed $controller = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bootstrap = $this->makeFakeBootstrap();
        $this->controller = new \Empathy\MVC\Controller($this->bootstrap);
    }


    public function testNew(): void
    {
        $this->assertInstanceOf(\Empathy\MVC\Controller::class, $this->controller);
    }

    public function testRedirect(): void
    {
        $this->expectOutputRegex('/Setting header/');
        $this->controller->redirect('foobar');
    }

    public function testRedirectCGI(): void
    {
        $this->expectOutputRegex('/Setting header/');
        $this->controller->redirect_cgi('cgiscript.pl');
    }


    public function testSessionDown(): void
    {
        $this->expectOutputRegex('/session unset/');
        $this->expectOutputRegex('/session destroy/');
        $this->controller->sessionDown();
    }

    public function testXmlHttpRequest(): void
    {
        $this->assertFalse($this->controller->isXMLHttpRequest());
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->assertTrue($this->controller->isXMLHttpRequest());
    }


    public function testInitID(): void
    {
        $_GET['id'] = 100;
        $valid = $this->controller->initID('id', 1);
        $this->assertTrue($valid);
        $_GET['id'] = 100.1;
        $valid = $this->controller->initID('id', 1);
        $this->assertFalse($valid);
        $valid = $this->controller->initID('some_id', 1, true);
        $this->assertFalse($valid);
    }

}
