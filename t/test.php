<?php

// if(false == @include('../vendor/autoload.php')) {

// } else {
//     require('bootstrap.php');

//     \Empathy\MVC\Testable::header('Cache-Control: no-cache, must-revalidate');
    
//     $m = new ESuite\Fake\Message();
//     //$new = $m->withHeader('Cache-Control', 'no-cache, must-revalidate');



//     echo '<pre>';
//     print_r($m->getHeaders());
//     //print_r($_SERVER);
//     echo '</pre>';
// }


if(false == @include('../vendor/autoload.php')) {

} else {
    \Empathy\MVC\Testable::header('Cache-Control: no-cache, must-revalidate');
    //header('foo: bar');
    echo '<pre>';
    //print_r($m->getHeaders());
    print_r(headers_list());
    echo '</pre>';

}