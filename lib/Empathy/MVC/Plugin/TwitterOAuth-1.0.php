<?php

// uses abraham-twitteroauth
// https://github.com/abraham/twitteroauth

namespace Empathy\MVC\Plugin;
use Empathy\MVC\Plugin as Plugin;

class TwitterOAuth
{

  public function __construct()
  {
    require 'twitteroauth/twitteroauth.php';
  }

}
