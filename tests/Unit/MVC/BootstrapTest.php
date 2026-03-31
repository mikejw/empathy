<?php

declare(strict_types=1);

beforeEach(function () {
    $this->bootstrap = $this->empathy->makeFakeBootstrap();
});

test('bootstrap is a Bootstrap instance', function () {
    $bootstrap = $this->bootstrap;
    assert($bootstrap instanceof \Empathy\MVC\Bootstrap);
    expect($bootstrap)->toBeInstanceOf(\Empathy\MVC\Bootstrap::class);
});

test('dispatch throws RequestException for unknown route', function () {
    $bootstrap = $this->bootstrap;
    assert($bootstrap instanceof \Empathy\MVC\Bootstrap);

    expect(function () use ($bootstrap): void {
        $_SERVER['HTTP_HOST'] = 'www.dev.org';
        $_SERVER['REQUEST_URI'] = '/foo';
        $bootstrap->dispatch();
    })->toThrow(\Empathy\MVC\RequestException::class);
});

test('dispatchException renders html for generic exception', function () {
    $bootstrap = $this->bootstrap;
    assert($bootstrap instanceof \Empathy\MVC\Bootstrap);

    ob_start();
    $bootstrap->dispatchException(new \Exception());
    $output = (string) ob_get_clean();

    expect($output)->toMatch('/html/');
});
