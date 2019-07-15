<?php


namespace {

    class R extends RedBeanPHP\Facade
    {
    }
}

namespace Empathy\MVC\Plugin {

    use Empathy\MVC\Config;

    /**
     * Empathy Redbean Plugin
     * @file            Empathy/MVC/Plugin/Redbean.php
     * @description
     * @author          Mike Whiting
     * @license         See LICENCE
     *
     * (c) copyright Mike Whiting

     * with this source code in the file licence.txt
     */
    class Redbean extends \Empathy\MVC\Plugin implements PreDispatch
    {
        
        private function isIP($server)
        {
            $ip = false;
            $count = 0;
            $stripped = str_replace('.', '', Config::get('DB_SERVER'), $count);
            if ($count) {
                if (is_numeric($stripped)) {
                    $ip = true;
                }
            }
            return $ip;
        }
        
        public function onPreDispatch()
        {
            $dbms = (isset($this->config['dbms']))? $this->config['dbms']: 'mysql';

            if ($dbms == 'sqlite') {
                if (!isset($this->config['database'])) {
                    throw new \Empathy\MVC\Exception('sqlite database file not supplied.');
                }
                $db = DOC_ROOT.'/'.$this->config['database'];
                if (!file_exists($db)) {
                    throw new \Empathy\MVC\Exception('sqlite database file not found.');
                }
                \R::setup('sqlite:'.$db);
            } else {
                if (Config::get('DB_SERVER') === false) {
                    throw new \Empathy\MVC\Exception('Database server is not defined in config.');
                }
                // disable IP check
                if (false && !$this->isIP(Config::get('DB_SERVER'))) {
                    throw new \Empathy\MVC\Exception('Database server must be an IP address.');
                }
                $dsn = $dbms.':host='.Config::get('DB_SERVER').';dbname='.Config::get('DB_NAME').';';

                $db_port = Config::get('DB_PORT');
                if (is_numeric($db_port)) {
                    $dsn .= 'port='.$db_port.';';
                }
                \R::setup($dsn, Config::get('DB_USER'), Config::get('DB_PASS'));
            }
        }
    }
}
