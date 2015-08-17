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
    }

    private function protocolVersionValid($version)
    {
        if (!in_array($version, array('1.0', '1.1'))) {
            throw new \Exception('Not valid protocol version.');
        }
    }

    // get header by case-insenstive matching
    private function getHeaderMatch($name)
    {
        $header = '';        
        $name = strtolower($name);
        $h = array_change_key_case(Testable::getHeaders());
        if(isset($h[$name])) {
            $header = $h[$name];        
        }
        return $header;
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
        return in_array(strtolower($name), array_map(
            'strtolower',
            array_keys(Testable::getHeaders()))
        );
    }

    public function getHeader($name)
    {
        $values = array();        
        $h = $this->getHeaderMatch($name);
        if ($h !== '') {
            $values = array_map('trim', explode(',', $h));
        }
        return $values;
    }


    public function getHeaderLine($name)
    {
        return $this->getHeaderMatch($name);        
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
