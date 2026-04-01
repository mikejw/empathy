<?php

declare(strict_types=1);

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Config;
use Empathy\MVC\Plugin as Plugin;
use Empathy\MVC\RequestException;

/**
 * Empathy EDefault Plugin
 * @file            Empathy/MVC/Plugin/Subdomains.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting
 * with this source code in the file licence.txt
 *
 * If you are using the EDefault plugin, (redirects to ^www when WEB_ROOT starts with 'www.'),
 * ensure EDefault comes after Subdomains in your config.yml plugin config.
 *
 */
class Subdomains extends Plugin implements PreDispatch
{
    // regex from http://stackoverflow.com/a/10526727/6108127
    public function onPreDispatch(): void
    {
        $validSubs = [];
        if ($this->config !== null) {
            $validSubs = array_keys($this->config);
        }

        $webRoot = Config::get('WEB_ROOT');
        Config::store('WEB_ROOT_DEFAULT', $webRoot);
        $saneWebRoot = preg_replace('/^www\\./', '', (string) $webRoot);

        if (isset($_SERVER['HTTP_HOST'])) {
            $matches = [];
            $pattern = '/(?:http[s]*\:\/\/)*(.*?)\.(?=[^\/]*\..{2,5})/i';
            if (preg_match($pattern, (string) $_SERVER['HTTP_HOST'], $matches)) {
                if (count($validSubs) && !in_array($matches[1], $validSubs, true)) {
                    throw new RequestException('Site not found', RequestException::NOT_FOUND);
                } else {
                    Config::store('SUBDOMAIN', $matches[1]);
                    Config::store('WEB_ROOT', $matches[0] . $saneWebRoot);
                    if (isset($this->config[$matches[1]]['boot_options'])) {
                        $newOptions = array_merge(
                            Config::get('BOOT_OPTIONS'),
                            $this->config[$matches[1]]['boot_options']
                        );
                        Config::store('BOOT_OPTIONS', $newOptions);
                        $this->bootstrap->getMVC()->setBootOptions($newOptions);
                        $this->bootstrap->initBootOptions();
                    }
                }
            }
        }
    }
}
