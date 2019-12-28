<?php

namespace Empathy\MVC\Util;

use Empathy\ELib\Util\Libs;
use Empathy\MVC\Config;

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
    
    public static function generate($module, $lib = null)
    {
        Libs::detect();
        $installed = Libs::getInstalled();
        $class_list = array();
        $generated = 0;

        foreach ($installed as $i) {
            $gen_root = Config::get('DOC_ROOT').'/vendor/'.$i.'/src/Empathy/ELib/Gen';
            if (file_exists($gen_root)) {
                $files = glob($gen_root.'/*.php');
                foreach ($files as $f) {
                    $matches = array();
                    preg_match('/Gen\/(.+)\.php$/', $f, $matches);
                    $class_list[] = 'Empathy\\ELib\\Gen\\'.$matches[1];
                }
            }
        }
        foreach ($class_list as $c) {
            $tmp = new $c();
            if ($tmp->getModule() == $module) {
                if ($tmp->write()) {
                    $generated++;
                }
            }
        }
        return $generated;
    }
}
