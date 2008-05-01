<?php

class categories extends CustomController
{

  public function default_event()
  {
    require("empathy/include/EntityDBMS.php");
    require(DOC_ROOT."/storage/CategoryItem.php");
    require(DOC_ROOT."/storage/Attribute.php");
    require(DOC_ROOT."/storage/CategoryAttribute.php");

    $c = new CategoryItem($this);
    $a = new Attribute($this);
    $ca = new CategoryAttribute($this);

    $cat = $c->loadIndexed();
    $category = array();

    foreach($cat as $id => $name)
      {
	$category[$id]['name'] = $name;
	$category[$id]['attributes'] = $ca->loadForCategory($id);
      }


    $this->presenter->assign("attributes", $a->loadIndexed());
    $this->presenter->assign("categories", $category);
  }


}


?>