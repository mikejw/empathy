<?php

class product extends CustomController
{ 
  public function default_event()
  {
    $this->redirect("admin/products");
  }


  public function add()
  {
    require("empathy/include/EntityDBMS.php");
    require(DOC_ROOT."/storage/ProductItem.php");
    require(DOC_ROOT."/storage/CategoryAttribute.php");
    require(DOC_ROOT."/storage/ProductAttribute.php");
    require(DOC_ROOT."/storage/ProductRange.php");    


    $p = new ProductItem($this);
    $r = new ProductRange($this);
    $ca = new CategoryAttribute($this);
    $pa = new ProductAttribute($this);

    $stateAny = 0;
    if(!isset($_GET['id']) || $_GET['id'] == 0)
    {
      $_GET['id'] = 1;
      $stateAny = 1;
    }

    $attribute = $ca->loadForCategory($_GET['id']);

    $product_id = $p->create($_GET['id']);

    $pa->insertForProduct($product_id, $attribute);

    $r->makeUnavailable($product_id);

    if($stateAny)
      {
	$_GET['id'] = 0;
      }
    

    $this->redirect("admin/products/".$_GET['id']);
  }


  public function attributes()
  {
    require("empathy/include/EntityDBMS.php");
    require(DOC_ROOT."/storage/Attribute.php");
    require(DOC_ROOT."/storage/ProductItem.php");
    require(DOC_ROOT."/storage/ProductAttribute.php");
    require(DOC_ROOT."/storage/StockItem.php");


    if(!isset($_GET['id']))
    {
      $_GET['id'] = $_POST['product_id'];
    }

    $p = new ProductItem($this);
    $p->id = $_GET['id'];
    $p->load(ProductItem::$table);

    $a = new Attribute($this);
    $pa = new ProductAttribute($this);


    $s = new StockItem($this);
    $stock_exists = $s->stockExists($p->id);


    if(isset($_POST['save_attr']) && !$stock_exists)
      {
	if(!isset($_POST['attribute']))
	  {
	    $_POST['attribute'] = array();
	  }

	$attribute = $_POST['attribute'];



	$product_id = $p->id;
      
	$pa->updateForProduct($product_id, $attribute);
	$this->redirect("admin/products");
      }
    else
      {
	$attr = $a->loadIndexed();
	$selected_attr = $pa->loadForProduct($p->id);
	
	$this->presenter->assign("stock_exists", $stock_exists);
	$this->presenter->assign("selected_attr", $selected_attr);
	$this->presenter->assign("attributes", $attr);
	$this->presenter->assign("product", $p);
      }
  }


  // image related
  public function unlinkImage($file)
  {
    unlink(DOC_ROOT.PUBLIC_DIR."/img/uploads/$file");
  }
    
  public function reset_image()
  {
    $data = new DataItem();
    
    if(isset($_POST['reset']) || isset($_POST['cancel']))
    {      
      $_GET['id'] = $_POST['id'];      
      $data->id = $_GET['id'];
      $data->getItem();      
      
      if(isset($_POST['reset']))
      {
	$this->unlinkImage($data->image);
	
	$data->resetImage();	
      }
      $this->redirect("admin/?section=sections&id=".$data->section_id);
    }
    
    $data->getItem();
    $this->templateFile = "data_item.tpl";
    $this->presenter->assign("operation", "Reset Image");
    $this->presenter->assign("data", $data);
    $this->setNavigation($data->section_id, $data->heading);
  }
  
  public function upload_image()
  {
    if(isset($_POST['upload']))
    {
      $_GET['id'] = $_POST['id'];
    }
    
//    $this->templateFile = "data_item.tpl";
    //   $this->presenter->assign("operation", "Upload Image");    

    require("empathy/include/EntityDBMS.php");
    require(DOC_ROOT."/storage/ProductItem.php");

    $p = new ProductItem($this);
    $p->id = $_GET['id'];
    $p->load(ProductItem::$table);
    
    $this->presenter->assign("product", $p);

    
    if(isset($_POST['upload']))
    {
      require(DOC_ROOT."/application/ImageUpload.php");
      $upload = new ImageUpload();

      $upload->upload("", false);
      
      if($upload->error != "")
      {
	$this->presenter->assign("error", $upload->error);
      }
      else
      {	
	if($p->image != "")
	{
	  $this->unlinkImage($p->image);
	}
	// update db
	$p->image = $upload->currentFile;
	$p->save(ProductItem::$table);
	$this->redirect("admin/products");
      }           
    }
  }

  

  
  public function remove()
  {
    require("empathy/include/EntityDBMS.php");
    require(DOC_ROOT."/storage/ProductItem.php");

    if(!isset($_GET['category']) || !is_numeric($_GET['category']))
    {
      $_GET['category'] = 0;
    }
    
    $p = new ProductItem($this);
    $p->id = $_GET['id'];
    $p->remove();
    $this->redirect("admin/products/?category=".$_GET['category']);    
  }


  
  
  public function edit()
  {
    require("empathy/include/EntityDBMS.php");
    require(DOC_ROOT."/storage/ProductItem.php");
    require(DOC_ROOT."/storage/CategoryItem.php");
    require(DOC_ROOT."/storage/ProductRange.php");
    require(DOC_ROOT."/storage/RangeItem.php");

    $p = new ProductItem($this);
    $pr = new ProductRange($this);

    if(isset($_POST['submit_product']))
    {     
      $p->id = $_POST['id'];

      if(!isset($_POST['range']))
	{
	  $_POST['range'] = array();
	}
      $range = $_POST['range'];
      $pr->updateForProduct($p->id, $range);


      $p->load(ProductItem::$table);;
      $p->category_id = $_POST['category'];
      $p->name = $_POST['name'];
      $p->description = $_POST['description'];
      $p->sanitize();
      $p->price = $_POST['price'];
      $p->save(ProductItem::$table);
      $this->redirect("admin/products");
    }

    

    $p->id = $_GET['id'];
    $p->load(ProductItem::$table);

    $product_ranges = $pr->loadForProduct($p->id);

    $r = new RangeItem($this);
    $ranges = $r->loadAllIndexed();


    $c = new CategoryItem($this);
    $category = $c->loadIndexed();

    $this->presenter->assign("product_ranges", $product_ranges);
    $this->presenter->assign("ranges", $ranges);

    $this->presenter->assign("product", $p);
    $this->presenter->assign("categories", $category);


  }



}
?>