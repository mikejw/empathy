<?php

namespace Empathy\MVC;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Logging
{
    private $log;

    public function __construct($level = Logger::DEBUG)
    {
        try {
            $logPath = Config::get('DOC_ROOT') . '/logs';
            $logFile = $logPath . '/main.log';
            $this->log = new Logger('default');
            $this->log->pushHandler(new StreamHandler($logFile, $level));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getLog()
    {
        return $this->log;
    }
}
