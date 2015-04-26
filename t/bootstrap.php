<?php

if (!function_exists('loadClass')) {
    function loadClass($class)
    {
        if (strpos($class, 'ESuite') === 0) {
            $base = dirname(realpath(__FILE__));
            
            $class = str_replace('ESuite\\', '', $class);
            $class = str_replace('\\', '/', $class);
            $class_file = $base."/$class.php";
            
            if (!@include($class_file)) {
                echo '[[['.$class_file.']]]';
                throw new \Exception('Could not include class '.$class_file);
            }
        }
    }
    spl_autoload_register('loadClass');
}



//\Empathy\MVC\Util\Lib::addToIncludePath('../../libs');
//class R extends RedBean_Facade{}
//$boot = new Empathy\MVC\Empathy(realpath(dirname(realpath(__FILE__)).'/../'), true);
$suite = new ESuite\Boot();
