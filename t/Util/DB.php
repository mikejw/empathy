<?php

namespace ESuite\Util;

use Empathy\MVC\Config as EmpConfig;

class DB
{
    private static $dbh;
    private static $db_criteria;


    public static function getDefDBCreds()
    {
        return array(
            'db_host' => '127.0.0.1',
            'db_name' => 'etest',
            'db_user' => 'root',
            'db_pass' => '',
            'db_port' => 3306
        );
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


    private static function connect()
    {
        self::$dbh = new \PDO('mysql:host='.EmpConfig::get('DB_SERVER'),
            EmpConfig::get('DB_USER'), EmpConfig::get('DB_PASS'));
    }

    public static function reset($db_name = NULL)
    {
        if ($db_name === NULL) {
            $db_name = EmpConfig::get('DB_NAME').'.sql';
        }
        self::create(EmpConfig::get('DB_NAME'));
        self::load($db_name);
    }

    private static function create($name)
    {
        if (self::$dbh === NULL) {
            self::connect();
        }

        $sql = 'DROP DATABASE IF EXISTS '.$name.'; CREATE DATABASE '.$name.';';      
        $result = self::$dbh->query($sql);
    }

    private static function load($file)
    {
        $exec = Config::get('mysql').' -u '.EmpConfig::get('DB_USER').' --password=\''.EmpConfig::get('DB_PASS').'\' '
            .EmpConfig::get('DB_NAME').' < '.$file;
        exec($exec);
        //echo $exec;
    }




    // not used as yet
    private static function getCriteria()
    {
        self::$db_criteria = array(
            'host' => Config::get('db_host'),
            'username' => Config::get('db_user'),
            'password' => Config::get('db_pass'),
            'database' => Config::get('db_name')
            );
    }



}