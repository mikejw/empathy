<?php

namespace Empathy\MVC\Plugin;


/**
 * Empathy Cassandra Plugin
 * @file            Empathy/MVC/Plugin/Cassandra.php
 * @description     
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
use Empathy\MVC\Plugin as Plugin;

class Cassandra extends Plugin
{

    public function __construct()
    {
        require 'Cassandra/gen-php/cassandra/Cassandra.php';
        require 'Cassandra/gen-php/cassandra/Types.php';
    }
}
