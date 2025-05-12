<?php

namespace Empathy\MVC;
use Empathy\MVC\PluginManager\Option;

/**
 * Empathy PluginManager
 * @file            Empathy/MVC/PluginManager.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class PluginManager
{
    private $plugins;
    private $initialized;
    private $controller;
    private $options = [];
    private $whitelist = [];
    private $view;

    const DEF_WHITELIST_LIST = [
        'ELibs',
        'Smarty',
        'SmartySSL',
        'JSONView',
        'EDefault'
    ];


    public function __construct()
    {
        $this->initialized = false;
        $this->plugins = array();
        $this->view_plugins = array();
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function setWhitelist($whitelist)
    {
        if (in_array(Option::DefaultWhitelist, $this->options)) {
            $whitelist = array_merge($whitelist, self::DEF_WHITELIST_LIST);
        }
        $this->whitelist = $whitelist;
    }


    public function setController($c)
    {
        $this->controller = $c;
    }

    public function init()
    {
        $this->initialized = true;
    }

    public function register($p)
    {
        $this->plugins[] = $p;
    }

    public function preDispatch()
    {
        foreach ($this->plugins as $p) {
            $r = new \ReflectionClass(get_class($p));
            if (in_array('Empathy\MVC\Plugin\PreDispatch', $r->getInterfaceNames())) {
                $p->onPreDispatch();
            }
        }
    }

    public function preEvent()
    {
        foreach ($this->plugins as $p) {
            $r = new \ReflectionClass(get_class($p));
            if (in_array('Empathy\MVC\Plugin\PreEvent', $r->getInterfaceNames())) {
                $p->onPreEvent();
            }
        }
    }

    public function attemptSetView($p)
    {
        $r = new \ReflectionClass(get_class($p));
        if (in_array('Empathy\MVC\Plugin\Presentation', $r->getInterfaceNames())) { 
            $this->view = $p;
        }
    }

    public function getView()
    {
        return $this->view;
    }

    public function setView($view)
    {
        $this->view = $view;
    }

    public function getInitialized()
    {
        return $this->initialized;
    }

    public function eLibsTestMode()
    {

        $mode = false;
        foreach ($this->plugins as $p) {
            if (get_class($p) == 'Empathy\MVC\Plugin\ELibs') {
                $c = $p->getConfig();
                if (isset($c['testing']) && $c['testing']) {
                    $mode = true;
                }
                break;
            }
        }
        
        return $mode;
    }

    public function getWhitelist()
    {
        return $this->whitelist;
    }

}
