<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Config;
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

