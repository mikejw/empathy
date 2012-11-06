<?php

namespace Empathy\MVC\Util;

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
