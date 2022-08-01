<?php
/**
 * This file is part of the Empathy package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * @copyright 2008-2016 Mike Whiting
 * @license  See LICENSE
 * @link      http://www.empathyphp.co.uk
 */

namespace Empathy\MVC\Controller;

use Empathy\MVC\Controller as BaseController;
use Empathy\MVC\Config;
use Empathy\MVC\Entity;
use Empathy\MVC\Model;

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
        $apcuAvailable = function_exists('apcu_enabled') && \apcu_enabled();
        if (!$apcuAvailable) {
            throw new \Exception('APCu is not available!');
        }
        \apcu_clear_cache();
        $this->assign('cc', true);
    }

}
