<?php

declare(strict_types=1);

use Empathy\MVC\DI;

beforeEach(function () {
    $this->boot = $this->empathy->makeFakeBootstrap();
});

function initMvcTestUri(string $hostString): void
{
    unset($_GET['module'], $_GET['class'], $_GET['event'], $_GET['id']);
    $_SERVER['HTTP_HOST'] = 'www.dev.org';
    $_SERVER['REQUEST_URI'] = $hostString;
    DI::getContainer()->get('URI');
}

test('default URI maps to front module', function () {
    initMvcTestUri('/');
    expect($_GET['module'])->toBe('front');
    expect($_GET['class'])->toBe('front');
    expect($_GET['event'])->toBe('default_event');
});

test('blog item URI parses module class event and id', function () {
    initMvcTestUri('/blog/item/21');
    expect($_GET['module'])->toBe('blog');
    expect($_GET['class'])->toBe('blog');
    expect($_GET['event'])->toBe('item');
    expect($_GET['id'])->toEqual(21);
});
