<?php

// uses abraham-twitteroauth
// https://github.com/abraham/twitteroauth

namespace Empathy\Plugin;
use Empathy\Plugin as Plugin;

class TwitterOAuth
{

  public function __construct()
  {
    require('twitteroauth/twitteroauth.php');
  }

  


}
?>