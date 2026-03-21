<?php

declare(strict_types=1);

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Bootstrap;
use Empathy\MVC\Config;
use Empathy\MVC\Plugin as Plugin;
use Empathy\MVC\Util\Lib;
use Empathy\MVC\PluginManager;

/**
 * Empathy ELibs Plugin
 * @file            Empathy/MVC/Plugin/ELibs.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class ELibs extends Plugin
{
    public const int TESTING_EMPATHY = 1;
    public const int TESTING_LIB = 2;


    public function __construct(PluginManager $manager, Bootstrap $bootstrap, string $config)
    {
        parent::__construct($manager, $bootstrap, $config);
        $path = '';
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

        if ($path !== '') {
            Lib::addToIncludePath(Config::get('DOC_ROOT').$path);
        }
    }
}
