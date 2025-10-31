<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Config;
use Empathy\MVC\DI;

/**
 * Empathy Smarty Plugin
 * @file            Empathy/MVC/Plugin/Smarty.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class Smarty extends PresentationPlugin implements PreDispatch, Presentation
{
    protected $smarty;

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
        $this->smarty->error_reporting = E_ALL  & ~E_NOTICE & ~E_WARNING;

        if (class_exists('Empathy\ELib\Plugin\SmartyResourceELib')) {
            $this->smarty->registerResource('elib', new \Empathy\ELib\Plugin\SmartyResourceELib());
        }

        $this->smarty->registerPlugin(
            "modifier",
            "base64_encode",
            "base64_encode",
            "ucfirst"
        );
    }

    public function assign($name, $data, $no_array = false)
    {
        $this->smarty->assign($name, $data);
    }

    public function clear_assign($name)
    {
        $this->smarty->clear_assign($name);
    }

    public function display($template, $internal = false)
    {
        if ($internal) {
            $this->switchInternal();
        }
        $this->assignEmpathyDir();
        $this->smarty->display($template);
    }


    public function assignEmpathyDir()
    {
        // for default templates check test mode
        // derived from elibs plugin
        if (DI::getContainer()->get('PluginManager')->eLibsTestMode()) {
            $empathy_dir = realpath(Config::get('DOC_ROOT').'/../');
        } else {
            $empathy_dir = Config::get('DOC_ROOT').'/vendor/mikejw/empathy';
        }
        $this->assign('EMPATHY_DIR', $empathy_dir);
    }


    public function loadFilter($type, $name)
    {
        $this->smarty->load_filter($type, $name);
    }

    protected function switchInternal()
    {
        $this->smarty->template_dir = realpath(dirname(__FILE__).'/../../../../tpl/');
    }

    public function exception($debug, $exception, $reqError)
    {
        $this->assign('centerpage', true);
        $this->assign('error', $exception->getMessage());
        if ($reqError) {
            $this->assign('code', $exception->getCode());
            $this->display('req_error.tpl');
        } else {
            $this->display('empathy.tpl', true);
        }
    }

    public function getVars()
    {
        return $this->smarty->getTemplateVars();
    }

    public function clearVars()
    {
        $this->smarty->clear_all_assign();
    }

    public function getSmarty()
    {
        return $this->smarty;
    }

}
