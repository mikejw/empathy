<?php

namespace Empathy\MVC\Util;


/**
 * Empathy ControllerGen Plugin
 * @file            Empathy/MVC/Util/ControllerGen.php
 * @description     Simple controller code generation
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
class ControllerGen
{

    const BLOB = <<<ENDBLOB
<?php

namespace Empathy\MVC\Controller;

class %s extends %s
{
    //    
}

ENDBLOB;

    protected $name;
    protected $module;
    protected $parent;

    public function __construct()
    {
        //
    }
    
    public function getModule()
    {
        return $this->module;
    }

    public function write()
    {       
        $sucess = false;
        $module = DOC_ROOT.'/application/'.$this->module;
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
