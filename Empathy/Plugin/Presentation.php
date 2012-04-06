<?php

namespace Empathy\Plugin;

interface Presentation
{
  public function __construct();
  public function assign($name, $data);
  public function display($template);
}
?>