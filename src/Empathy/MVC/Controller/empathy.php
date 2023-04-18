<?php

namespace Empathy\MVC\Controller;

use Empathy\MVC\Controller as BaseController;
use Empathy\MVC\Config;
use Empathy\MVC\Entity;
use Empathy\MVC\Model;
use Empathy\MVC\FileContentsCache;
use Empathy\MVC\DI;

/**
 * Default controller that reveals info about Empathy
 *
 * @author Mike Whiting mike@ai-em.net
 */
class empathy extends BaseController
{
    /**
     * Default controller event.
     * @return null
     */
    public function default_event()
    {
        $this->assign('about', true);
    }

    /**
     * Default controller event.
     * @return null
     */
    public function status()
    {
        $status = 'Unknown';
        if (Config::get('DB_NAME') !== false) {
            $e = new Entity();
            $model = Model::connectModel($e);
            $status = 'OK';
        } else {
            $status = 'OK';
        }
        $this->assign('status', $status);
    }


    /**
     * Clear APCu cache.
     */
    public function cc()
    {
        $success = false;

        if (
            !DI::getContainer()->get('ApcuDebug')
        ) {
            $this->redirect();
        } else {
            $success = FileContentsCache::clear();
        }
        $this->assign('cc', $success);
    }
}
