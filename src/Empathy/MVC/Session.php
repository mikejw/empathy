<?php

namespace Empathy\MVC;

/**
 * Empathy Session class
 * @file            Empathy/MVC/Session.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class Session
{
    public static $app;
    private static $up = false;

    public static function dump()
    {
        if (isset($_SESSION)) {
            echo "<pre>\n";
            print_r($_SESSION);
            echo "</pre>";
        }
    }

    public static function up()
    {
        if (empty($name = Config::get('NAME'))) {
            self::$app = 'unnamed';
        } else {
            self::$app = $name;
        }

        if (self::$up === false) {
            Testable::session_start();
            if (!isset($_SESSION['app']) ||
               !isset($_SESSION['app'][self::$app])) {
                $_SESSION['app'][self::$app] = array();
            }
            //self::dump();
            self::$up = true;
        }
    }

    public static function getNewApp()
    {
        $new_app = false;
        if (isset($_SESSION['app'][self::$app]) &&
            sizeof($_SESSION['app'][self::$app]) > 0
        ) {
            $new_app = true;
        }

        return $new_app;
    }

    public static function setUISetting($ui, $setting, $value)
    {
        $_SESSION['app'][self::$app][$ui][$setting] = $value;
    }

    public static function getUISetting($ui, $setting)
    {
        if (isset($_SESSION['app'][self::$app][$ui][$setting])) {
            return $_SESSION['app'][self::$app][$ui][$setting];
        } else {
            return false;
        }
    }

    public static function down()
    {
        // backwards compatibility
        $new_app = self::getNewApp();

        // main logic
        unset($_SESSION['app'][self::$app]);
        if (sizeof($_SESSION['app']) == 0) {
            Testable::session_unset();
            Testable::session_destroy();
        }

        // backwards compatibility
        if (isset($_SESSION['user_id']) && !$new_app) {
            foreach ($_SESSION as $index => $value) {
                if ($index != 'app') {
                    unset($_SESSION[$index]);
                }
            }
        }
    }

    public static function set($key, $value)
    {
        $_SESSION['app'][self::$app][$key] = $value;
    }

    public static function get($key)
    {
        if (!isset($_SESSION['app'][self::$app][$key])) {
            return false;
        } else {
            return $_SESSION['app'][self::$app][$key];
        }
    }

    public static function clear($key)
    {
        unset($_SESSION['app'][self::$app][$key]);
    }

    
    
    public static function loadUIVars($ui, $ui_array)
    {
        $new_app = self::getNewApp();
        foreach ($ui_array as $setting) {
            if (isset($_GET[$setting])) {
                if (!$new_app) {
                    $_SESSION[$ui][$setting] = $_GET[$setting];
                } else {
                    self::setUISetting($ui, $setting, $_GET[$setting]);
                }
            } elseif (self::getUISetting($ui, $setting) !== false) {
                $_GET[$setting] = self::getUISetting($ui, $setting);
            } elseif (isset($_SESSION[$ui][$setting])) {
                $_GET[$setting] = $_SESSION[$ui][$setting];
            }
        }
    }


    public static function write()
    {
        Testable::session_write_close();
    }
}
