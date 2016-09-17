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
    const TESTING_EMPATHY = 1;
    const TESTING_LIB = 2;


    public function __construct($manager, $bootstrap, $config)
    {
        parent::__construct($manager, $bootstrap, $config);
	 	
	 	if (isset($this->config['testing']) && $this->config['testing']) {

            switch ($this->config['testing']) {
                case self::TESTING_EMPATHY:
                    $path = '/../vendor/mikejw/elibs';
                    break;
                case self::TESTING_LIB:
                    $path = '/../../../../vendor/mikejw/elibs';
                    break;
                default:
                    break;
            }
            
        } else {
            $path = '/vendor/mikejw/elibs';
        }

        \Empathy\MVC\Util\Lib::addToIncludePath(Config::get('DOC_ROOT').$path);
    }
}

