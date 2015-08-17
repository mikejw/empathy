<?php

namespace ESuite\Fake;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use Empathy\MVC\Testable;

class Message implements MessageInterface
{
    private $protocolVersion;


    public function __construct()
    {
        Testable::miscReset();
        $this->protocolVersion = '1.1';
        // set random header
        Testable::header('Cache-Control: no-cache, must-revalidate');

    }

    private function protocolVersionValid($version)
    {
        if (!in_array($version, array('1.0', '1.1'))) {
            throw new \Exception('Not valid protocol version.');
        }
    }


    // interface methods

    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    
    public function withProtocolVersion($version)
    {
        $this->protocolVersionValid($version);
        $this->protocolVersion = $version;
        return $this;
    }

    
    public function getHeaders()
    {
        return Testable::getHeaders();
    }

   
    public function hasHeader($name)
    {

    }

    public function getHeader($name)
    {

    }


    public function getHeaderLine($name)
    {

    }


    public function withHeader($name, $value)
    {

    }

   
    public function withAddedHeader($name, $value)
    {

    }

   
    public function withoutHeader($name)
    {

    }

   
    public function getBody()
    {

    }

    public function withBody(StreamInterface $body)
    {

    }

}
