<?php

namespace Empathy\MVC;

class FileContentsCache
{
	
	public static function cachedCallback($filename, $callback = null)
	{
		$apcuAvailable = function_exists('apcu_enabled') && \apcu_enabled();
		if (!$apcuAvailable) {
			throw new \Exception('APCu is not available!');
		}

		$data = false;
        if ((false !== ($data = \apcu_fetch($filename)))) {
            // received cached
        } else {
        	//echo 'reading file';
			if (!file_exists($filename)) {
	            throw new \Exception('Attempted to cache '.$filename.' but file was not found');
    	    }

        	$data = file_get_contents($filename);
        	if (is_callable($callback)) {
        		$data = $callback($data);
        	}                
            \apcu_add($filename, $data);
        }
        return $data;
	}
}
