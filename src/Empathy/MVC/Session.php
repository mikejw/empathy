<?php

declare(strict_types=1);

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
    public static string $app = '';

    private static bool $up = false;

    public static function dump(): void
    {
        if (isset($_SESSION)) {
            echo "<pre>\n";
            print_r($_SESSION);
            echo '</pre>';
        }
    }

    public static function up(): void
    {
        if (empty($name = Config::get('NAME'))) {
            self::$app = 'unnamed';
        } else {
            self::$app = is_string($name) ? $name : 'unnamed';
        }

        if (self::$up === false) {
            Testable::session_start();
            if (!isset($_SESSION['app']) ||
               !isset($_SESSION['app'][self::$app])) {
                $_SESSION['app'][self::$app] = [];
            }
            self::$up = true;
        }
    }

    public static function getNewApp(): bool
    {
        $new_app = false;
        if (isset($_SESSION['app'][self::$app]) &&
            count($_SESSION['app'][self::$app]) > 0
        ) {
            $new_app = true;
        }

        return $new_app;
    }

    public static function setUISetting(string $ui, string $setting, mixed $value): void
    {
        $_SESSION['app'][self::$app][$ui][$setting] = $value;
    }

    public static function getUISetting(string $ui, string $setting): mixed
    {
        return $_SESSION['app'][self::$app][$ui][$setting] ?? false;
    }

    public static function down(): void
    {
        $new_app = self::getNewApp();

        unset($_SESSION['app'][self::$app]);
        if (count($_SESSION['app']) === 0) {
            Testable::session_unset();
            Testable::session_destroy();
        }

        if (isset($_SESSION['user_id']) && !$new_app) {
            foreach (array_keys($_SESSION) as $index) {
                if ($index !== 'app') {
                    unset($_SESSION[$index]);
                }
            }
        }
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION['app'][self::$app][$key] = $value;
    }

    public static function get(string $key): mixed
    {
        if (!isset($_SESSION['app'][self::$app][$key])) {
            return false;
        }

        return $_SESSION['app'][self::$app][$key];
    }

    public static function clear(string $key): void
    {
        unset($_SESSION['app'][self::$app][$key]);
    }

    /**
     * @param list<string> $ui_array
     */
    public static function loadUIVars(string $ui, array $ui_array): void
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

    public static function write(): void
    {
        Testable::session_write_close();
    }
}
