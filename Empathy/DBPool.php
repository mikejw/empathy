<?php

namespace Empathy;

class DBPool
{
  private static $pool = array();

  public static function addHost($s, $n, $u, $p, $host)
  {
    self::$pool[$host] = new DBC($s, $n, $u, $p);
  }

  private static function getHost($host)
  {
    return self::$pool[$host];
  }

  public static function getConnection($host)
  {
    $cx = self::getHost($host);
    return $cx->getHandle();
  }

  public static function getDefCX()
  {
    if(sizeof(self::$pool) < 1)
      {
	self::addHost(DB_SERVER, DB_NAME, DB_USER, DB_PASS, 'default');
      }    

    return self::getConnection('default');
  }
}
?>