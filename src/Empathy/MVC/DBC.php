<?php

declare(strict_types=1);

namespace Empathy\MVC;
use PDO;

/**
 * Empathy Database Connection
 * @file      Empathy/DBC.php
 * @description   Instance of an Empathy database connection. (Supports MySQL only.)
 * @author      Mike Whiting
 * @license     See LICENSE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class DBC
{
    /**
    * IP address of database server to connect to.
    *
    */
    private string $server;

    /**
    * Name of database.
    */
    private string $name;

    /**
    * Username to use for connection.
    */
    private string $user;

    /**
    * Password for connection.
    */
    private string $pass;

    /**
    * Database port
    */
    private int | null $port;


    /**
    * Handle for connection produced by PDO.
    */
    private \PDO $handle;

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
    * @param int|null $port database port.
    */
    public function __construct(string $s, string $n, string $u, string $p, ?int $port = null)
    {
        $this->server = $s;
        $this->name = $n;
        $this->user = $u;
        $this->pass = $p;
        $this->port = $port;

        $dsn = 'mysql:host='.$this->server.';dbname='.$this->name.';charset=utf8mb4;';
        echo $dsn;
        if ($this->port !== null) {
            $dsn .= 'port='.$this->port.';';
        }
        $this->handle = new \PDO($dsn, $this->user, $this->pass);
        $this->handle->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }

    /**
    * Returns database connection handle produced by PDO
    * @return PDO handle
    */
    public function getHandle(): PDO
    {
        return $this->handle;
    }
}
