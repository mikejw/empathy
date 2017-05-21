<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin;

/**
 * Empathy Phirehose Plugin
 * @file            Empathy/MVC/Plugin/Phirehose.php
 * @description
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
class Phirehose
{

    public function __construct()
    {
        require 'phirehose/Phirehose.php';
    }
}
