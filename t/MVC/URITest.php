<?php

namespace ESuite\MVC;

use ESuite\ESuiteTest;
use Empathy\MVC\DI;

class URITest extends ESuiteTest
{
    private $boot;
    private $host = 'www.dev.org';
    private $uri = '';

    protected function setUp(): void
    {
        if (!$this->boot) {
            $this->boot = $this->makeFakeBootstrap(true);
        }
    }

    private function initURI($hostString)
    {
        unset($_GET['module']);
        unset($_GET['class']);
        unset($_GET['event']);
        unset($_GET['id']);
        $_SERVER['HTTP_HOST'] = $this->host;
        $_SERVER['REQUEST_URI'] = $this->uri . $hostString;
        DI::getContainer()->get('URI');
    }

    public function testURI1()
    {
        $this->initURI('/');
        $this->assertEquals('front', $_GET['module']);
        $this->assertEquals('front', $_GET['class']);
        $this->assertEquals('default_event', $_GET['event']);
    }

    public function testURI2()
    {
        $this->initURI('/blog/item/21');
        $this->assertEquals('blog', $_GET['module']);
        $this->assertEquals('blog', $_GET['class']);
        $this->assertEquals('item', $_GET['event']);
        $this->assertEquals(21, $_GET['id']);
    }
}

