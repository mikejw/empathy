<?php

namespace Empathy\MVC\Plugin;

interface Presentation
{
    
    public function assign($name, $data);

	public function exception($debug, $exception, $req_error);
	
    public function display($template);
}
