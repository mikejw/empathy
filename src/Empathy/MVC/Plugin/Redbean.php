<?php

declare(strict_types=1);

namespace Empathy\MVC\Plugin {

    use Empathy\MVC\Config;
    use Empathy\MVC\Exception;
    use Empathy\MVC\Plugin;

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
    class Redbean extends Plugin implements PreDispatch
    {
        private function usingRedbean(): bool
        {
            return class_exists(\R::class);
        }

        public function onPreDispatch(): void
        {
            $dbms = $this->config['dbms'] ?? 'mysql';

            if ($dbms === 'sqlite') {
                if (!isset($this->config['database'])) {
                    throw new Exception('sqlite database file not supplied.');
                }
                $db = Config::get('DOC_ROOT') . '/' . $this->config['database'];
                if (!file_exists($db)) {
                    throw new Exception('sqlite database file not found.');
                }
                if ($this->usingRedbean()) {
                    new \R()->setup('sqlite:'.$db);
                }
            } else {
                if (Config::get('DB_SERVER') === false) {
                    throw new \Empathy\MVC\Exception('Database server is not defined in config.');
                }
                // IP check disabled (previously optional strict validation).
                $dsn = $dbms.':host='.Config::get('DB_SERVER').';dbname='.Config::get('DB_NAME').';';

                $db_port = Config::get('DB_PORT');
                if (is_numeric($db_port)) {
                    $dsn .= 'port='.$db_port.';';
                }
                if ($this->usingRedbean()) {
                    new \R()->setup($dsn, Config::get('DB_USER'), Config::get('DB_PASS'));
                }
            }
        }
    }
}
