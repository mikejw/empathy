<?php

declare(strict_types=1);

namespace Empathy\MVC\Plugin {

    use Empathy\MVC\DatabasePoolDefaults;
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
                $docRoot = $this->bootstrap->getMVC()->getApplicationPaths()->docRoot;
                if ($docRoot === null || $docRoot === '') {
                    throw new Exception('DOC_ROOT is not set; cannot open sqlite database.');
                }
                $db = $docRoot.'/'.$this->config['database'];
                if (!file_exists($db)) {
                    throw new Exception('sqlite database file not found.');
                }
                if ($this->usingRedbean()) {
                    new \R()->setup('sqlite:'.$db);
                }
            } else {
                $defaults = DatabasePoolDefaults::tryFromConfig();
                if ($defaults === null) {
                    throw new Exception('Database server is not defined in config.');
                }
                // IP check disabled (previously optional strict validation).
                $dsn = $dbms.':host='.$defaults->server.';dbname='.$defaults->databaseName.';';

                if ($defaults->port !== null) {
                    $dsn .= 'port='.$defaults->port.';';
                }
                if ($this->usingRedbean()) {
                    new \R()->setup($dsn, $defaults->user, $defaults->password);
                }
            }
        }
    }
}
