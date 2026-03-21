<?php

declare(strict_types=1);

namespace Empathy\MVC\Util;

use Empathy\MVC\Config;

/**
 * Empathy ControllerGen Plugin
 * @file            Empathy/MVC/Util/ControllerGen.php
 * @description     Simple controller code generation
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class ControllerGen
{
    public const BLOB = <<<ENDBLOB
<?php

namespace Empathy\MVC\Controller;

class %s extends %s
{
    //    
}

ENDBLOB;

    protected string $name = '';

    protected string $module = '';

    protected string $parent = '';

    public function __construct()
    {
        //
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function write(): bool
    {
        $success = false;
        $module = Config::get('DOC_ROOT').'/application/'.$this->module;
        $controller = $module.'/'.$this->name.'.php';
        if (!file_exists($module)) {
            mkdir($module);
        }
        if (!file_exists($controller)) {
            $file = sprintf(self::BLOB, $this->name, $this->parent);
            if (file_put_contents($controller, $file)) {
                $success = true;
            }
        }
        return $success;
    }
}
