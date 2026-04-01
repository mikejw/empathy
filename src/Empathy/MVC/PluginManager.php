<?php

declare(strict_types=1);

namespace Empathy\MVC;

use Empathy\MVC\Plugin\PreDispatch;
use Empathy\MVC\Plugin\PreEvent;
use Empathy\MVC\Plugin\Presentation;
use Empathy\MVC\Plugin\PresentationPlugin;
use Empathy\MVC\PluginManager\Option;

/**
 * Empathy PluginManager
 * @file            Empathy/MVC/PluginManager.php
 * @description
 * @author          Michael J. Whiting
 * @license         See LICENCE
 *
 * (c) copyright Michael J. Whiting

 * with this source code in the file LICENSE
 */
class PluginManager
{
    private bool $initialized = false;
    private ?Controller $controller = null;

    /** @var list<mixed> */
    private array $options = [];

    /** @var list<string> */
    private array $whitelist = [];

    /** @var list<object> */
    private array $plugins = [];

    private mixed $view = null;

    public const DEF_WHITELIST_LIST = [
        'ELibs',
        'Smarty',
        'SmartySSL',
        'JSONView',
        'EDefault',
    ];

    /**
     * @param list<mixed> $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @param list<string> $whitelist
     */
    public function setWhitelist(array $whitelist): void
    {
        if (in_array(Option::DefaultWhitelist, $this->options, true)) {
            $whitelist = array_merge($whitelist, self::DEF_WHITELIST_LIST);
        }
        $this->whitelist = $whitelist;
    }

    public function setController(Controller $c): void
    {
        $this->controller = $c;
    }

    public function getController(): ?Controller
    {
        return $this->controller;
    }

    public function init(): void
    {
        $this->initialized = true;
    }

    public function register(object $p): void
    {
        $this->plugins[] = $p;
        DI::getContainer()->set($p::class, $p);
    }

    public function preDispatch(object $p): void
    {
        if ($p instanceof PreDispatch) {
            $p->onPreDispatch();
        }
    }

    public function preEvent(): void
    {
        foreach ($this->plugins as $p) {
            if ($p instanceof PreEvent) {
                $p->onPreEvent();
            }
        }
    }

    public function attemptSetView(object $p): void
    {
        if ($p instanceof Presentation && $p instanceof PresentationPlugin && $p->getGlobal()) {
            $this->setView($p);
        }
    }

    public function getView(): mixed
    {
        return $this->view;
    }

    public function setView(mixed $view): void
    {
        $this->view = $view;
    }

    public function getInitialized(): bool
    {
        return $this->initialized;
    }

    public function eLibsTestMode(): bool
    {
        $mode = false;
        foreach ($this->plugins as $p) {
            if ($p::class === \Empathy\MVC\Plugin\ELibs::class) {
                $c = $p->getConfig();
                if (isset($c['testing']) && $c['testing']) {
                    $mode = true;
                }
                break;
            }
        }
        return $mode;
    }

    /**
     * @return list<string>
     */
    public function getWhitelist(): array
    {
        return $this->whitelist;
    }

    /**
     * @param list<string> $names
     */
    public function find(array $names = []): object
    {
        foreach ($names as $name) {
            if (count(explode('\\', $name)) === 1) {
                $name = 'Empathy\\MVC\\Plugin\\' . $name;
            }
            foreach ($this->plugins as $p) {
                if ($p::class === $name) {
                    return $p;
                }
            }
        }
        throw new \Exception('Could not find plugin in list: '. implode(', ', $names) . '.');
    }
}
