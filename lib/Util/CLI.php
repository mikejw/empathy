<?php

namespace Empathy\Util;

/* based on Util/Test.php
   (which is currently outside of namespacing) 
*/

class CLI
{
  public static function request($e, $uri)
  {
    ob_start();
    $t_request_start = microtime();    
    $_SERVER['REQUEST_URI'] = $uri;

    $e->beginDispatch();
    $t_request_finish = microtime();
    $response = ob_get_contents();    
    ob_end_clean();
    $t_elapsed = ($t_request_finish - $t_request_start);
    return $t_elapsed;
  }



}
?>