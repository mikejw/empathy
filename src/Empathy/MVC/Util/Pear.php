<?php

namespace Empathy\MVC\Util;

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

    public static function getConfigDir()
    {
        $er = error_reporting();
        error_reporting(0);
        require_once 'PEAR/Config.php';
        $global_config_dir = \PEAR_Config::singleton()->get('cfg_dir');
        error_reporting($er);

        return $global_config_dir;
    }
}
