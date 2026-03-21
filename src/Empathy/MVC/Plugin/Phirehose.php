<?php

declare(strict_types=1);

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin;

/**
 * Empathy Phirehose Plugin
 * @file            Empathy/MVC/Plugin/Phirehose.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class Phirehose
{
    public function __construct()
    {
        if (file_exists('phirehose/Phirehose.php')) {
            include __DIR__ . '/phirehose/Phirehose.php';
        }
    }
}
