<?php

namespace Empathy\MVC\Plugin;

interface Presentation
{
    //  public function __construct();
    public function assign($name, $data);

    public function exception($debug, $exception, $req_error);

    public function display($template);
}
