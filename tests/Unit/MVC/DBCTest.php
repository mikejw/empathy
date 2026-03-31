<?php

declare(strict_types=1);

use Empathy\MVC\DBC;
use Empathy\MVC\Util\Testing\Util\DB;

beforeEach(function () {
    DB::loadDefDBCreds();
    DB::reset();

    $creds = DB::getDefDBCreds();
    $this->dbc = new DBC(
        $creds['db_host'],
        $creds['db_name'],
        $creds['db_user'],
        $creds['db_pass'],
        $creds['db_port']
    );
});

test('DBC exposes a PDO handle', function () {
    $dbc = $this->dbc;
    assert($dbc instanceof \Empathy\MVC\DBC);
    expect($dbc->getHandle())->toBeInstanceOf(\PDO::class);
});
