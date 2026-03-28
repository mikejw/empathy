<?php

declare(strict_types=1);

use Empathy\MVC\Config;
use Empathy\MVC\DBPool;
use Empathy\MVC\Util\Testing\Util\DB;

beforeEach(function () {
    DB::loadDefDBCreds();
});

test('DBPool returns PDO and recovers after reset with DB_PORT disabled', function () {
    expect(DBPool::getDefCX())->toBeInstanceOf(\PDO::class);
    DBPool::reset();
    Config::store('DB_PORT', false);
    expect(DBPool::getDefCX())->toBeInstanceOf(\PDO::class);
});
