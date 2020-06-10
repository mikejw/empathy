<?php

/**
 * Empathy/MVC/Util/CLI.php
 *
 * @category  Framework
 * @package   Empathy
 * @author    Mike Whiting <mikejw3@gmail.com>
 * @copyright 2008-2015 Mike Whiting
 * @license   See LICENSE
 * @link      http://empathyphp.co.uk
 *
 */
namespace Empathy\MVC\Util;

class CLI
{

    private static $reqMode = CLIMode::STREAMED;


    /**
     * Set request mode. Expects class constant value from CLIMode class.
     *
     * @param integer $mode request mode value
     *
     * @return void
     */
    public static function setReqMode($mode)
    {
        self::$reqMode = $mode;
    }


    /**
     * Formatted microtime.
     *
     * @return float microtime
     */
    private static function realMicrotime()
    {
        list($micro, $seconds) = explode(' ', microtime());
        return ((float) $micro + (float) $seconds);
    }

    /**
     * End request by resetting super globals.
     *
     * @return void
     */
    private static function requestEnd()
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

        switch (self::$reqMode) {
            case CLIMode::TIMED:
                $t_request_start = self::realMicrotime();
                $_SERVER['REQUEST_URI'] = $uri;

                ob_start();
                $e->beginDispatch();
                $t_request_finish = self::realMicrotime();
                ob_end_clean();

                $t_elapsed = ($t_request_finish - $t_request_start);
                $t_elapsed = number_format($t_elapsed, 4);
                self::requestEnd();

                return $t_elapsed;
                break;

            case CLIMode::CAPTURED:
                ob_start();

                $_SERVER['REQUEST_URI'] = $uri;
                $e->beginDispatch();
                $response = ob_get_contents();
                ob_end_clean();
                self::requestEnd();

                return $response;
                break;

            case CLIMode::FAKED:
                ob_start();
                $_SERVER['REQUEST_URI'] = $uri;
                $controller = $e->beginDispatch(true);
                ob_end_clean();
                self::requestEnd();

                return $controller;
                break;

            case CLIMode::STREAMED:
                $_SERVER['REQUEST_URI'] = $uri;
                $e->beginDispatch();
                break;
                
            default:
                break;
        }
    }
}
