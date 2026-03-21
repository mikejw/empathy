<?php

declare(strict_types=1);

namespace Empathy\MVC\Util;

use Empathy\MVC\SafeException;

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
    public static function getLibLocation(): string
    {
        if (!class_exists(Empathy\ELib\Util::class)) {
            throw new SafeException('ELib Base not found. Please install it and try again.');
        }

        return Empathy\ELib\Util::getLocation();
    }
}
