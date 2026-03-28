<?php

declare(strict_types=1);

beforeEach(function () {
    $this->bootstrap = $this->empathy->makeFakeBootstrap();
});

test('bootstrap is a Bootstrap instance', function () {
    expect($this->bootstrap)->toBeInstanceOf(\Empathy\MVC\Bootstrap::class);
});

test('dispatch throws RequestException for unknown route', function () {
    $this->expectException(\Empathy\MVC\RequestException::class);
    $_SERVER['HTTP_HOST'] = 'www.dev.org';
    $_SERVER['REQUEST_URI'] = '/foo';
    $this->bootstrap->dispatch();
});

test('dispatchException renders html for generic exception', function () {
    $this->expectOutputRegex('/html/');
    $this->bootstrap->dispatchException(new \Exception());
});
