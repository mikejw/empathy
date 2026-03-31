<?php

declare(strict_types=1);

use Empathy\MVC\Config;
use Empathy\MVC\Util\Testing\EmpathyApp;

function empathyTestCreateMvc(EmpathyApp $app): \Empathy\MVC\Empathy
{
    $bootstrap = $app->makeFakeBootstrap();

    return $bootstrap->getMVC();
}

function empathyTestChangeEnv(\Empathy\MVC\Empathy $mvc, string $env): void
{
    $boot_options = Config::get('BOOT_OPTIONS');
    $boot_options['environment'] = $env;
    Config::store('BOOT_OPTIONS', $boot_options);
    $mvc->reloadBootOptions();
}

function empathyTestChangeDebug(\Empathy\MVC\Empathy $mvc, bool $debug): void
{
    $boot_options = Config::get('BOOT_OPTIONS');
    $boot_options['debug_mode'] = $debug;
    Config::store('BOOT_OPTIONS', $boot_options);
    $mvc->reloadBootOptions();
}

test('errorHandler collects warnings notices and unknown types', function () {
    $mvc = empathyTestCreateMvc($this->empathy);

    expect($mvc->getErrors())->toBeEmpty();
    expect($mvc->hasErrors())->toBeFalse();
    expect($mvc->errorsToString())->toBeEmpty();

    ob_start();
    $mvc->errorHandler(E_ERROR, 'dummy error', 'someFile.php', 1);
    expect((string) ob_get_clean())->toMatch('/Fatal error/');

    $mvc->errorHandler(E_USER_WARNING, 'dummy error', 'someFile.php', 1);
    expect($mvc->errorsToString())->toMatch('/Warning/');

    $mvc->errorHandler(E_NOTICE, 'dummy error', 'someFile.php', 1);
    expect($mvc->errorsToString())->toMatch('/Notice/');

    $mvc->errorHandler(0, 'dummy error', 'someFile.php', 1);
    expect($mvc->errorsToString())->toMatch('/Error/');
});

test('exceptionHandler outputs dummy error when debug and prior notice', function () {
    $mvc = empathyTestCreateMvc($this->empathy);
    empathyTestChangeDebug($mvc, true);
    $mvc->errorHandler(E_NOTICE, 'dummy error', 'someFile.php', 1);
    ob_start();
    $mvc->exceptionHandler(new \Exception(''));
    expect((string) ob_get_clean())->toMatch('/dummy error/');
});

test('exceptionHandler handles SafeException as bad request when not in dev', function () {
    $mvc = empathyTestCreateMvc($this->empathy);
    empathyTestChangeEnv($mvc, 'prod');
    ob_start();
    $mvc->exceptionHandler(new \Empathy\MVC\SafeException('some error'));
    expect((string) ob_get_clean())->toMatch('/Bad request/');
});

test('exceptionHandler SafeException in dev dies with message', function () {
    $mvc = empathyTestCreateMvc($this->empathy);
    empathyTestChangeEnv($mvc, 'dev');
    ob_start();
    $mvc->exceptionHandler(new \Empathy\MVC\SafeException('some error'));
    expect((string) ob_get_clean())->toMatch('/die: Safe exception: some error/');
});

test('exceptionHandler renders generic exception message in error page', function () {
    $mvc = empathyTestCreateMvc($this->empathy);
    ob_start();
    $mvc->exceptionHandler(new \Exception('some error'));
    expect((string) ob_get_clean())->toMatch('/some error/');
});

test('exceptionHandler RequestException code 0 is not found', function () {
    $mvc = empathyTestCreateMvc($this->empathy);
    ob_start();
    $mvc->exceptionHandler(new \Empathy\MVC\RequestException('some error'));
    expect((string) ob_get_clean())->toMatch('/Not found/');
});

test('exceptionHandler RequestException code 1 is bad request', function () {
    $mvc = empathyTestCreateMvc($this->empathy);
    ob_start();
    $mvc->exceptionHandler(new \Empathy\MVC\RequestException('some error', 1));
    expect((string) ob_get_clean())->toMatch('/Bad request/');
});
