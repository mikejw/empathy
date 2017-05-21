<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin;
use Empathy\MVC\Config;

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


    public function display($template, $internal = false)
    {
        if ($internal) {
            $this->switchInternal();
        }
        if (\Empathy\MVC\Util\Misc::isSecure()) {
            echo str_replace('http://'.Config::get('WEB_ROOT'),
                'https://'.Config::get('WEB_ROOT'),
                $this->smarty->fetch($template)
            );
        } else {
            $this->smarty->display($template);
        }
    }
}
