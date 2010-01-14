<?php
  // Copyright 2008 Mike Whiting (mail@mikejw.co.uk).
  // This file is part of the Empathy MVC framework.

  // Empathy is free software: you can redistribute it and/or modify
  // it under the terms of the GNU Lesser General Public License as published by
  // the Free Software Foundation, either version 3 of the License, or
  // (at your option) any later version.

  // Empathy is distributed in the hope that it will be useful,
  // but WITHOUT ANY WARRANTY; without even the implied warranty of
  // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  // GNU Lesser General Public License for more details.

  // You should have received a copy of the GNU Lesser General Public License
  // along with Empathy.  If not, see <http://www.gnu.org/licenses/>.

namespace Empathy;

class Bootstrap
{
  private $controller = null;
  private $defaultModule;
  private $dynamicModule;
  private $uri;
  private $mvc;

  public function __construct($bootOptions, $e)
  {    
    $this->mvc = $e;
    if(isset($bootOptions['default_module']))
      {
	$this->defaultModule = $bootOptions['default_module'];
      }
    if(isset($bootOptions['dynamic_module']))
      {
	$this->dynamicModule = $bootOptions['dynamic_module'];
      }
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
  } 

  public function dispatch()
  {
    $this->uri = new URI($this->defaultModule, $this->dynamicModule);
    $error = $this->uri->getError();

    if($error == URI::MISSING_CLASS
       && isset($this->dynamicModule)
       && $this->dynamicModule != '')
      {	
	    $error = $this->uri->dynamicSection();	
      }    
    if($error > 0)
      {
	throw new Exception('Dispatch error '.$error.' : '.$this->uri->getErrorMessage());
      }
    $controller_name = $this->uri->getControllerName();
    $this->controller = new $controller_name($this->uri->getError(), $this->uri->getCliMode(), false);     
    $this->controller->$_GET['event']();
    if($this->mvc->hasErrors())
      {	
	throw new ErrorException($this->mvc->errorsToString());
      }
    else
      {
	$this->display(false);    
      }
  }

  public function dispatchException($e)
  {    
    $this->controller = new Controller(0, false, true);    
    $this->controller->setTemplate('empathy.tpl');
    $this->controller->assign('error', $e->getMessage());    
    $this->display(true);    
  }
  
  private function display($i)
  {    
    /*
    if(PNG_OUTPUT == 1)
      {
	$this->controller->presenter->loadFilter('output', 'png_image');
      }
    */
    $this->controller->initDisplay($i);
  }
}
?>