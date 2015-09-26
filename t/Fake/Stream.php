<?php

namespace ESuite\Fake;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    private $data;

    public function __construct()
    {
        $this->init();
    }


    private function init()
    {
        $this->data = fopen('php://memory', 'r+');
    }


    public function reset()
    {
        $this->close();
        $this->init();
    }


    public function isClosed()
    {
        $meta = @stream_get_meta_data($this->data);
        return $meta['mode'] == NULL;
    }

    // interface methods


    public function __toString()
    {
        return $this->getContents();
    }

    public function close()
    {
        fclose($this->data);
    }

    public function detach()
    {
        if (!$this->isClosed($this->data)) {
            $new = fopen('php://memory', 'r+');
            stream_copy_to_stream($this->data, $new);
            $this->close();
            rewind($new);
            return $new;
        } else {
            return NULL;
        }
    }

    public function getSize()
    {
        return strlen(utf8_decode($this->__toString()));
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
        rewind($this->data);
    }


    public function isWritable()
    {

    }

    public function write($string)
    {
        fwrite($this->data, $string);   
    }


    public function isReadable()
    {

    }

    public function read($length)
    {

    }

    public function getContents()
    {
        $this->rewind();
        return stream_get_contents($this->data);
    }

    public function getMetadata($key = null)
    {

    }
}
