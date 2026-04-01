<?php

declare(strict_types=1);

namespace Empathy\MVC\Controller;

use Empathy\MVC\Bootstrap;
use Empathy\MVC\Config;
use Empathy\MVC\Controller as BaseController;
use Empathy\MVC\Entity;
use Empathy\MVC\FileContentsCache;
use Empathy\MVC\Model;
use Empathy\MVC\PluginManager\Option as PMOption;

/**
 * Default controller that reveals info about Empathy
 *
 * @author Mike Whiting mike@ai-em.net
 */
class empathy extends BaseController
{
    public function __construct(Bootstrap $boot)
    {
        parent::__construct($boot, false, [PMOption::DefaultWhitelist]);
    }

    /**
     * Default controller event.
     */
    public function default_event(): void
    {
        $this->assign('about', true);
    }

    /**
     * Default controller event.
     */
    public function status(): void
    {
        $status = 'Unknown';
        if (Config::get('DB_NAME') !== false) {
            $e = new Entity();
            Model::connectModel($e);
        }
        $status = 'OK';
        $this->assign('status', $status);
    }


    /**
     * Clear APCu cache.
     */
    public function cc(): void
    {
        $success = false;

        if (!$this->boot->isApcuDebugEnabled()) {
            $this->redirect();
        } else {
            $success = FileContentsCache::clear();

            if (function_exists('opcache_reset')) {
                @opcache_reset();
            }

            // presume to clear memcache
            try {
                $cache = $this->boot->getCacheService();
                if (is_object($cache) && method_exists($cache, 'clear')) {
                    $cache->clear();
                }
            } catch (\Exception) {
                // do nothing
            }
        }
        $this->assign('cc', $success);
    }
}
