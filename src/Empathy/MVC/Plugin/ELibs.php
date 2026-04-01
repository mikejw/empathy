<?php

declare(strict_types=1);

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Bootstrap;
use Empathy\MVC\Plugin;
use Empathy\MVC\PluginManager;
use Empathy\MVC\Util\Lib;

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


    public function __construct(PluginManager $manager, Bootstrap $bootstrap, ?string $config = null)
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
            $docRoot = $bootstrap->getMVC()->getApplicationPaths()->docRoot;
            if ($docRoot !== null && $docRoot !== '') {
                Lib::addToIncludePath($docRoot.$path);
            }
        }
    }
}
