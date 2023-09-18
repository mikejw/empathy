<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin;
use Empathy\MVC\Config;

/**
 * Empathy Smarty Plugin
 * @file            Empathy/MVC/Plugin/Smarty.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class SmartySSL extends Smarty
{
    public function isSecure()
    {
        return (
            isset($this->config['force']) && $this->config['force'] ||
            \Empathy\MVC\Util\Misc::isSecure()
        );
    }

    public function display($template, $internal = false)
    {
        if ($internal) {
            $this->switchInternal();
        }

        $this->assignEmpathyDir();

        if (
            $this->isSecure()
        ) {
            echo str_replace(
                'http://'.Config::get('WEB_ROOT'),
                'https://'.Config::get('WEB_ROOT'),
                $this->smarty->fetch($template)
            );
        } else {
            $this->smarty->display($template);
        }
    }
}
