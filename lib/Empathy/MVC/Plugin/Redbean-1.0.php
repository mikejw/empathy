<?php

namespace Empathy\MVC\Plugin;
use Empathy\MVC\Plugin as Plugin;

class Redbean extends Plugin implements PreDispatch
{

  public function __construct()
  {
    //
  }


  private function isIP($server)
  {
    $ip = false;
    $count = 0;
    $stripped = str_replace('.', '', DB_SERVER, $count);
    if($count)
      {	
	if(is_numeric($stripped))
	  {
	    $ip = true;
	  }	
      }
    return $ip;
  }


  public function onPreDispatch()
  {           
    if(!$this->isIP(DB_SERVER))
      {	   
	throw new \Empathy\Exception('Database server must be an IP address.');
      }	            
            
    \R::setup('mysql:host='.DB_SERVER.';dbname='.DB_NAME, DB_USER, DB_PASS);
    //\R::setup();
  }
}
?>