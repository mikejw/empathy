<?php

declare(strict_types=1);

namespace Empathy\MVC;

// simple mocking for certain native calls
// use magic method?

class Testable
{
    /** @var array<string, string> */
    private static array $headers = [];

    private static function testMode(): bool
    {
        return defined('MVC_TEST_MODE') && (bool) MVC_TEST_MODE;
    }

    private static function testModeOutput(): bool
    {
        return defined('MVC_TEST_OUTPUT_ON') && (bool) MVC_TEST_OUTPUT_ON;
    }

    private static function output(string $msg): void
    {
        if (self::testModeOutput()) {
            echo $msg."\n";
        }
    }

    public static function doDie(string $msg = ''): void
    {
        if (self::testMode()) {
            self::output('die: ' . $msg);
        } else {
            die($msg);
        }
    }

    public static function header(string $header): void
    {
        if (self::testMode()) {
            self::output('Setting header:' . $header);
            if ($header === '') {
                return;
            }
            $header_arr = explode(':', $header);
            $index = $header_arr[0];
            array_shift($header_arr);
            if (count($header_arr) === 1) {
                $content = $header_arr[0];
            } else {
                $content = implode(':', $header_arr);
            }
            self::$headers[$index] = trim($content);
        } else {
            header($header);
        }
    }

    public static function session_start(): void
    {
        if (self::testMode()) {
            self::output('session start');
        } else {
            session_start();
        }
    }

    public static function session_unset(): void
    {
        if (self::testMode()) {
            self::output('session unset');
        } else {
            session_unset();
        }
    }

    public static function session_destroy(): void
    {
        if (self::testMode()) {
            self::output('session destroy');
        } else {
            session_destroy();
        }
    }

    public static function session_write_close(): void
    {
        if (self::testMode()) {
            self::output('session_write_close');
        } else {
            session_write_close();
        }
    }

    /**
     * @return array<string, string>
     */
    public static function getHeaders(): array
    {
        if (self::testMode()) {
            return self::$headers;
        }
        if (function_exists('apache_response_headers')) {
            return apache_response_headers() ?: [];
        }
        $h = [];
        foreach ($_SERVER as $key => $value) {
            if (is_string($key) && strpos($key, 'HTTP_') === 0) {
                $new_key = str_replace(' ', '-', ucwords(str_replace('_', ' ', substr(strtolower($key), 5))));
                $h[$new_key] = is_scalar($value) ? (string) $value : '';
            }
        }
        return $h;
    }

    public static function miscReset(): void
    {
        self::$headers = [];
    }
}
