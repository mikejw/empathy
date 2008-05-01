<?php
// plugin to main controller index.php
// force_www.php
// URL does not begin 'www' initiate redirect


if(!(ereg('^www', $_SERVER['HTTP_HOST'])))
{
  $location = "http://".WEB_ROOT.$_SERVER['REQUEST_URI'];
  header("Location: $location");
  exit();
}



?>