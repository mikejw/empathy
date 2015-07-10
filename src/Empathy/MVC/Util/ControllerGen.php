<?php

namespace Empathy\MVC\Util;

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
        $module = DOC_ROOT.'/application/'.$this->module;
        $controller = $module.'/'.$this->name.'.php'; 
        if (!file_exists($module)) {
            mkdir($module);
        }
        if (!file_exists($controller)) {        
            $file = sprintf(self::BLOB, $this->name, $this->parent);
            file_put_contents($controller, $file);
        }
    }
}
