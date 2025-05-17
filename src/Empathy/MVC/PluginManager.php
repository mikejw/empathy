<?php

namespace Empathy\MVC;
use Empathy\MVC\PluginManager\Option;
use Empathy\MVC\DI;

/**
 * Empathy PluginManager
 * @file            Empathy/MVC/PluginManager.php
 * @description
 * @author          Michael J. Whiting
 * @license         See LICENCE
 *
 * (c) copyright Michael J. Whiting

 * with this source code in the file LICENSE
 */
class PluginManager
{
    private $initialized;
    private $controller;
    private $options = [];
    private $whitelist = [];
    private $plugins = [];
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
        DI::getContainer()->set(get_class($p), $p);
    }

    public function preDispatch($p)
    {
        $r = new \ReflectionClass(get_class($p));
        if (in_array('Empathy\MVC\Plugin\PreDispatch', $r->getInterfaceNames())) {
            $p->onPreDispatch();
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
            if ($p->getGlobal()) {
                $this->setView($p);
            }
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

    public function find($names = []) {
        foreach ($names as $name) {
            if (count(explode('\\', $name)) === 1) {
                $name = 'Empathy\\MVC\\Plugin\\' . $name;
            }
            try {
                return DI::getContainer()->get($name);
            } catch (\Exception $e) {
                if (get_class($e) == 'DI\Definition\Exception\InvalidDefinition') {
                    continue;
                } else {
                    throw $e;
                }
            }
        }
        throw new \Exception('Could not find plugin in list: '. implode(', ', $names) . '.');
    }
}
