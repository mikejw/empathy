<?php

require("empathy/include/Presenter.php");
require("Smarty/Smarty.class.php");

class SmartyPresenter extends Presenter
{
  public $smarty;

  public function __construct($internal)
  {
    $this->smarty = new Smarty();
    $this->smarty->debugging = false;
    if($internal)
      {
	$pathToEmp = explode('empathy', __FILE__);
	$this->smarty->template_dir = $pathToEmp[0]."empathy/presentation";
      }
    else
      {
	$this->smarty->template_dir = DOC_ROOT."/presentation";
      }
    $this->smarty->compile_dir = DOC_ROOT."/tpl/templates_c";
    $this->smarty->cache_dir = DOC_ROOT."/tpl/cache";
    $this->smarty->config_dir = DOC_ROOT."/tpl/configs";   

    // assign constants
    $this->assign("NAME", NAME);
    $this->assign("WEB_ROOT", WEB_ROOT);
    $this->assign("PUBLIC_DIR", PUBLIC_DIR);
    $this->assign("DOC_ROOT", DOC_ROOT);
  } 

  public function templateExists($template)
  {
    return file_exists($this->smarty->template_dir.'/'.$template);
  }

  public function assign($name, $data)
  {
    $this->smarty->assign($name, $data);
  }

  public function display($template)
  {
    $this->smarty->display($this->smarty->template_dir.'/'.$template);
  }


}

?>