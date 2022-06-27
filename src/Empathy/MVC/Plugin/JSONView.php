<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin;
use Empathy\MVC\Testable;

/**
 * Empathy JSONView Plugin
 * @file            Empathy/MVC/Plugin/JSONView.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class JSONView extends Plugin implements PreEvent, Presentation
{
    private $output;
    private $error_ob;
    private $return_ob;
    private $return_codes;
 


    public function assign($name, $data, $no_array = false)
    {
        if ($no_array) {
            $this->output = $data;
        } else {
            if (isset($this->object) && is_object($this->output)) {
                $this->clearVars();
            }
            $this->output[$name] = $data;
        }
    }


    public function display($template, $internal = false)
    {
        // check for existence of 'force formatted' config option
        // before displaying json responses in prettified format.
        // the debug_mode boot option has stopped being used for this
        // purpose because of cases where debug information is sought but (slower) formatting
        // is not required.
        $force_formatted = (defined('ELIB_FORCE_FORMATTED') && ELIB_FORCE_FORMATTED);
        
        Testable::header('Content-type: application/json');

        if (is_object($this->output) &&
           (get_class($this->output) ==  $this->return_ob ||
            get_class($this->output) == $this->error_ob)) {
            $output = (string) $this->output;

            if (get_class($this->output) == $this->error_ob) {
                $header = 'HTTP/1.1 '
                    .$this->output->getCode()
                    .' '
                    .$this->return_codes::getName($this->output->getCode())
                ;
                Testable::header($header);
            }

            if (false !== ($callback = $this->output->getJSONPCallback())) {
                $output = $callback.'('.$output.')';
            }

            if ($force_formatted) {
                echo json_decode(json_encode($output, JSON_PRETTY_PRINT));
            } else {
                echo $output;
            }
        } else {
            if ($force_formatted) {
                echo json_encode((array)$this->output, JSON_PRETTY_PRINT);
            } else {
                echo json_encode((array)$this->output);
            }
        }
    }


    public function onPreEvent()
    {
        $module = $this->bootstrap->getController()->getModule();

        if (isset($this->config)) {
            if (count($this->config) === 1) {
                $this->config[0] = $this->config;
            }
            foreach ($this->config as $item => $value) {
                if (in_array($module, array_keys($value))) {
                    $mod_conf = $value[$module];
                    $this->error_ob = isset($mod_conf['error_ob'])
                        ? $mod_conf['error_ob']
                        : 'Empathy\MVC\Plugin\JSONView\EROb';
                    $this->return_ob = isset($mod_conf['return_ob'])
                        ? $mod_conf['return_ob']
                        : 'Empathy\MVC\Plugin\JSONView\ROb';
                    $this->return_codes = isset($mod_conf['return_codes'])
                        ? $mod_conf['return_codes']
                        : 'Empathy\MVC\Plugin\JSONView\ReturnCodes';
                    $controller = $this->bootstrap->getController();
                    $controller->setPresenter($this);
                }
            }
        }
    }

    public function exception($debug, $exception, $req_error)
    {
        $rc = $this->return_codes;
        $e_ob = $this->error_ob;

        if (!$debug) {
            $r = new $e_ob($rc::Internal_Server_Error, 'Server error.');
        } else {
            $r = new $e_ob($rc::Internal_Server_Error, 'Exception: ' .$exception->getMessage(), 'SERVER_ERROR_EXPLICIT');
        }

        $header = 'HTTP/1.1 '
            .$rc::Internal_Server_Error
            .' '
            .$this->return_codes::getName($rc::Internal_Server_Error);
        Testable::header($header);

        $this->assign('default', $r, true);
        $this->display('');
    }

   
    public function getVars()
    {
        return $this->output;
    }

    public function clearVars()
    {
        unset($this->output);
    }
}
