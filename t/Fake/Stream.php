<?php

namespace ESuite\Fake;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    private $data;

    public function __construct()
    {
        $this->data = fopen('php://memory', 'r+');
        fwrite($this->data, 'foo');
        rewind($this->data);
    }


    public function isClosed()
    {
        $meta = @stream_get_meta_data($this->data);
        return $meta['mode'] == NULL;
    }




    // interface methods


    public function __toString()
    {
        return stream_get_contents($this->data);
    }

    public function close()
    {
        fclose($this->data);
    }

    public function detach()
    {

    }

    public function getSize()
    {

    }

    public function tell()
    {

    }

    public function eof()
    {

    }

    public function isSeekable()
    {

    }

    public function seek($offset, $whence = SEEK_SET)
    {

    }

    public function rewind()
    {

    }

    public function isWritable()
    {

    }

    public function write($string)
    {

    }

    public function isReadable()
    {

    }

    public function read($length)
    {

    }

    public function getContents()
    {

    }

    public function getMetadata($key = null)
    {

    }
}
