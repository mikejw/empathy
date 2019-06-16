<?php

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
        require 'phirehose/Phirehose.php';
    }
}
