<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Config;
use Empathy\MVC\Plugin as Plugin;

class ELibs extends Plugin
{
    public function __construct()
    {
        \Empathy\MVC\Util\Lib::addToIncludePath(Config::get('DOC_ROOT').'/vendor/mikejw/elibs');
    }
}

