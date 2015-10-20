<?php

namespace ESuite\Fake;

use Psr\Http\Message\UriInterface;


class Uri implements UriInterface
{

    private $scheme = '';
    private $user = '';
    private $password = '';
    private $port = 0;
    private $host = '';

    
    public function initDummy()
    {
        $this->scheme = 'http';
        $this->user = 'foo';
        $this->password = 'bar';
        $this->port = 80;
        $this->host = 'localhost';
    }



    // interface methods


    public function getScheme()
    {  
        return $this->scheme;
    }

    
    public function getAuthority()
    {
        return $this->getUserInfo().'@'.$this->getHost().':'.$this->port;
    }

    public function getUserInfo()
    {
        return $this->user.':'.$this->password;
    }

    public function getHost()
    {
        return $this->host;
    }

   
    public function getPort()
    {  
        return $this->port;
    }

    public function getPath()
    {

    }

    public function getQuery()
    {

    }

    public function getFragment()
    {

    }

    public function withScheme($scheme)
    {

    }

    public function withUserInfo($user, $password = null)
    {

    }

    public function withHost($host)
    {

    }

    public function withPort($port)
    {

    }

    public function withPath($path)
    {

    }

    public function withQuery($query)
    {

    }

    public function withFragment($fragment)
    {

    }

    public function __toString()
    {
        echo 'foo';
    }


}
