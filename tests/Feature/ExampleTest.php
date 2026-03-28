<?php

test('example', function () {
    expect(true)->toBeTrue();
});

test('empathy fake bootstrap is available via Tests\\TestCase', function () {
    $bootstrap = $this->empathy->makeFakeBootstrap();

    expect($bootstrap)->toBeInstanceOf(\Empathy\MVC\Bootstrap::class);
});
