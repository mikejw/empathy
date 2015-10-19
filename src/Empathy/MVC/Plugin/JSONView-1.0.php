<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin;


class JSONView extends Plugin implements PreEvent, Presentation
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
        $force_formatted = (defined('ELIB_FORCE_FORMATTED') && ELIB_FORCE_FORMATTED);

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

    public function onPreEvent()
    {        
        $config = json_decode('{"api": {"error_ob":"WRI\\\\GOS\\\\EROb","return_ob":"WRI\\\\GOS\\\\ROB","return_codes":"WRI\\\\GOS\\\\ReturnCodes"}}', true);
        $module = $this->bootstrap->getController()->getModule();
        
        if (in_array($module, array_keys($config))) {
            $mod_conf = $config[$module];
            $this->error_ob = $mod_conf['error_ob'];
            $this->return_ob = $mod_conf['return_ob'];
            $this->return_codes = $mod_conf['return_codes'];
            $controller = $this->bootstrap->getController();
            $controller->setPresenter($this);
        }
    }

    public function switchInternal($i)
    {
        //
    }

    public function exception($debug, $exception, $req_error)
    {    
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