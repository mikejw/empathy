<?php

declare(strict_types=1);

namespace Empathy\MVC;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Bridges framework log calls (LogItem, etc.) to a PSR-3 logger configured at application boot.
 */
final class LogBridge
{
    private static bool $enabled = false;

    private static ?LoggerInterface $logger = null;

    public static function configure(bool $enabled, ?LoggerInterface $logger): void
    {
        self::$enabled = $enabled && $logger !== null;
        self::$logger = $logger;
    }

    /**
     * @param array<string, mixed> $context
     */
    public static function log(string $level, string $message, array $context = []): void
    {
        if (!self::$enabled || self::$logger === null) {
            return;
        }

        $psrLevel = self::normalizeLevel($level);
        self::$logger->log($psrLevel, $message, $context);
    }

    private static function normalizeLevel(string $level): string
    {
        return match (strtolower($level)) {
            'debug' => LogLevel::DEBUG,
            'info' => LogLevel::INFO,
            'notice' => LogLevel::NOTICE,
            'warning' => LogLevel::WARNING,
            'error' => LogLevel::ERROR,
            'critical' => LogLevel::CRITICAL,
            'alert' => LogLevel::ALERT,
            'emergency' => LogLevel::EMERGENCY,
            default => LogLevel::INFO,
        };
    }
}
