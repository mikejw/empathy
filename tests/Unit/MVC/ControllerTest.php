<?php

declare(strict_types=1);

beforeEach(function () {
    $this->bootstrap = $this->empathy->makeFakeBootstrap();
    $this->controller = new \Empathy\MVC\Controller($this->bootstrap);
});

test('controller is a Controller instance', function () {
    expect($this->controller)->toBeInstanceOf(\Empathy\MVC\Controller::class);
});

test('redirect sets header in test mode', function () {
    $this->expectOutputRegex('/Setting header/');
    $this->controller->redirect('foobar');
});

test('redirect_cgi sets header in test mode', function () {
    $this->expectOutputRegex('/Setting header/');
    $this->controller->redirect_cgi('cgiscript.pl');
});

test('sessionDown unsets and destroys session in test mode', function () {
    $this->expectOutputRegex('/session unset/');
    $this->expectOutputRegex('/session destroy/');
    $this->controller->sessionDown();
});

test('isXMLHttpRequest reflects X-Requested-With header', function () {
    expect($this->controller->isXMLHttpRequest())->toBeFalse();
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
    expect($this->controller->isXMLHttpRequest())->toBeTrue();
});

test('initID validates integer id from request', function () {
    $_GET['id'] = 100;
    expect($this->controller->initID('id', 1))->toBeTrue();

    $_GET['id'] = 100.1;
    expect($this->controller->initID('id', 1))->toBeFalse();

    expect($this->controller->initID('some_id', 1, true))->toBeFalse();
});
