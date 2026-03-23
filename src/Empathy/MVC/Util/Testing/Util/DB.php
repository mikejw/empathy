<?php

declare(strict_types=1);

namespace Empathy\MVC\Util\Testing\Util;

use Empathy\MVC\Config as EmpConfig;
use Empathy\MVC\SafeException;

class DB
{
    private static $dbh;
    private static $db_criteria;

    public static function getDefDBCreds()
    {
        return [
            'db_host' => '127.0.0.1',
            'db_name' => 'etest',
            'db_user' => 'root',
            'db_pass' => 'example',
            'db_port' => 3306,
        ];
    }

    public static function loadDefDBCreds()
    {
        $creds = self::getDefDBCreds();
        EmpConfig::store('DB_SERVER', $creds['db_host']);
        EmpConfig::store('DB_NAME', $creds['db_name']);
        EmpConfig::store('DB_USER', $creds['db_user']);
        EmpConfig::store('DB_PASS', $creds['db_pass']);
        EmpConfig::store('DB_PORT', $creds['db_port']);
    }

    public static function create($name)
    {
        if (self::$dbh === null) {
            self::connect();
        }

        $sql = 'DROP DATABASE IF EXISTS '.$name.'; CREATE DATABASE '.$name.';';
        self::$dbh->query($sql);
    }

    public static function reset($file = null)
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

    private static function connect()
    {
        self::$dbh = new \PDO(
            'mysql:host='.EmpConfig::get('DB_SERVER'),
            EmpConfig::get('DB_USER'),
            EmpConfig::get('DB_PASS')
        );
    }

    private static function load($file)
    {
        $exec = Config::get('mysql').' -u '.EmpConfig::get('DB_USER').' --password=\''.EmpConfig::get('DB_PASS').'\' '
            .'-h '.EmpConfig::get('DB_SERVER').' '
            .EmpConfig::get('DB_NAME').' < '.$file;
        exec($exec);
    }
}
