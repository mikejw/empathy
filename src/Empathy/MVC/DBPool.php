<?php

namespace Empathy;

/**
 * Empathy Database Pool
 * @file			Empathy/DBPool.php
 * @description		Utility for adding and retrieving database connections. Primarily used for keeping track of a default connection object and handle.
 * @author			Mike Whiting
 * @license			LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
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

  public static function addHost($s, $n, $u, $p, $host)
  {
    self::$pool[$host] = new DBC($s, $n, $u, $p);
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
    self::addHost(DB_SERVER, DB_NAME, DB_USER, DB_PASS, 'default');
      }

    return self::getConnection('default');
  }
}
