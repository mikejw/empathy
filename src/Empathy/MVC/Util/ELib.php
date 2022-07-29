<?php

namespace Empathy\MVC\Util;

use Empathy\ELib\Util;

/**
 * Empathy ELib Plugin
 * @file            Empathy/MVC/Util/ELib.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class ELib
{
    public static function getLibLocation()
    {
        return Util::getLocation();
    }
}
