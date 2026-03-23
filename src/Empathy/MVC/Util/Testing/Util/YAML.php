<?php

declare(strict_types=1);

namespace Empathy\MVC\Util\Testing\Util;

class YAML
{
    public static function save($data, $file, $append = false)
    {
        $yaml = self::dump($data);
        $mode = 'w';

        if ($append) {
            $mode = 'a';
        }

        $fh = fopen($file, $mode);
        fwrite($fh, (string) $yaml);
        fclose($fh);
    }

    public static function load($file)
    {
        if (file_exists($file)) {
            $s = new \Spyc();
            return $s->YAMLLoad($file);
        }
    }


    public static function dump($data)
    {
        $s = new \Spyc();
        return $s->YAMLDump($data, 4, 60);
    }


    public static function loadString($data)
    {
        $s = new \Spyc();
        return $s->YAMLLoadString($data);
    }
}
