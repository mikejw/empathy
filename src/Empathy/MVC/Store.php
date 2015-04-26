<?php

namespace Empathy\MVC;

class Store
{
    private $object;

    public function __contruct()
    {
        $this->object = array();
    }

    public function set($index, $value)
    {
        $this->object[$index] = $value;
    }

    public function get($index)
    {
        return $this->object[$index];
    }
}
