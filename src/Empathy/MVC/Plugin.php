<?php

namespace Empathy\MVC;

/**
 * Empathy Plugin class
 * @file            Empathy/MVC/Plugin.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class Plugin
{
    protected $bootstrap;
    protected $config;
    protected $manager;
    

    public function __construct($manager, $bootstrap, $config = null)
    {
        $this->bootstrap = $bootstrap;
        $this->manager = $manager;
        if ($config !== null) {
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
