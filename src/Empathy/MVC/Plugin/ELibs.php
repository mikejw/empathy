<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin;

/**
 * Empathy ELibs Plugin
 * @file            Empathy/MVC/Plugin/ELibs.php
 * @description     
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
class ELibs extends Plugin
{
    public function __construct()
    {
        \Empathy\MVC\Util\Lib::addToIncludePath(DOC_ROOT.'/vendor/mikejw/elibs');
    }
}

