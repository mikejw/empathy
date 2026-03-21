<?php

declare(strict_types=1);

namespace Empathy\MVC;

class LogItem
{
    private string $msg;
    private array $context = [];
    private string $level;

    public function __construct(string $msg, array $context = [], string $class = '', string $level = 'debug')
    {
        $this->msg = $msg;
        $this->context = $context;
        $this->level = $level;

        if ($class === '') {
            $this->context['app-origin'] = 'unspecified';
        } else {
            $this->context['app-origin'] = $class;
        }
    }

    public function append($key, $value): void
    {
        $this->context[$key] = $value;
    }

    public function setLevel($level): void
    {
        $this->level = $level;
    }

    public function setMsg($msg): void
    {
        $this->msg = $msg;
    }

    public function fire()
    {
        $log = DI::getContainer()->get('LoggingOn') ? DI::getContainer()->get('Log') : false;
        $level = $this->level;
        if ($log !== false) {
            $log->$level($this->msg, $this->context);
        }
    }
}
