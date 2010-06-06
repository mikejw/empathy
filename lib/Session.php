<?php

namespace Empathy;

class Session
{
  public static $app = NAME;

  public static function up()
  {    
    @session_start();
    if(!isset($_SESSION['app']) ||
       !in_array(self::$app, $_SESSION['app']))
      {
	$_SESSION['app'][self::$app] = array();
      }
  }

  public static function down()
  {
    //    echo "<pre>\n";
    //print_r($_SESSION);

    unset($_SESSION['app'][self::$app]);
    if(sizeof($_SESSION['app'] == 0))
      {
	session_unset();
	session_destroy();
      }
    
    //echo "<pre>\n";
    //exit();

  }
  
  public static function set($key, $value)
  {
    $_SESSION['app'][self::$app][$key] = $value;
  }

  public static function get($key)
  {
    if(!isset($_SESSION['app'][self::$app][$key]))
      {
	return false;
      }
    else
      {
	return $_SESSION['app'][self::$app][$key];
      }
  }

  public static function clear($key)
  {
    unset($_SESSION['app'][self::$app][$key]);
  }





}
?>