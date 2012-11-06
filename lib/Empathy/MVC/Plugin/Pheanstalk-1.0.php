<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin;

class Pheanstalk-1.0
{

    public function __construct()
    {
        require 'pheanstalk/pheanstalk_init.php';
    }

}
