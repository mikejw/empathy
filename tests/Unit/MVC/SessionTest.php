<?php

declare(strict_types=1);

use Empathy\MVC\Session;

test('loadUIVars does not persist in test mode', function () {
    $_GET['bar'] = 'baz';
    Session::up();
    Session::loadUIVars('foo', ['bar']);

    expect(Session::getUISetting('foo', 'bar'))->toBeFalse();
});
