<?php

declare(strict_types=1);

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Config;
use Empathy\MVC\Plugin as Plugin;

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
    public function isSecure(): bool
    {
        return (
            isset($this->config['force']) && $this->config['force'] ||
            \Empathy\MVC\Util\Misc::isSecure()
        );
    }

    public function display(string $template, bool $internal = false): void
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
