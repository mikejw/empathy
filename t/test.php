<?php


if(false == @include('../vendor/autoload.php')) {

} else {
    require('bootstrap.php');
    $m = new ESuite\Fake\Message();
    echo '<pre>';
    print_r($m->getHeaders());
    echo '</pre>';
}

