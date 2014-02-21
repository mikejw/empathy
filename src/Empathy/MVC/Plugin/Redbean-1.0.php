<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin,
    RedBean_Facade as R;

class Redbean extends Plugin implements PreDispatch
{

    public function __construct()
    {
        //
    }

    private function isIP($server)
    {
        $ip = false;
        $count = 0;
        $stripped = str_replace('.', '', DB_SERVER, $count);
        if ($count) {
            if (is_numeric($stripped)) {
                $ip = true;
            }
        }

        return $ip;
    }

    public function onPreDispatch()
    {
        if (!$this->isIP(DB_SERVER)) {
            throw new \Empathy\Exception('Database server must be an IP address.');
        }

        $dsn = 'mysql:host='.DB_SERVER.';dbname='.DB_NAME.';';
        if(defined('DB_PORT') && is_numeric(DB_PORT)) {
            $dsn .= 'port='.DB_PORT.';';
        }
        R::setup($dsn, DB_USER, DB_PASS);
    }
}

