<?php
// plugin to main controller index.php
// force_endslash.php
// URL does not end in a forward slash initiate redirect

if(!(ereg('\/$', $_SERVER['REQUEST_URI'])))
{
  $location = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'/';
  header('Location: '.$location);
  exit();
}
?>