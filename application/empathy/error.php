<?php

class error extends CustomController
{ 
  public function default_event()
  {
    $this->templateFile = "empathy_error.tpl";
    $this->presenter->assign("failed_uri", str_replace("&", "&amp;", $_SESSION['failed_uri']));
   
  }


}
?>