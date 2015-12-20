<?php

namespace Empathy\MVC;

/**
 * Empathy Plugin class
 * @file            Empathy/MVC/Plugin.php
 * @description     
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
class Plugin
{
    protected $bootstrap;
    protected $config;
    protected $manager;
    

    public function __construct($manager, $bootstrap, $config = NULL)
    {
        $this->bootstrap = $bootstrap;
        $this->manager = $manager;
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
