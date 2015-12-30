<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Config;
use Empathy\MVC\Plugin as Plugin;

/**
 * Empathy Smarty Plugin
 * @file            Empathy/MVC/Plugin/Smarty.php
 * @description     
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
class Smarty extends Plugin implements PreDispatch, Presentation
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

        // for smarty 3 disable notices from view (like smarty 2)
        $this->smarty->error_reporting = E_ALL & ~E_NOTICE;
    }

    public function assign($name, $data, $no_array=false)
    {
        $this->smarty->assign($name, $data);
    }

    public function clear_assign($name)
    {
        $this->smarty->clear_assign($name);
    }

    public function display($template, $internal=false)
    {
        if ($internal) {
            $this->switchInternal();
        }

        $this->assignEmpathyDir();

        $this->smarty->display($template);
    }



    public function assignEmpathyDir()
    {
        // @todo: optimise somehow?
        // for default templates check test mode
        // derived from elibs plugin
        if ($this->manager->eLibsTestMode()) {
            $empathy_dir = Config::get('DOC_ROOT').'/../';
        } else {
            $empathy_dir = Config::get('DOC_ROOT').'/vendor/mikejw/empathy';
        }
        $empathy_dir = realpath($empathy_dir);
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

    public function exception($debug, $exception, $req_error)
    {
        $this->assign('error', $exception->getMessage());                    
        if($req_error) {
             $this->assign('code', $exception->getCode());
             $this->display('elib:/req_error.tpl');
        } else {            
            $this->display('empathy.tpl', true);
        }
    }

    public function getVars()
    {
        return $this->smarty->get_template_vars();
    }

    public function clearVars()
    {
        $this->smarty->clear_all_assign();
    }


}
