<?php

namespace ESuite\MVC;

use ESuite\ESuiteTest;
use Empathy\MVC\DI;

class URITest extends ESuiteTest
{
    private $boot;
    private $host = 'localhost';
    private $uri = '/eaa/public_html/';

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
        $_SERVER['HTTP_HOST'] = $this->host;
        $_SERVER['REQUEST_URI'] = $this->uri . $hostString;
        DI::getContainer()->get('URI');
    }

    public function testURI()
    {
        $this->initURI('');
        $this->assertEquals('front', $_GET['module']);
        $this->assertEquals('front', $_GET['class']);
        $this->assertEquals('default_event', $_GET['event']);
    }
}
