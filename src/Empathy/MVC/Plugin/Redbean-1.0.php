<?php


namespace {
    class R extends RedBeanPHP\Facade{} 
}

namespace Empathy\MVC\Plugin {

    /**
     * Empathy Redbean Plugin
     * @file            Empathy/MVC/Plugin/Redbean.php
     * @description     
     * @author          Mike Whiting
     * @license         LGPLv3
     *
     * (c) copyright Mike Whiting
     * This source file is subject to the LGPLv3 License that is bundled
     * with this source code in the file licence.txt
     */
    class Redbean extends \Empathy\MVC\Plugin implements PreDispatch
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

                if (!defined('DB_SERVER')) {
                    throw new \Empathy\MVC\Exception('Database server is not defined in config.');
                }
                if (!$this->isIP(DB_SERVER)) {
                    throw new \Empathy\MVC\Exception('Database server must be an IP address.');
                }
                $dsn = $dbms.':host='.DB_SERVER.';dbname='.DB_NAME.';';
                if(defined('DB_PORT') && is_numeric(DB_PORT)) {
                    $dsn .= 'port='.DB_PORT.';';
                }                    
                \R::setup($dsn, DB_USER, DB_PASS);                
            }
        }
    }
}
