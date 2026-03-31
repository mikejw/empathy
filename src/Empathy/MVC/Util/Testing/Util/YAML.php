<?php

declare(strict_types=1);

namespace Empathy\MVC\Util\Testing\Util;

class YAML
{
    public static function save(mixed $data, string $file, bool $append = false): void
    {
        $yaml = self::dump($data);
        $mode = 'w';

        if ($append) {
            $mode = 'a';
        }

        $fh = fopen($file, $mode);
        if ($fh === false) {
            throw new \RuntimeException('Could not open YAML file: ' . $file);
        }
        fwrite($fh, $yaml);
        fclose($fh);
    }

    public static function load(string $file): mixed
    {
        if (file_exists($file)) {
            $s = new \Spyc();
            return $s->YAMLLoad($file);
        }
        return null;
    }


    public static function dump(mixed $data): string
    {
        $s = new \Spyc();
        return $s->YAMLDump($data, 4, 60);
    }


    public static function loadString(string $data): mixed
    {
        $s = new \Spyc();
        return $s->YAMLLoadString($data);
    }
}
