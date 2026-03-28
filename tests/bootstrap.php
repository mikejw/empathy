<?php

declare(strict_types=1);

require dirname(__DIR__).'/vendor/autoload.php';

Empathy\MVC\Util\Testing\Util\Config::init();
if (Empathy\MVC\Util\Testing\Util\Config::get('set_test_mode')) {
    define('MVC_TEST_MODE', Empathy\MVC\Util\Testing\Util\Config::get('set_test_mode'));
}
if (Empathy\MVC\Util\Testing\Util\Config::get('set_test_mode_output')) {
    define('MVC_TEST_OUTPUT_ON', Empathy\MVC\Util\Testing\Util\Config::get('set_test_mode_output'));
}
