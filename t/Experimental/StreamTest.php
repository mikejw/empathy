<?php

namespace ESuite\Experimental;

use ESuite\ESuiteTest;


class StreamTest extends ESuiteTest
{
    private $stream;
    

    protected function setUp()
    {
        $this->stream = new \ESuite\Fake\Stream();
    }

    public function testToString()
    {
        $this->expectOutputString('foo');
        echo $this->stream;
    }

    public function testClose()
    {
        $this->assertFalse($this->stream->isClosed());
        $this->stream->close();
        $this->assertTrue($this->stream->isClosed());
    }    




}

