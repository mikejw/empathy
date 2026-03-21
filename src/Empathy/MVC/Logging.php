<?php

declare(strict_types=1);

namespace Empathy\MVC;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Logging
{
    private readonly Logger $log;

    public function __construct(int $level = Logger::DEBUG)
    {
        $logPath = Config::get('DOC_ROOT') . '/logs';
        $logFile = $logPath . '/main.log';
        $this->log = new Logger('default');
        $monologLevel = match ($level) {
            Logger::DEBUG,
            Logger::INFO,
            Logger::NOTICE,
            Logger::WARNING,
            Logger::ERROR,
            Logger::CRITICAL,
            Logger::ALERT,
            Logger::EMERGENCY => $level,
            default => throw new \InvalidArgumentException('Invalid Monolog level: '.$level),
        };
        $this->log->pushHandler(new StreamHandler($logFile, $monologLevel));
    }

    public function getLog(): Logger
    {
        return $this->log;
    }
}
