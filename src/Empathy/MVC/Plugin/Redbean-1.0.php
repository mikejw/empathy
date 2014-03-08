<?php


namespace {
    class R extends RedBean_Facade{} 
}

namespace Empathy\MVC\Plugin {

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
            if (!defined('DB_SERVER')) {
                throw new \Empathy\MVC\Exception('Database server is not defined in config.');
            } else {
                if (!$this->isIP(DB_SERVER)) {
                    throw new \Empathy\MVC\Exception('Database server must be an IP address.');
                } else {
                    
                    $dbms = (isset($this->config['dbms']))? $this->config['dbms']: 'mysql';
                    $dsn = $dbms.':host='.DB_SERVER.';dbname='.DB_NAME.';';
                    if(defined('DB_PORT') && is_numeric(DB_PORT)) {
                        $dsn .= 'port='.DB_PORT.';';
                    }                    
                    \R::setup($dsn, DB_USER, DB_PASS);
                }
            }           
        }
    }
}
