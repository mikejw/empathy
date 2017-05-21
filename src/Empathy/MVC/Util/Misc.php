<?php

namespace Empathy\MVC\Util;

/**
 * Empathy Misc util
 * @file            Empathy/MVC/Util/Pear.php
 * @description
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
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
