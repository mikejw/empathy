<?php

declare(strict_types=1);

namespace Empathy\MVC;

class LogItem
{
    private string $msg;

    /** @var array<string, mixed> */
    private array $context = [];

    private string $level;

    /**
     * @param array<string, mixed> $context
     */
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
