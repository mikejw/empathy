<?php

namespace ESuite\Experimental;

use ESuite\ESuiteTest;
use Empathy\MVC\Testable;


class MessageTest extends ESuiteTest
{
    private $message;
    

    protected function setUp()
    {
        $this->message = new \ESuite\Fake\Message();    
    }

    public function testGetProtocolVersion()
    {
        $this->assertEquals('1.1', $this->message->getProtocolVersion());
    }

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
        Testable::header('Cache-Control: no-cache, must-revalidate');
        $headers = $this->message->getHeaders();
        $this->assertEquals(1, sizeof($headers));
    }

    public function testHasHeader()
    {
        Testable::header('Cache-Control: no-cache, must-revalidate');
        $this->assertTrue($this->message->hasHeader('cache-control'));
        $this->assertTrue($this->message->hasHeader('CACHE-control'));
        $this->assertFalse($this->message->hasHeader('cache-controlz'));
    }

    public function testGetHeader()
    {
        Testable::header('Cache-Control: no-cache, must-revalidate');
        Testable::header('Foo-Bar: baz');        
        $this->assertEquals('["no-cache","must-revalidate"]', json_encode($this->message->getHeader('cache-control')));
        $this->assertEquals('["baz"]', json_encode($this->message->getHeader('FOO-BAR')));
        $this->assertEquals('[]', json_encode($this->message->getHeader('ghost')));
    }

    public function testGetHeaderLine()
    {
        Testable::header('Cache-Control: no-cache, must-revalidate');        
        $this->assertEquals('no-cache, must-revalidate', $this->message->getHeaderLine('cache-control'));
        $this->assertEquals('', $this->message->getHeaderLine('ghost'));
    }



}

