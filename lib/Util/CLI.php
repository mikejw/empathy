<?php

namespace Empathy\Util;

/* based on Util/Test.php
   (which is currently outside of namespacing) 
*/

class CLI
{
  
  // new function
  private static function realMicrotime()
  {
    list($micro, $seconds) = explode(' ', microtime());
    return ((float)$micro + (float)$seconds);
  }

  public static function request($e, $uri)
  {
    ob_start();
    $t_request_start = self::realMicrotime();    
    $_SERVER['REQUEST_URI'] = $uri;

    $e->beginDispatch();
    $t_request_finish = self::realMicrotime();
    $response = ob_get_contents();    
    ob_end_clean();
    $t_elapsed = ($t_request_finish - $t_request_start);    
    $t_elapsed = number_format($t_elapsed, 4);

    //return $response;
    return $t_elapsed;    
  }



}
?>