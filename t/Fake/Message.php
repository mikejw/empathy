<?php

namespace ESuite\Fake;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;


class Message implements MessageInterface
{
    private $protocolVersion;
    private $headers = array();
    private $body;
    private $matched;

    public function __construct()
    {
        $this->protocolVersion = '1.1';
        $this->body = new Stream();
    }

    private function protocolVersionValid($version)
    {
        if (!in_array($version, array('1.0', '1.1'))) {
            throw new \Exception('Not valid protocol version.');
        }
    }

    public function findHeader($name)
    {
        $this->matched = NULL;
        $name = strtolower($name);
        foreach ($this->headers as $key => $value) {
            if ($name == strtolower($key)) {
                $this->matched = $key;
                break;
            }
        }       
    }

    public function getMatched()
    {
        return $this->matched;
    }

    // get header by case-insenstive matching
    public function getHeaderMatch($name)
    {
        $header = '';        
        $this->findHeader($name);
        if ($this->matched !== NULL) {
            $header = $this->headers[$this->matched];
        }
        return $header;
    }

    public function setHeader($header, $value)
    {
        $this->headers[$header] = $value;
    }

    public function removeHeader($name)
    {
        $this->findHeader($name);        
        unset($this->headers[$this->getMatched()]);
    }

    public function setBody(StreamInterface $body)
    {
        $this->body = $body;
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
        return $this->headers;
    }

   
    public function hasHeader($name)
    {   
        return in_array(strtolower($name), array_map(
            'strtolower',
            array_keys($this->headers))
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
        $this->setHeader($name, $value);
        return $this;
    }

   
    public function withAddedHeader($name, $value)
    {
        $values = $this->getHeader($name);
        $values[] = $value;
        $values = implode(', ', $values);
        $this->setHeader($this->matched, $values);
        return $this;        
    }

   
    public function withoutHeader($name)
    {        
        $local_m = clone $this;
        $local_m->removeHeader($name);
        return $local_m;
    }

   
    public function getBody()
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        $local_m = clone $this;
        $local_m->setBody($body);
        return $local_m;
    }

}
