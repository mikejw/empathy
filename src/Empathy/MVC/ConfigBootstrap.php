<?php

declare(strict_types=1);

namespace Empathy\MVC;

/**
 * Loads merged application + framework YAML into {@see Config} before {@see Empathy}
 * is constructed, so services (e.g. logging) see a populated {@see Config}.
 *
 * @author Mike Whiting
 */
final class ConfigBootstrap
{
    /**
     * @param array{0: array<string, mixed>, 1: array<string, mixed>} $loadedConfig
     */
    public static function apply(array $loadedConfig, string $configDir): BootSnapshot
    {
        [$app, $global] = $loadedConfig;
        $bootOptions = [];
        $plugins = [];

        foreach ([$app, $global] as $config) {
            self::mergeIntoConfig($config, $configDir);
            if (isset($config['boot_options']) && is_array($config['boot_options'])) {
                $bootOptions = $config['boot_options'];
            }
            if (isset($config['plugins']) && is_array($config['plugins'])) {
                $plugins = array_values($config['plugins']);
            }
        }

        /** @var list<array<string, mixed>> $plugins */
        return new BootSnapshot($bootOptions, $plugins);
    }

    /**
     * @param array<string, mixed> $config
     */
    private static function mergeIntoConfig(array $config, string $configDir): void
    {
        foreach ($config as $index => $item) {
            $value = $item;
            if (!is_array($value) && $index === 'doc_root' && !file_exists((string) $value)) {
                $value = $configDir;
            }
            Config::store(strtoupper((string) $index), $value);
        }
    }
}
