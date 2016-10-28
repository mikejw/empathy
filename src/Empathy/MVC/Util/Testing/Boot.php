<?php

namespace Empathy\MVC\Util\Testing;


/**
 * Empathy test suite boot loader
 * @file            Empathy/MVC/Util/Testing/Boot.php
 * @description     
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
class Boot
{
    private static $base;
    
    public static function init($base)
    {   
        self::$base = $base;
        spl_autoload_register(array('\Empathy\MVC\Util\Testing\Boot', 'loadClass'));
    }


    public static function loadClass($class)
    {
        if (strpos($class, 'ESuite') === 0) {

            $class = str_replace('ESuite\\', '', $class);
            $class = str_replace('\\', '/', $class);
            $class_file = self::$base."/t/$class.php";
            
            if (!@include($class_file)) {
                echo '[[['.$class_file.']]]';
                throw new \Exception('Could not include class '.$class_file);
            }
        }
        
    }

}

