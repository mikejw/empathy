<?php

declare(strict_types=1);

namespace Empathy\MVC;

class LogItem
{
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(private string $msg, private array $context = [], string $class = '', private string $level = 'debug')
    {
        $this->context['app-origin'] = $class === '' ? 'unspecified' : $class;
    }

    public function append(string $key, mixed $value): void
    {
        $this->context[$key] = $value;
    }

    public function setLevel(string $level): void
    {
        $this->level = $level;
    }

    public function setMsg(string $msg): void
    {
        $this->msg = $msg;
    }

    public function fire(): void
    {
        $log = DI::getContainer()->get('LoggingOn') ? DI::getContainer()->get('Log') : false;
        $level = $this->level;
        if ($log !== false) {
            $log->$level($this->msg, $this->context);
        }
    }
}
