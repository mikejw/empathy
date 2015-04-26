<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin;

class Smarty extends Plugin implements PreDispatch, Presentation
{
    private $smarty;

    public function __construct()
    {
        $this->smarty = new \Smarty();
    }

    public function onPreDispatch()
    {
        if (defined('SMARTY_DEBUGGING') && SMARTY_DEBUGGING) {
            $this->smarty->debugging = true;
        }

        $this->smarty->template_dir = DOC_ROOT."/presentation";
        $this->smarty->compile_dir = DOC_ROOT."/tpl/templates_c";
        $this->smarty->cache_dir = DOC_ROOT."/tpl/cache";
        $this->smarty->config_dir = DOC_ROOT."/tpl/configs";

        if (defined('SMARTY_CACHING') && SMARTY_CACHING == true) {
            $this->smarty->caching = 1;
        }

        // assign constants
        if (defined('NAME')) {
            $this->assign('NAME', NAME);
        }
        $this->assign('DOC_ROOT', DOC_ROOT);
        $this->assign('WEB_ROOT', WEB_ROOT);
        $this->assign('PUBLIC_DIR', PUBLIC_DIR);
        $this->assign('MVC_VERSION', MVC_VERSION);
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
