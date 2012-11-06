<?php

namespace Empathy\MVC;

class Plugin
{
    protected $bootstrap;

    public function __construct($b)
    {
        $this->bootstrap = $b;
    }

}
