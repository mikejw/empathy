<?php

namespace Empathy\MVC;

class PluginManager
{
    private $plugins;
    private $view_plugins;
    private $initialized;
    private $controller;

    public function __construct()
    {
        $this->initialized = false;
        $this->plugins = array();
        $this->view_plugins = array();
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
            if (in_array('Empathy\MVC\Plugin\Presentation', $r->getInterfaceNames())) {
                $this->view_plugins[] = $p;
            }
        }
    }

    public function getInitialized()
    {
        return $this->initialized;
    }

    public function getView()
    {    
        if (sizeof($this->view_plugins) == 0) {
            throw new \Exception('No plugin loaded for view.');
        } else {
            $module = $this->controller->getModule();
            $class = $this->controller->getClass();

            $plugin = 0;
            if ($module == 'api') {
                $plugin = 1;
            }

            $view = $this->view_plugins[$plugin];

            return $view;
        }
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

}
