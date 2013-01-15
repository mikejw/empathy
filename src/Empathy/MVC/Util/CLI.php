<?php

/**
 * Empathy/MVC/Util/CLI.php 
 * 
 * PHP Version 5
 *
 * LICENSE: This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 *
 * @category  Framework
 * @package   Empathy
 * @author    Mike Whiting <mail@mikejw.co.uk>
 * @copyright 2008-2013 Mike Whiting
 * @license   http://www.gnu.org/licenses/gpl-3.0-standalone.html GPL v3.0
 * @link      http://empathyphp.co.uk
 *      
 */

namespace Empathy\MVC\Util;


/**
 * Empathy CLI Utility class.
 * Make various requests to the MVC from the command line.
 *
 * @category Framework
 * @package  Empathy
 * @author   Mike Whiting <mail@mikejw.co.uk>
 * @license  http://www.gnu.org/licenses/gpl-3.0-standalone.html GPL v3.0
 * @link     http://empathyphp.co.uk
 */
class CLI
{

    private static $_reqMode = CLIMode::TIMED;


    /**
     * Set request mode. Expects class constant value from CLIMode class.
     *
     * @param integer $mode request mode value 
     *
     * @return void
     */
    public static function setReqMode($mode)
    {
        self::$_reqMode = $mode;
    }


    /**
     * Formatted microtime.
     *
     * @return float microtime
     */
    private static function _realMicrotime()
    {
        list($micro, $seconds) = explode(' ', microtime());

        return ((float) $micro + (float) $seconds);
    }

    /**
     * End request by resetting super globals.
     *
     * @return void
     */
    private static function _requestEnd()
    {
        $_GET = array();
        $_POST = array();
    }

    /**
     * Performm request to mvc using active method.
     *
     * @param Empathy $e   MVC boot object
     * @param string  $uri URI of request 
     *
     * @return float/string/Controller
     */
    public static function request($e, $uri)
    {

        switch (self::$_reqMode) {
        case CLIMode::TIMED:

            $t_request_start = self::_realMicrotime();
            $_SERVER['REQUEST_URI'] = $uri;

            ob_start();
            $e->beginDispatch();
            $t_request_finish = self::_realMicrotime();
            ob_end_clean();

            $t_elapsed = ($t_request_finish - $t_request_start);
            $t_elapsed = number_format($t_elapsed, 4);
            self::_requestEnd();

            return $t_elapsed;
            break;

        case CLIMode::CAPTURED:

            ob_start();
            $_SERVER['REQUEST_URI'] = $uri;
            $e->beginDispatch();
            $response = ob_get_contents();
            ob_end_clean();
            self::_requestEnd();

            return $response;
            break;

        case CLIMode::FAKED:
            ob_start();
            $_SERVER['REQUEST_URI'] = $uri;            
            $controller = $e->beginDispatch(true);
            ob_end_clean();
            self::_requestEnd();

            return $controller;
            break;

        default:
            break;

        }

    }
}
