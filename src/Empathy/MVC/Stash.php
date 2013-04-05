<?php

namespace Empathy\MVC;

class Stash
{
    private $items;

    public function __construct()
    {
        $this->items = array();
    }

    public function get($key)
    {
        if(!isset($this->items[$key])) {
            return null;
        } else {        
            return $this->items[$key];
        }
    }

    public function store($key, $data)
    {
        $this->items[$key] = $data;
    }

}
