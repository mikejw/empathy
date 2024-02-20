<?php

namespace ESuite\MVC;

use ESuite\ESuiteTest;
use Empathy\MVC\Session;


class ControllerTest extends ESuiteTest
{
    private $bootstrap;
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bootstrap = $this->makeFakeBootstrap();
        $this->controller = new \Empathy\MVC\Controller($this->bootstrap);
    }


    public function testNew()
    {
        $this->assertInstanceOf('Empathy\MVC\Controller', $this->controller);
    }

    public function testRedirect()
    {
        $this->expectOutputRegex('/Setting header/');
        $this->controller->redirect('foobar');
    }

    public function testRedirectCGI()
    {
        $this->expectOutputRegex('/Setting header/');
        $this->controller->redirect_cgi('cgiscript.pl');
    }


    public function testSessionDown()
    {
        $this->expectOutputRegex('/session unset/');
        $this->expectOutputRegex('/session destroy/');
        $this->controller->sessionDown();
    }

    public function testXmlHttpRequest()
    {
        $this->assertFalse($this->controller->isXMLHttpRequest());
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->assertTrue($this->controller->isXMLHttpRequest());
    }


    public function testInitID()
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
