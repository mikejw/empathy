<?php

namespace Empathy\Plugin;
use Empathy\Plugin as Plugin;

class Pheanstalk
{

  public function __construct()
  {   
    require('pheanstalk/pheanstalk_init.php');
  }


}
?>