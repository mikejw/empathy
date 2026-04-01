<?php

declare(strict_types=1);

namespace Empathy\MVC\Plugin;

use Empathy\MVC\ApplicationPaths;
use Empathy\MVC\Config;
use Empathy\MVC\SafeException;

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
    /**
     * @var bool
     */
    public $debugging;
    /**
     * @var int
     */
    public $caching;
    /**
     * @var bool|string
     */
    public $template_dir;
    /**
     * @var string
     */
    public $compile_dir;
    /**
     * @var string
     */
    public $cache_dir;
    /**
     * @var string
     */
    public $config_dir;
    /**
     * @var int
     */
    public $error_reporting;
    protected \Smarty $smarty;

    public function onPreDispatch(): void
    {
        $this->smarty = new \Smarty();

        if (Config::get('SMARTY_DEBUGGING')) {
            $this->smarty->debugging = true;
        }
        if (Config::get('SMARTY_CACHING')) {
            $this->smarty->caching = 1;
        }
        $docRoot = $this->applicationPaths()->docRoot;
        if ($docRoot === null || $docRoot === '') {
            throw new SafeException('Smarty requires doc_root (DOC_ROOT) to be set in application config.');
        }
        $this->smarty->template_dir = $docRoot.'/presentation';
        $this->smarty->compile_dir = $docRoot.'/tpl/templates_c';
        $this->smarty->cache_dir = $docRoot.'/tpl/cache';
        $this->smarty->config_dir = $docRoot.'/tpl/configs';
        $this->smarty->error_reporting = E_ALL  & ~E_NOTICE & ~E_WARNING;

        if (class_exists('Empathy\ELib\Plugin\SmartyResourceELib')) {
            $this->smarty->registerResource('elib', new \Empathy\ELib\Plugin\SmartyResourceELib());
        }

        $this->smarty->registerPlugin(
            'modifier',
            'base64_encode',
            'base64_encode'
        );
        $this->smarty->registerPlugin(
            'modifier',
            'ucfirst',
            'ucfirst'
        );
        $this->smarty->registerPlugin(
            'modifier',
            'sizeof',
            'sizeof'
        );
        $this->smarty->registerPlugin(
            'modifier',
            'preg_match',
            'preg_match'
        );
    }

    public function assign(string $name, mixed $data, bool $no_array = false): void
    {
        $this->smarty->assign($name, $data);
    }

    public function clear_assign(string $name): void
    {
        $this->smarty->clear_assign($name);
    }

    public function display(string $template, bool $internal = false): void
    {
        if ($internal) {
            $this->switchInternal();
        }
        $this->assignEmpathyDir();
        $this->smarty->display($template);
    }


    public function assignEmpathyDir(): void
    {
        // for default templates check test mode
        // derived from elibs plugin
        $docRoot = $this->applicationPaths()->docRoot;
        if ($docRoot === null || $docRoot === '') {
            throw new SafeException('Cannot resolve EMPATHY_DIR: doc_root is not set.');
        }
        if ($this->manager->eLibsTestMode()) {
            $empathy_dir = realpath($docRoot.'/../');
        } else {
            $empathy_dir = $docRoot.'/vendor/mikejw/empathy';
        }
        $this->assign('EMPATHY_DIR', $empathy_dir);
    }

    private function applicationPaths(): ApplicationPaths
    {
        return $this->bootstrap->getMVC()->getApplicationPaths();
    }


    public function loadFilter(string $type, string $name): void
    {
        $this->smarty->load_filter($type, $name);
    }

    protected function switchInternal(): void
    {
        $this->smarty->template_dir = realpath(__DIR__.'/../../../../tpl/');
    }

    public function exception(bool $debug, \Throwable $exception, bool $reqError): void
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

    public function getVars(): mixed
    {
        return $this->smarty->getTemplateVars();
    }

    public function clearVars(): void
    {
        $this->smarty->clear_all_assign();
    }
}
