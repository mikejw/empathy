<?php

namespace Empathy\MVC\Util;
use Empathy\MVC\DI;

/**
 * Empathy Misc util
 * @file            Empathy/MVC/Util/Pear.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class Misc
{
    public static function isSecure()
    {
        try {
            DI::getContainer()->get('PluginManager')->find(['SmartySSL']);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
