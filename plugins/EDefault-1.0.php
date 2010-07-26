<?php

namespace Empathy\Plugin;
use Empathy\Plugin as Plugin;

class EDefault extends Plugin implements PreDispatch
{
  public function __construct()
  {
    //
  }

  public function onPreDispatch()
  {    
    date_default_timezone_set('Europe/London');
    header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
  }
}
?>