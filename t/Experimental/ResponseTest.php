<?php

namespace ESuite\Experimental;

use ESuite\ESuiteTest;


class ResponseTest extends ESuiteTest
{
    private $message;
    

    protected function setUp()
    {
        $this->message = new \ESuite\Fake\Response();
    }

    public function testGetProtocolVersion()
    {
        $this->assertEquals('1.1', $this->message->getProtocolVersion());
    }

/*
    public function testWithProtocolVersion()
    {
        $version = '1.0';
        $this->assertInstanceOf('ESuite\Fake\Message', $this->message->withProtocolVersion($version));
        $this->assertEquals($version, $this->message->getProtocolVersion());

        $version = '0.9';
        $this->setExpectedException('Exception');
        $this->message->withProtocolVersion($version);
    }

    public function testGetAllHeaders()
    {
        $this->message->setHeader('Cache-Control', 'no-cache, must-revalidate');
        $headers = $this->message->getHeaders();
        $this->assertEquals(1, sizeof($headers));
    }

    public function testFindHeader()
    {
        $this->message->setHeader('Cache-Control', 'no-cache, must-revalidate');
        $this->message->findHeader('cache-control');
        $key = $this->message->getMatched();
        $this->assertEquals('no-cache, must-revalidate', $this->message->getHeaderLine($key));
    }


    public function testHasHeader()
    {
        $this->message->setHeader('Cache-Control', 'no-cache, must-revalidate');
        $this->assertTrue($this->message->hasHeader('cache-control'));
        $this->assertTrue($this->message->hasHeader('CACHE-control'));
        $this->assertFalse($this->message->hasHeader('cache-controlz'));
    }

    public function testGetHeader()
    {
        $this->message->setHeader('Cache-Control', 'no-cache, must-revalidate');
        $this->message->setHeader('Foo-Bar', 'baz');
        $this->assertEquals('["no-cache","must-revalidate"]', json_encode($this->message->getHeader('cache-control')));
        $this->assertEquals('["baz"]', json_encode($this->message->getHeader('FOO-BAR')));
        $this->assertEquals('[]', json_encode($this->message->getHeader('ghost')));
    }

    public function testGetHeaderLine()
    {        
        $this->message->setHeader('Cache-Control', 'no-cache, must-revalidate');
        $this->assertEquals('no-cache, must-revalidate', $this->message->getHeaderLine('cache-control'));
        $this->assertEquals('', $this->message->getHeaderLine('ghost'));
    }

    public function testWithHeader()
    {        
        $this->message->setHeader('Cache-Control', 'no-cache, must-revalidate');
        $n = $this->message->withHeader('Cache-Control', '0');
        $m = $this->message->withHeader('Cache-Control', 'no-cache, must-revalidate');
        $this->assertInstanceOf('ESuite\Fake\Message', $m);
        $this->assertEquals('no-cache, must-revalidate', $m->getHeaderLine('cache-control'));
    }

    public function testWithAddedHeader()
    {
        $this->message->setHeader('Cache-Control', 'no-cache');
        $m = $this->message->withAddedHeader('Cache-ControL', 'must-revalidate');        
        $this->assertInstanceOf('ESuite\Fake\Message', $m);
        $h = $m->getHeaderLine('cache-control');
        $this->assertEquals('no-cache, must-revalidate', $m->getHeaderLine('cache-control'));
    }

    public function testWithoutHeader()
    {
        $this->message->setHeader('Control', 'no-cache');
        $this->message->setHeader('CACHE-Control', 'no-cache');
        $m = $this->message->withoutHeader('Cache-Control');
        $this->assertInstanceOf('ESuite\Fake\Message', $m);
        $this->assertEquals('', $m->getHeaderLine('cache-control'));
    }

    public function testGetBody()
    {
         $this->assertInstanceOf('ESuite\Fake\Stream', $this->message->getBody());
    }

    public function testWithBody()
    {
         $this->assertInstanceOf('ESuite\Fake\Message', $this->message->withBody(new \ESuite\Fake\Stream()));
    }
    */

}

