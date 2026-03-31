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
    * Handle for connection produced by PDO.
    */
    private readonly \PDO $handle;

    /**
     * Contrustor takes connection passed from parameters from
     * DBPool object and creates connection.
     * @param string $server server name.
     *
     * @param string $name database name.
     *
     * @param string $user database username.
     *
     * @param string $pass database password.
     *
     * @param int|null $port database port.
     */
    public function __construct(private readonly string $server, private readonly string $name, private readonly string $user, private readonly string $pass, private readonly int | null $port = null)
    {
        $dsn = 'mysql:host='.$this->server.';dbname='.$this->name.';charset=utf8mb4;';
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
