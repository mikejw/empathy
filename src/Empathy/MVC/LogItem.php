<?php

namespace Empathy\MVC;
use Empathy\MVC\DI;

class LogItem
{
    private $msg;
    private $context = array();
    private $level;

    public function __construct($msg, $context = array(),  $class = '', $level = 'debug',)
    {
        $this->msg = $msg;
        $this->context = $context;
        $this->level = $level;

        if ($class === '') {
            $this->context['app-origin'] = 'unspecified';
        } else {
            $this->context['app-origin'] = $class;
        }
        return $this;
    }

    public function append($key, $value) {
        $this->context[$key] = $value;
    }

    public function setLevel($level) {
        $this->level = $level;
    }

    public function setMsg($msg) {
        $this->msg = $msg;
    }

    public function fire() {
        $log = DI::getContainer()->get('LoggingOn') ? DI::getContainer()->get('Log') : false;
        $level = $this->level;
        if ($log !== false) {
            $log->$level($this->msg, $this->context);
        }
    }
}
