<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin;

class Cassandra extends Plugin
{

    public function __construct()
    {
        require 'Cassandra/gen-php/cassandra/Cassandra.php';
        require 'Cassandra/gen-php/cassandra/Types.php';
    }
}
