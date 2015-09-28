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

    public function testTell()
    {
        $this->stream->reset();
        $this->stream->write('hello, world');
        $this->assertEquals(12, $this->stream->tell());
    }

    public function testEof()
    {
        $this->stream->reset();
        $this->stream->write('hello, world');
        $chunk = $this->stream->read(1);
        $this->assertTrue($this->stream->eof());
    }

    public function testIsSeekable()
    {
        $this->assertTrue($this->stream->isSeekable());
    }

    public function testSeek()
    {
        $this->stream->reset();
        $this->stream->write('0123456789');
        $this->stream->rewind();
        $this->assertEquals(0, $this->stream->tell());
        $this->stream->seek(1, SEEK_SET);
        $this->assertEquals(1, $this->stream->tell());
        $this->stream->seek(1, SEEK_CUR);
        $this->assertEquals(2, $this->stream->tell());
        $this->stream->seek(-9, SEEK_END);
        $this->assertEquals(1, $this->stream->tell());
    }

    public function testIsWritable()
    {
        $this->assertTrue($this->stream->isWritable());
    }

    public function testIsReadable()
    {
        $this->assertTrue($this->stream->isWritable());
    }

}

