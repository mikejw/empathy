<?php

declare(strict_types=1);

namespace Empathy\MVC\Util\Testing\Util;

use Empathy\MVC\Config as EmpConfig;
use Empathy\MVC\SafeException;

class DB
{
    private static ?\PDO $dbh = null;

    /**
     * @return array{db_host: string, db_name: string, db_user: string, db_pass: string, db_port: int}
     */
    public static function getDefDBCreds(): array
    {
        return [
            'db_host' => '127.0.0.1',
            'db_name' => 'etest',
            'db_user' => 'root',
            'db_pass' => 'example',
            'db_port' => 3306,
        ];
    }

    public static function loadDefDBCreds(): void
    {
        $creds = self::getDefDBCreds();
        EmpConfig::store('DB_SERVER', $creds['db_host']);
        EmpConfig::store('DB_NAME', $creds['db_name']);
        EmpConfig::store('DB_USER', $creds['db_user']);
        EmpConfig::store('DB_PASS', $creds['db_pass']);
        EmpConfig::store('DB_PORT', $creds['db_port']);
    }

    public static function create(string $name): void
    {
        if (self::$dbh === null) {
            self::connect();
        }
        if (self::$dbh === null) {
            throw new \RuntimeException('Database connection not initialized');
        }

        $sql = 'DROP DATABASE IF EXISTS '.$name.'; CREATE DATABASE '.$name.';';
        self::$dbh->query($sql);
    }

    public static function reset(?string $file = null): void
    {
        if ($file === null) {
            $file = 'fixtures/' . EmpConfig::get('DB_NAME').'.sql';
        }
        self::create(EmpConfig::get('DB_NAME'));
        $reset = Config::get('util_dir') . '/' . $file;

        if (!file_exists($reset)) {
            throw new SafeException('Reset file ' . $reset . ' does not exist.');
        }
        self::load($reset);
    }

    private static function connect(): void
    {
        self::$dbh = new \PDO(
            'mysql:host='.EmpConfig::get('DB_SERVER'),
            EmpConfig::get('DB_USER'),
            EmpConfig::get('DB_PASS')
        );
    }

    private static function load(string $file): void
    {
        $exec = Config::get('mysql').' -u '.EmpConfig::get('DB_USER').' --password=\''.EmpConfig::get('DB_PASS').'\' '
            .'-h '.EmpConfig::get('DB_SERVER').' '
            .EmpConfig::get('DB_NAME').' < '.$file;
        exec($exec);
    }
}
