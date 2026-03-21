<?php

declare(strict_types=1);


include(realpath(__DIR__.'/../vendor/autoload.php'));

if (!function_exists('loadClass')) {
    function loadClass($class)
    {
        if (str_starts_with($class, 'ESuite')) {
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
    spl_autoload_register(loadClass(...));
}



ESuite\Util\Config::init();
if (ESuite\Util\Config::get('set_test_mode')) {
    define('MVC_TEST_MODE', ESuite\Util\Config::get('set_test_mode'));
}
if (ESuite\Util\Config::get('set_test_mode_output')) {
    define('MVC_TEST_OUTPUT_ON', ESuite\Util\Config::get('set_test_mode_output'));
}
