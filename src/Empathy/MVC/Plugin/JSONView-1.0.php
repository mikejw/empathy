<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin;

class JSONView extends Plugin implements PreDispatch, Presentation
{
    private $output;
    private $error_ob;
    private $return_ob;
    private $return_codes;

   
    public function assign($name, $data)
    {
        $this->output = $data;
    }

    public function clear_assign($name)
    {
        $this->smarty->clear_assign($name);
    }

    public function display($template = null)
    {  
        $this->initDeps();
        $force_formatted = (defined('ELIB_FORCE_FORMATTED') && ELIB_FORCE_FORMATTED);

        //$debug_mode = $this->bootstrap->getDebugMode();

        if(!(defined('MVC_TEST_MODE') && MVC_TEST_MODE)) {
            header('Content-type: application/json');
        }

        if(is_object($this->output) &&
           (get_class($this->output) == $this->return_ob ||
            get_class($this->output) == $this->error_ob))
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

    private function initDeps()
    {
        $module = $this->bootstrap->getController()->getModule();

        $app_mod = $this->bootstrap->getApiMods();
        if ($app_mod['name'] != $module) {
            throw new \Exception('module and api config mismatch');
        }

        $this->error_ob = $app_mod['error_ob'];
        $this->return_ob = $app_mod['return_ob'];
        $this->return_codes = $app_mod['return_codes'];
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

    public function exception($debug, $exception, $req_error)
    {        
        $this->initDeps();
        $rc = $this->return_codes;
        $e_ob = $this->error_ob;

        if (!$debug) {
            $r = new $e_ob($rc::SERVER_ERROR, 'Server error.');
        } else {
           
            $r = new $e_ob(999, 'Exception: ' .$exception->getMessage(), 'SERVER_ERROR_EXPLICIT');
        }
        $this->assign('default', $r);
        $this->display();
    }


}