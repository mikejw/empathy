<?php

declare(strict_types=1);

namespace Empathy\MVC\Util;

use Empathy\MVC\PluginManager;

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
    public static function isSecure(PluginManager $pluginManager): bool
    {
        try {
            $pluginManager->find(['SmartySSL']);
        } catch (\Exception) {
            return false;
        }

        return true;
    }
}
