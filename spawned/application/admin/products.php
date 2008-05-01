<?php

class products extends CustomController
{

  public function default_event()
  {
    require("empathy/include/EntityDBMS.php");
    require(DOC_ROOT."/storage/ProductItem.php");
//    require(DOC_ROOT."/storage/Category.php");

    $p = new ProductItem($this);

    if(isset($_GET['id']) && is_numeric($_GET['id']))
    {
      $showCat = $_GET['id'];
    }
    else
    {
      $showCat = 0;
    }
    
    $product = $p->loadAllWithStock($showCat);

    $this->presenter->assign("products", $product);

    
    $c = new CategoryItem($this);
    $category = $c->loadIndexed();
    $category = array_reverse($category, true); 
    $category[0] = "Any";
    $category = array_reverse($category, true); 
    
    $this->presenter->assign("categories", $category);

    $this->presenter->assign("category", $showCat);
    
  }




}
?>