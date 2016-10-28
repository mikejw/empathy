<?php

namespace Empathy\MVC;

// simple mocking for certain native calls
// use magic method?

class Testable
{
    private static $headers;


    private static function testMode()
    {        
        return (defined('MVC_TEST_MODE') && MVC_TEST_MODE);
    }


    private static function testModeOutput()
    {
        return (defined('MVC_TEST_OUTPUT_ON') && MVC_TEST_OUTPUT_ON);  
    }


    private static function output($msg)
    {
        if (self::testModeOutput()) {
            echo $msg."\n";
        }
    }

    public static function doDie($msg='')
    {
        if (self::testMode()) {
            self::output('die: ' . $msg);
        } else {
            die($msg);
        }
    }

    public static function header($header)
    {
        if (self::testMode()) {
            self::output('Setting header:' . $header);
            $header_arr = explode(':', $header);

            if (sizeof($header_arr)) {
                $index = $header_arr[0];
                array_shift($header_arr);
                if (sizeof($header_arr) == 1 ) {
                    $content = $header_arr[0];
                } else {
                    $content = implode(':', $header_arr);
                }
                self::$headers[$index] = trim($content);
            }
        } else {
            header($header);
        }
    }

    public static function session_start()
    {
        if (self::testMode()) {
            self::output('session start');
        } else {
            session_start();
        }
    }


    public static function session_unset()
    {
        if (self::testMode()) {
            self::output('session unset');
        } else {
            session_unset();
        }
    }


    public static function session_destroy()
    {
        if (self::testMode()) {
            self::output('session destroy');
        } else {
            session_destroy();
        }
    }


    public static function session_write_close()
    {
        if (self::testMode()) {
            self::output('session_write_close');
        } else {
            session_write_close();
        }
    }

    public static function getHeaders()
    {
        if (self::testMode()) {            
            return self::$headers;
        } else {
            if (function_exists('apache_response_headers')) {
                return apache_response_headers(); 
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
