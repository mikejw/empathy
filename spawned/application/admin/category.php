<?php

class category extends CustomController
{


  public function edit()
  {
    require("empathy/include/EntityDBMS.php");
    require(DOC_ROOT."/storage/CategoryItem.php");
    require(DOC_ROOT."/storage/Attribute.php");
    require(DOC_ROOT."/storage/CategoryAttribute.php");

    if(!isset($_GET['id']))
      {
	$_GET['id'] = $_POST['category_id'];
      }

    $c = new CategoryItem($this);
    $a = new Attribute($this);
    $ca = new CategoryAttribute($this);

    $c->id = $_GET['id'];
    $c->load(CategoryItem::$table);

    if(isset($_POST['save_cat']))
      {
	if(!isset($_POST['attribute']))
	  {
	    $_POST['attribute'] = array();
	  }
	$attribute = $_POST['attribute'];

	$category_id = $c->id;

	$c->name = $_POST['name'];
	$c->sanitize();
	$c->save(CategoryItem::$table);

	$ca->updateForCategory($category_id, $attribute);
	$this->redirect("admin/categories");

      }


    $category = array();
    $category[$c->id]['name'] = $c->name;
    $category[$c->id]['attributes'] = $ca->loadForCategory($c->id);


    $this->presenter->assign("attributes", $a->loadIndexed());
    $this->presenter->assign("categories", $category);
  }


}


?>