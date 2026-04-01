<?php

declare(strict_types=1);

namespace Empathy\MVC;

use PDO;

/**
 * Empathy Database Pool
 * @file            Empathy/DBPool.php
 * @description     Utility for adding and retrieving database connections. Primarily used for
 *                  keeping track of a default connection object and handle.
 *
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class DBPool
{
    /**
     * Data structure for storing connection objects.
     *
     * @var array<string, DBC>
     */
    private static array $pool = [];

    /**
     * Add a new host/connection to the pool.
     *
     * @param string $s host/server address.
     *
     * @param string $n name of database.
     *
     * @param string $u username for database.
     *
     * @param string $p password for database.
     *
     * @param string $host name for connection. used as index in $pool array
     */
    public static function addHost(string $s, string $n, string $u, string $p, string $host, ?int $port = null): void
    {
        self::$pool[$host] = new DBC($s, $n, $u, $p, $port);
    }

    /**
    * Get connection object by name.
    *
    * @param string $host connection name
    *
    * @return DBC Empathy Database Connection Object
    */
    private static function getHost(string $host): DBC
    {
        return self::$pool[$host];
    }

    /**
    * Get a specific connection and return the PDO handle.
    *
    * @param string $host connection name. (Usually 'default'.)
    *
    * @return PDO Handle
    */
    public static function getConnection(string $host): PDO
    {
        $cx = self::getHost($host);
        return $cx->getHandle();
    }


    /**
    * Get the PDO handle for the default connection
    *
    * @return PDO Handle
    */
    public static function getDefCX(): PDO
    {
        if (count(self::$pool) < 1) {
            $defaults = DatabasePoolDefaults::tryFromConfig();
            if ($defaults === null) {
                throw new \LogicException('Database defaults are not configured (DB_SERVER missing).');
            }
            self::addHost(
                $defaults->server,
                $defaults->databaseName,
                $defaults->user,
                $defaults->password,
                'default',
                $defaults->port
            );
        }
        return self::getConnection('default');
    }

    public static function reset(): void
    {
        self::$pool = [];
    }
}
