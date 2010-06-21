<?php

namespace Empathy;

class DBC
{
  private $server;
  private $name;
  private $user;
  private $pass;
  private $handle;


  public function __construct($s, $n, $u, $p)
  {
    $this->server = $s;
    $this->name = $n;
    $this->user = $u;
    $this->pass = $p;
    $this->handle = new \PDO('mysql:host='.$this->server.';dbname='.$this->name,
    			     $this->user, $this->pass);
  }

  public function getHandle()
  {    
    return $this->handle;
  }

}
?>