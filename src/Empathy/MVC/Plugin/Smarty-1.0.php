<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Config;
use Empathy\MVC\Plugin as Plugin;

class Smarty extends Plugin implements PreDispatch, Presentation
{
    private $smarty;


    public function onPreDispatch()
    {
        $this->smarty = new \Smarty();

        if (Config::get('SMARTY_DEBUGGING')) {
            $this->smarty->debugging = true;
        }
        if (Config::get('SMARTY_CACHING')) {
            $this->smarty->caching = 1;
        }
        $this->smarty->template_dir = Config::get('DOC_ROOT')."/presentation";
        $this->smarty->compile_dir = Config::get('DOC_ROOT')."/tpl/templates_c";
        $this->smarty->cache_dir = Config::get('DOC_ROOT')."/tpl/cache";
        $this->smarty->config_dir = Config::get('DOC_ROOT')."/tpl/configs";

        // for smarty 3 disable notices from view (like smarty 2)
        $smarty->error_reporting = E_ALL & ~E_NOTICE;
    }

    public function assign($name, $data)
    {
        $this->smarty->assign($name, $data);
    }

    public function clear_assign($name)
    {
        $this->smarty->clear_assign($name);
    }

    public function display($template)
    {
        $this->smarty->display($template);
    }

    public function loadFilter($type, $name)
    {
        $this->smarty->load_filter($type, $name);
    }

    public function switchInternal($i)
    {
        if ($i) {
            $this->smarty->template_dir = realpath(dirname(__FILE__)).'/../../../../tpl/';
        }
    }
}
