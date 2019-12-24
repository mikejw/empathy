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
        $mode = 'r+';
        $this->data = fopen('php://memory', $mode);
    }


    public function reset()
    {
        $this->close();
        $this->init();
    }


    public function isClosed()
    {
        return $this->getMetadata('mode') == NULL;
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
        return ftell($this->data);
    }

    public function eof()
    {
        return feof($this->data);

    }

    public function isSeekable()
    {
        return $this->getMetadata('seekable') == true;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        fseek($this->data, $offset, $whence);
    }

    public function rewind()
    {
        rewind($this->data);
    }


    public function isWritable()
    {
        $mode = $this->getMetadata('mode');
        return strpos($mode, 'w+') === 0;
    }

    public function write($string)
    {
        fwrite($this->data, $string);   
    }


    public function isReadable()
    {
        $mode = $this->getMetadata('mode');
        return strpos($mode, 'w+') === 0 || strpos($mode, 'r') === 0;
    }

    public function read($length)
    {
        return fread($this->data, $length);
    }

    public function getContents()
    {
        $this->rewind();
        return stream_get_contents($this->data);
    }

    public function getMetadata($key = null)
    {
        $meta = @stream_get_meta_data($this->data);
        if (!$meta) {
            return null;
        } else {
            if ($key === null) {
                return $meta;
            } else {
                return $meta[$key];
            }
        }
    }
}
