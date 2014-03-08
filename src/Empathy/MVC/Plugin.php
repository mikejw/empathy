<?php

namespace Empathy\MVC;

class Plugin
{
    protected $bootstrap;
    protected $config;

    public function __construct($b)
    {
        $this->bootstrap = $b;
    }

    public function assignConfig($config)
    {
    	$this->config = json_decode($config, true);
    }


}
