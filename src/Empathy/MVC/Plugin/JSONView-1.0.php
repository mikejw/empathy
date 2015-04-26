<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin;

class JSONView extends Plugin implements PreDispatch, Presentation
{
    private $output;

    public function assign($name, $data)
    {
        $this->output = $data;
    }

    public function clear_assign($name)
    {
        $this->smarty->clear_assign($name);
    }

    public function display($template)
    {         
        // check for existence of 'force formatted' config option
        // before displaying json responses in prettified format.
        // the debug_mode boot option has stopped being used for this 
        // purpose because of cases where debug information is sought but (slower) formatting
        // is not required.
        $force_formatted = (defined('ELIB_FORCE_FORMATTED') && ELIB_FORCE_FORMATTED);

        if(!(defined('MVC_TEST_MODE') && MVC_TEST_MODE)) {
            header('Content-type: application/json');
        }

        if(is_object($this->output) &&
           (get_class($this->output) == 'ROb'||
            get_class($this->output) == 'EROb'))
        {           
            $output = (string) $this->output;

            if(false !== ($callback = $this->output->getJSONPCallback())) {
                $output = $callback.'('.$output.')';
            }

            if ($force_formatted) {
                $jsb = new \JSBeautifier();
                echo $jsb->beautify($output);
            } else {
                echo $output;
            }
        } else {

            if ($force_formatted) {
                $jsb = new \JSBeautifier();
                echo $jsb->beautify(json_encode($this->output));
            } else {
                echo json_encode($this->output);
            }
        }
    }


    public function onPreDispatch()
    {
        //
    }

    public function switchInternal($i)
    {
        //
    }
}
