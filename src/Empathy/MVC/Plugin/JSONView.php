<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin;

/**
 * Empathy JSONView Plugin
 * @file            Empathy/MVC/Plugin/JSONView.php
 * @description
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
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

        if (!(defined('MVC_TEST_MODE') && MVC_TEST_MODE)) {
            header('Content-type: application/json');
        }

        if (is_object($this->output) &&
           (get_class($this->output) ==  $this->return_ob ||
            get_class($this->output) == $this->error_ob)) {
            $output = (string) $this->output;

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

        if (isset($this->config) && in_array($module, array_keys($this->config))) {
            $mod_conf = $this->config[$module];
            $this->error_ob = $mod_conf['error_ob'];
            $this->return_ob = $mod_conf['return_ob'];
            $this->return_codes = $mod_conf['return_codes'];
            $controller = $this->bootstrap->getController();
            $controller->setPresenter($this);
        }
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
        $this->assign('default', $r, true);
        $this->display();
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
