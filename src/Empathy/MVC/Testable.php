<?php

namespace Empathy\MVC;

// simple mocking for certain native calls
// use magic method?

class Testable
{

    public static function doDie($msg='')
    {
        if (defined('MVC_TEST_MODE') && MVC_TEST_MODE) {
            if (defined('MVC_TEST_OUTPUT_ON') && MVC_TEST_OUTPUT_ON) {
                echo 'die: ' . $msg;
            }        
        } else {
            die($header);
        }
    }

    public static function header($header)
    {
        if (defined('MVC_TEST_MODE') && MVC_TEST_MODE) {
            if (defined('MVC_TEST_OUTPUT_ON') && MVC_TEST_OUTPUT_ON) {
                echo 'Setting header:' . $header;    
            }
            
        } else {
            header($header);
        }
    }

    public static function session_start()
    {
        if (defined('MVC_TEST_MODE') && MVC_TEST_MODE) {
            if (defined('MVC_TEST_OUTPUT_ON') && MVC_TEST_OUTPUT_ON) {
                echo 'session start';
            }
        } else {
            session_start();
        }
    }


    public static function session_unset()
    {
        if (defined('MVC_TEST_MODE') && MVC_TEST_MODE) {
            if (defined('MVC_TEST_OUTPUT_ON') && MVC_TEST_OUTPUT_ON) {
                echo 'session unset';
            }
        } else {
            session_unset();
        }
    }


    public static function session_destroy()
    {
        if (defined('MVC_TEST_MODE') && MVC_TEST_MODE) {
            if (defined('MVC_TEST_OUTPUT_ON') && MVC_TEST_OUTPUT_ON) {
                echo 'session destroy';
            }
        } else {
            session_destroy();
        }
    }


    public static function session_write_close()
    {
        if (defined('MVC_TEST_MODE') && MVC_TEST_MODE) {
            if (defined('MVC_TEST_OUTPUT_ON') && MVC_TEST_OUTPUT_ON) {
                echo 'session_write_close';
            }
        } else {
            session_write_close();
        }
    }
}

