<?php

class stores extends Controller
{ 
  public function default_event()
  {
    require("empathy/include/EntityDBMS.php");  
    require(DOC_ROOT."/storage/StockItem.php");

    $s = new StockItem($this);
    $stock = $s->countAll();

    $this->presenter->assign("stock", $stock);    
		     
  }


}
?>