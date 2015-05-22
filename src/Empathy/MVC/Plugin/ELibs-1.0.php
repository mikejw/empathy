<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Config;
use Empathy\MVC\Plugin as Plugin;

class ELibs extends Plugin
{
    public function __construct($config)
    {
        parent::__construct($config);

        if (isset($this->config['testing']) && $this->config['testing']) {
            $path = '/../vendor/mikejw/elibs';
        } else {
            $path = '/vendor/mikejw/elibs';
        }

        \Empathy\MVC\Util\Lib::addToIncludePath(Config::get('DOC_ROOT').$path);
    }
}

