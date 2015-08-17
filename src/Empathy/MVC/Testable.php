<?php

namespace Empathy\MVC;

// simple mocking for certain native calls
// use magic method?

class Testable
{
    private static $headers;

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
                //echo 'Setting header:' . $header;
                $header_arr = explode(':', $header);                
                self::$headers[$header_arr[0]] = trim($header_arr[1]);
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

    public static function getHeaders()
    {
        if (defined('MVC_TEST_MODE') && MVC_TEST_MODE) {            
            return self::$headers;
        } else {
            if (function_exists('getallheaders')) {
                return getallheaders(); 
            } else {
                $h = array();
                foreach ($_SERVER as $key => $value) {
                    if (strpos($key, 'HTTP_') === 0) {                        
                        $new_key = str_replace(' ', '-', ucwords(str_replace('_', ' ', substr(strtolower($key), 5))));
                        $h[$new_key] = $value;
                    }
                }
                return $h;
            }
        }
    }

    public static function miscReset()
    {
        self::$headers = array();
    }

}

