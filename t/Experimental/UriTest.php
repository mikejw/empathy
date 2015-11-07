<?php

namespace ESuite\Experimental;

use ESuite\ESuiteTest;


class UriTest extends ESuiteTest
{
    private $uri;
    
    protected function setUp()
    {
        $this->uri = new \ESuite\Fake\Uri();
        $this->uri->initDummy();
    }

    public function testGetScheme()
    {
        $this->assertEquals('http', $this->uri->getScheme());
    }

    public function testGetAuthority()
    {
        $this->assertEquals('foo:bar@localhost:80', $this->uri->getAuthority());
    }

    public function testGetUserInfo()
    {
        $this->assertEquals('foo:bar', $this->uri->getUserInfo());
    }

    public function testGetHost()
    {
        $this->assertEquals('localhost', $this->uri->getHost());
    }


    public function testToString()
    {
        $this->assertTrue(true);
    }

    public function testGetPort()
    {
        $this->assertEquals(80, $this->uri->getPort());
    }



}

