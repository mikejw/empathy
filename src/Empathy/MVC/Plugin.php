<?php

namespace Empathy\MVC;

class Plugin
{
    protected $config;
    
    public function assignConfig($config)
    {
        $this->config = json_decode($config, true);
    }
}
