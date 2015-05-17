<?php

if(false == @include('../vendor/autoload.php')) {
    
    include('./site_down.html');
} else {
    $boot = new Empathy\MVC\Empathy(realpath(dirname(realpath(__FILE__)).'/../'));
}
