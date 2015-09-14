<?php

namespace ESuite\Fake;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    private $data;

    public function __construct()
    {
        $this->data = 'foo';
    }


    public function __toString()
    {
        return $this->data;
    }

    public function close()
    {

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
