<?php

declare(strict_types=1);

namespace Empathy\MVC\Util;

use Empathy\MVC\Config;
use Empathy\MVC\SafeException;

/**
 * Empathy module code generation
 * @file            Empathy/MVC/Util/ModuleGen.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class ModuleGen
{
    public static function generate(string $module, ?string $lib = null): int
    {
        if (!class_exists(\Empathy\ELib\Util\Libs::class)) {
            throw new SafeException('ELib Base not found. Please install it and try again.');
        }
        \Empathy\ELib\Util\Libs::detect();
        $installed = \Empathy\ELib\Util\Libs::getInstalled();
        $class_list = [];
        $generated = 0;

        foreach ($installed as $i) {
            $gen_root = Config::get('DOC_ROOT').'/vendor/'.$i.'/src/Empathy/ELib/Gen';
            if (file_exists($gen_root)) {
                $files = glob($gen_root.'/*.php');
                if (!is_array($files)) {
                    continue;
                }
                foreach ($files as $f) {
                    $matches = [];
                    if (preg_match('/Gen\/(.+)\.php$/', $f, $matches) !== 1) {
                        continue;
                    }
                    $class_list[] = 'Empathy\\ELib\\Gen\\'.$matches[1];
                }
            }
        }
        foreach ($class_list as $c) {
            if (!class_exists($c)) {
                continue;
            }
            $tmp = new $c();
            $getModule = [$tmp, 'getModule'];
            $write = [$tmp, 'write'];
            if (!is_callable($getModule) || !is_callable($write)) {
                continue;
            }
            $mod = \call_user_func($getModule);
            if ($mod !== $module) {
                continue;
            }
            if (\call_user_func($write)) {
                $generated++;
            }
        }
        return $generated;
    }
}
