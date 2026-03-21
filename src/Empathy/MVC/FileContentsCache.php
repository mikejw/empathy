<?php

declare(strict_types=1);

namespace Empathy\MVC;

class FileContentsCache
{
    /**
     * @param callable(string): mixed|null $callback
     */
    public static function cachedCallback(string $filename, ?callable $callback = null): mixed
    {
        $apcuAvailable = function_exists('apcu_enabled') && apcu_enabled();

        if ($apcuAvailable && (false !== ($data = apcu_fetch($filename)))) {
            // received cached
        } else {
            if (!file_exists($filename)) {
                throw new \Exception('Attempted to cache '.$filename.' but file was not found');
            }
            $raw = file_get_contents($filename);
            if ($raw === false) {
                throw new \Exception('Could not read file: '.$filename);
            }
            $data = $raw;
            if (is_callable($callback)) {
                $data = $callback($data);
            }
            if ($apcuAvailable) {
                apcu_add($filename, $data);
            }
        }
        return $data;
    }

    public static function clear(): bool
    {
        return (
            function_exists('apcu_enabled') &&
            apcu_enabled() &&
            apcu_clear_cache()
        );
    }
}
