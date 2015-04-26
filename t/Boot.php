<?php

namespace ESuite;

class Boot
{
    private $dbh;
    private $db_criteria;


    private static function dummyConfig()
    {
        // what's the dumbest configuaration that can be used?
        // (using architype dir as dummy project base dir)
        define('WEB_ROOT', 'foo');
        define('PUBLIC_DIR', 'bar');
        define('DOC_ROOT', realpath(dirname(realpath(__FILE__)).'/../eaa/'));
    }



    public function __construct()
    {
        Config::init();

        $this->getCriteria();     
        //$this->dbConnect();

        if (Config::get('reset_db')) {
            $this->dbReset();
        }
        if(Config::get('set_test_mode')) {
            define('MVC_TEST_MODE', true);
        }


        self::dummyConfig();
    }

    private function getCriteria()
    {
        $this->db_criteria = array(
            'host' => Config::get('db_host'),
            'username' => Config::get('db_user'),
            'password' => Config::get('db_pass'),
            'database' => Config::get('db_name')
            );
    }

    private function dbConnect()
    {
        $this->dbh = new \PDO('mysql:host='.$this->db_criteria['host'],
                              $this->db_criteria['username'], $this->db_criteria['password']);
    }

    public function dbReset()
    {
        $this->create_db($this->db_criteria['database']);
        $this->loadDump($this->db_criteria['database'].'.sql');
    }

    private function create_db($name)
    {
        $sql = 'DROP DATABASE IF EXISTS '.$name.'; CREATE DATABASE '.$name.';';      
        $result = $this->dbh->query($sql);
    }

    private function loadDump($file)
    {
        $exec = Config::get('mysql').' -u '.$this->db_criteria['username'].' --password=\''.$this->db_criteria['password'].'\' '
            .$this->db_criteria['database'].' < ../setup.sql';
        exec($exec);
    }

}