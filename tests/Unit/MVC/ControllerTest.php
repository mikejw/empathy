<?php

declare(strict_types=1);

use Empathy\MVC\Controller;

beforeEach(function () {
    $this->bootstrap = $this->empathy->makeFakeBootstrap();
    $this->controller = new Controller($this->bootstrap);
});

test('controller is a Controller instance', function () {
    $controller = $this->controller;
    assert($controller instanceof Controller);
    expect($controller)->toBeInstanceOf(Controller::class);
});

test('redirect sets header in test mode', function () {
    $controller = $this->controller;
    assert($controller instanceof Controller);
    ob_start();
    $controller->redirect('foobar');
    expect((string) ob_get_clean())->toMatch('/Setting header/');
});

test('redirect_cgi sets header in test mode', function () {
    $controller = $this->controller;
    assert($controller instanceof Controller);
    ob_start();
    $controller->redirect_cgi('cgiscript.pl');
    expect((string) ob_get_clean())->toMatch('/Setting header/');
});

test('sessionDown unsets and destroys session in test mode', function () {
    $controller = $this->controller;
    assert($controller instanceof Controller);
    ob_start();
    $controller->sessionDown();
    $output = (string) ob_get_clean();
    expect($output)->toMatch('/session unset/');
    expect($output)->toMatch('/session destroy/');
});

test('isXMLHttpRequest reflects X-Requested-With header', function () {
    $controller = $this->controller;
    assert($controller instanceof Controller);
    expect($controller->isXMLHttpRequest())->toBeFalse();
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
    expect($controller->isXMLHttpRequest())->toBeTrue();
});

test('initID validates integer id from request', function () {
    $controller = $this->controller;
    assert($controller instanceof Controller);
    $_GET['id'] = 100;
    expect($controller->initID('id', 1))->toBeTrue();

    $_GET['id'] = 100.1;
    expect($controller->initID('id', 1))->toBeFalse();

    expect($controller->initID('some_id', 1, true))->toBeFalse();
});
