<?php

namespace Empathy;

class Stash
{
  private $items;
  
  public function __construct()
  {
    $this->items = array();
  }  

  public function get($key)
  {
    return $this->items[$key];
  }

  public function store($key, $data)
  {
    $this->items[$key] = $data;
  }


}
?>