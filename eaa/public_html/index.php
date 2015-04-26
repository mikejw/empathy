<?php

if(false == @include('../vendor/autoload.php')) {
    
    include('./site_down.html');
} else {
    
    \Empathy\MVC\Util\Lib::addToIncludePath('../../libs');
    $boot = new Empathy\MVC\Empathy(realpath(dirname(realpath(__FILE__)).'/../'));
}
