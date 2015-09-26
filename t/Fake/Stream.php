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


    /** 
     * (From php.net)
     * Count the number of bytes of a given string.
     * Input string is expected to be ASCII or UTF-8 encoded.
     * Warning: the function doesn't return the number of chars
     * in the string, but the number of bytes.
     * See http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
     * for information on UTF-8.
     *
     * @param string $str The string to compute number of bytes
     *
     * @return The length in bytes of the given string.
     */
    private function strBytes($str)
    {
        // STRINGS ARE EXPECTED TO BE IN ASCII OR UTF-8 FORMAT
        
        // Number of characters in string
        $strlen_var = strlen($str);
        
        // string bytes counter
        $d = 0;
        
        /*
         * Iterate over every character in the string,
         * escaping with a slash or encoding to UTF-8 where necessary
         */
        for ($c = 0; $c < $strlen_var; ++$c) {
            $ord_var_c = ord($str{$c});
            switch(true) {
            case(($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)):
                // characters U-00000000 - U-0000007F (same as ASCII)
                $d++;
                break;
            case(($ord_var_c & 0xE0) == 0xC0):
                // characters U-00000080 - U-000007FF, mask 110XXXXX
                $d+=2;
                break;
            case(($ord_var_c & 0xF0) == 0xE0):
                // characters U-00000800 - U-0000FFFF, mask 1110XXXX
                $d+=3;
                break;
            case(($ord_var_c & 0xF8) == 0xF0):
                // characters U-00010000 - U-001FFFFF, mask 11110XXX
                $d+=4;
                break;
            case(($ord_var_c & 0xFC) == 0xF8):
                // characters U-00200000 - U-03FFFFFF, mask 111110XX
                $d+=5;
                break;
            case(($ord_var_c & 0xFE) == 0xFC):
                // characters U-04000000 - U-7FFFFFFF, mask 1111110X
                $d+=6;
                break;
            default:
                $d++;
            };
        };
        return $d;
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
        $contents = $this->__toString();
        return $this->strBytes($contents);
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
