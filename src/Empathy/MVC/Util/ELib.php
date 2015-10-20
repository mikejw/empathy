<?php

namespace Empathy\MVC\Util;

use ELib\MVC\Util;


/**
 * Empathy ELib Plugin
 * @file            Empathy/MVC/Util/ELib.php
 * @description     
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
class ELib
{
    public static function getLibLocation()
    {
        return Util::getLocation();
    }
}
