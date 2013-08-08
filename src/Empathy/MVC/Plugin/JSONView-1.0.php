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
        $force_formatted = (defined('ELIB_FORCE_FORMATTED') && ELIB_FORCE_FORMATTED);

        $debug_mode = $this->bootstrap->getDebugMode();

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

            if ($debug_mode || $force_formatted) {
                $jsb = new \JSBeautifier();
                echo $jsb->beautify($output);
            } else {
                echo $output;
            }
        } else {

            if ($debug_mode || $force_formatted) {
                $jsb = new \JSBeautifier();
                echo $jsb->beautify(json_encode($this->output));
            } else {
                echo json_encode($this->output);
            }
        }
    }

    /*
      public function __construct($b)
      {
      parent::__construct($b);
      //
      }
    */

    public function onPreDispatch()
    {
        //
    }

    public function switchInternal($i)
    {
        //
    }

}