<?php
date_default_timezone_set('Europe/London');
include('spyc/spyc.php');
$s = new Spyc();
$config = $s->YAMLLoad('../config.yml');      

foreach($config as $index => $item)
  {
    if(!is_array($item))
      {
	define(strtoupper($index), $item);
      }
  }

?>