<?php

namespace Empathy;

/**
 * Empathy Database Connection
 * @file			Empathy/DBC.php
 * @description		Instance of an Empathy database connection. (Supports MySQL only.)
 * @author			Mike Whiting
 * @license			LGPLv3
 *
 * (c) copyright Mike Whiting 
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
class DBC
{

  /**
   * IP address of database server to connect to.
   *
   */
  private $server;

  /**
   * Name of database.
   */
  private $name;

  
  /**
   * Username to use for connection.
   */
  private $user;

  /**
   * Password for connection.
   */
  private $pass;

  /**
   * Handle for connection produced by PDO.
   */
  private $handle;


  /** 
   * Contrustor takes connection passed from parameters from
   * DBPool object and creates connection.
   * @param string $s server name.
   *
   * @param string $n database name.
   *
   * @param string $u database username.
   *
   * @param string $p database password.
   *
   * @return void.
   */
  public function __construct($s, $n, $u, $p)
  {
    $this->server = $s;
    $this->name = $n;
    $this->user = $u;
    $this->pass = $p;
    $this->handle = new \PDO('mysql:host='.$this->server.';dbname='.$this->name,
    			     $this->user, $this->pass);
  }


  /**
   * Returns database connection handle produced by PDO
   * @return */
  public function getHandle()
  {    
    print_r($this->handle);

    return $this->handle;
  }

}
?>