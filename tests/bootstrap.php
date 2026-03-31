<?php

declare(strict_types=1);
use Empathy\MVC\Util\Testing\Util\Config;

require dirname(__DIR__).'/vendor/autoload.php';

$base = realpath(__DIR__);
if ($base === false) {
    throw new \RuntimeException('Could not resolve tests bootstrap path.');
}
Config::init($base);
if (Config::get('set_test_mode')) {
    define('MVC_TEST_MODE', Config::get('set_test_mode'));
}
if (Config::get('set_test_mode_output')) {
    define('MVC_TEST_OUTPUT_ON', Config::get('set_test_mode_output'));
}
