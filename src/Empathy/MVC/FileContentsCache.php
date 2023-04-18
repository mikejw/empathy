<?php

namespace Empathy\MVC;

class FileContentsCache
{
    public static function cachedCallback($filename, $callback = null)
    {
        $apcuAvailable = function_exists('apcu_enabled') && apcu_enabled();

        if ($apcuAvailable && (false !== ($data = apcu_fetch($filename)))) {
            // received cached
        } else {
            if (!file_exists($filename)) {
                throw new \Exception('Attempted to cache '.$filename.' but file was not found');
            }
            $data = file_get_contents($filename);
            if (is_callable($callback)) {
                $data = $callback($data);
            }
            if ($apcuAvailable) {
                apcu_add($filename, $data); 
            }
        }
        return $data;
    }

    public static function clear()
    {
        return (
            function_exists('apcu_enabled') &&
            apcu_enabled() &&
            apcu_clear_cache()
        );
    }
}
