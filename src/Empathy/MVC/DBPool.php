<?php

namespace Empathy\MVC;

use Empathy\MVC\Config;

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
    */
    private static $pool = array();

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
    *
    * @return void
    */
    public static function addHost($s, $n, $u, $p, $host, $port = null)
    {
        self::$pool[$host] = new DBC($s, $n, $u, $p, $port);
    }

    /**
    * Get connection object by name.
    *
    * @param string $host connection name
    *
    * return DBC $host Empathy Database Connection Object
    */
    private static function getHost($host)
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
    public static function getConnection($host)
    {
        $cx = self::getHost($host);
        return $cx->getHandle();
    }


    /**
    * Get the PDO handle for the default connection
    *
    * @return PDO Handle
    */
    public static function getDefCX()
    {
        if (sizeof(self::$pool) < 1) {
            $db_port = Config::get('DB_PORT');
            if (is_numeric($db_port)) {
                self::addHost(
                    Config::get('DB_SERVER'),
                    Config::get('DB_NAME'),
                    Config::get('DB_USER'),
                    Config::get('DB_PASS'),
                    'default',
                    $db_port
                );
            } else {
                self::addHost(
                    Config::get('DB_SERVER'),
                    Config::get('DB_NAME'),
                    Config::get('DB_USER'),
                    Config::get('DB_PASS'),
                    'default'
                );
            }
        }
        return self::getConnection('default');
    }

    public static function reset()
    {
        self::$pool = array();
    }
}
