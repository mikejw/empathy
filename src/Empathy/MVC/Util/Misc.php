<?php

namespace Empathy\MVC\Util;

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
        return ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443));
    }
}
