<?php

namespace Empathy\MVC\Plugin;

/**
 * Empathy Presentation interface
 * @file            Empathy/MVC/Plugin/Presentation.php
 * @description     
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
interface Presentation
{
    
    public function assign($name, $data, $no_array=false);

	public function exception($debug, $exception, $req_error);
	
    public function display($template, $internal=false);

    public function getVars();

    public function clearVars();

}
