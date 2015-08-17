<?php

namespace ESuite\Experimental;

use ESuite\ESuiteTest;


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

}

