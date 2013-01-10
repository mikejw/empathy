<?php

namespace Empathy\MVC\Util;

/* based on Util/Test.php
   (which is currently outside of namespacing)

   usage: (from application subfolder. eg. '/scripts'.)
   // get into application-like context...
   include 'Empathy/Empathy.php';
   $boot = new Empathy(realpath(dirname(realpath(__FILE__)).'/../'), true);
   // make admin
   \Empathy\Session::up();
   \Empathy\Session::set('user_id', 2);
   $output = \Empathy\Util\CLI::request($boot, $data['url']);
   // print output etc...
   */

class CLI
{   

    private static $reqMode = CLIMode::TIMED;


    public static function setReqMode($mode)
    { 
        self::$reqMode = $mode;
    }


    private static function realMicrotime()
    {
        list($micro, $seconds) = explode(' ', microtime());

        return ((float) $micro + (float) $seconds);
    }

    private static function requestEnd()
    {
        // reset super globals
        $_GET = array();
        $_POST = array();
    }


    public static function request($e, $uri)
    {
        switch(self::$reqMode) {
            case CLIMode::TIMED:

                $t_request_start = self::realMicrotime();
                $_SERVER['REQUEST_URI'] = $uri;

                ob_start();
                $e->beginDispatch();
                $t_request_finish = self::realMicrotime();
                //ob_get_contents();
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
                $_SERVER['REQUEST_URI'] = $uri;
                $controller = $e->beginDispatch(true);
                self::requestEnd();

                return $controller;
            break;

            default:
            break;

        }

    }
}
