<?php

namespace ESuite\Fake;

use \Psr\Http\Message\MessageInterface;
use \Psr\Http\Message\StreamInterface;


class Message implements MessageInterface
{
    private $protocolVersion;


    public function __construct()
    {
        $this->protocolVersion = '1.1';
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
