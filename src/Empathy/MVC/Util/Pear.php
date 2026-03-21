<?php

declare(strict_types=1);

namespace Empathy\MVC\Util;
use Empathy\MVC\SafeException;

/**
 * Empathy Pear util
 * @file            Empathy/MVC/Util/Pear.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class Pear
{
    public static function getConfigDir(): string
    {
        $dir = '';
        if (!file_exists('PEAR/Config.php')) {
            throw new SafeException('PEAR not found. Please install it and try again.');
        }
        include(require_once __DIR__ . '/PEAR/Config.php');

        if (class_exists(\PEAR_Config::class)) {
            $dir = \PEAR_Config::singleton()->get('cfg_dir');
        }
        return $dir;
    }
}
