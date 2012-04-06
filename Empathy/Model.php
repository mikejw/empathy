<?php

namespace Empathy;

class Model
{
  protected static $db_handle = null;

  protected static function connectModel($model, $host)
  {
    // cached handle is not null
    // and new host is null
    // use cached
    if(self::$db_handle !== null && $host === null)
      {
	$model->setDBH(self::$db_handle);
      }
    // use a new host
    elseif($host !== null)
      {
	$model->setDBH(DBPool::getConnection($host));
      }
    // db_handle is null and host is null
    // (initiate default)
    elseif(self::$db_handle == null && $host == null)
      {
	$handle = DBPool::getDefCX();
	$model->setDBH($handle);
	self::$db_handle = $handle;
      }

  }

  public static function load($model, $host = null)
  {           
    $class = '\Empathy\\Model\\'.$model;
    $storage_object = new $class();

    self::connectModel($storage_object, $host);

    return $storage_object;
  }

  public static function getTable($model)
  {
    $class = '\\Empathy\\Model\\'.$model;
    return $class::TABLE;
  }
}
?>