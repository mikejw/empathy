<?php

namespace Empathy\MVC;

class Plugin
{
    protected $config;
    

    public function __construct($config = NULL)
    {
        if ($config !== NULL) {
            $this->assignConfig($config);
        }
    }


    public function assignConfig($config)
    {
        $this->config = json_decode($config, true);        
    }


    public function getConfig()
    {
        return $this->config;
    }

}
