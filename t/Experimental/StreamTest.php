<?php

namespace ESuite\Experimental;

use ESuite\ESuiteTest;


class StreamTest extends ESuiteTest
{
    private $stream;
    

    protected function setUp()
    {
        $this->stream = new \ESuite\Fake\Stream();
        $this->stream->write('foo');
        $this->stream->rewind();
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

    public function testDetatch()
    {
        $d = $this->stream->detach();    
        $this->assertTrue($this->stream->isClosed());
        $this->assertEquals('foo', stream_get_contents($d));
        $this->assertNull($this->stream->detach());
    }


    public function testGetSize()
    {
        $this->assertEquals(3, $this->stream->getSize());
        $this->stream->reset();
        $this->stream->write("¡");
        $this->assertEquals(1, $this->stream->getSize());
    }


}

