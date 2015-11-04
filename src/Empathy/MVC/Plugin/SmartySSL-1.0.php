<?php



namespace Empathy\MVC\Plugin;

require('../vendor/mikejw/empathy/src/Empathy/MVC/Plugin/Smarty-1.0.php');

use Empathy\MVC\Plugin as Plugin;

/**
 * Empathy Smarty Plugin
 * @file            Empathy/MVC/Plugin/Smarty.php
 * @description     
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
class SmartySSL extends Smarty
{


    public function display($template, $internal=false)
    {
        if ($internal) {
            $this->switchInternal();
        }
	echo str_replace('http://'.WEB_ROOT,
	     'https://'.WEB_ROOT,
	     $this->smarty->fetch($template));
    }

}
