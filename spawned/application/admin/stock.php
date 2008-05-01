<?php

class stock extends Controller
{ 
  public function default_event()
  {  
    if(isset($_POST['add_stock']))
      {
	$this->add();
      }
    elseif(isset($_POST['update_stock']))
      {
	$this->update();
      }

    require('empathy/include/EntityDBMS.php');
    require(DOC_ROOT.'/storage/ProductItem.php');
    require(DOC_ROOT.'/storage/ProductAttribute.php');
    require(DOC_ROOT.'/storage/AttributeOption.php');
    require(DOC_ROOT.'/storage/StockItem.php');
 
    $p = new ProductItem($this);    
    $p->id = $_GET['id'];
    $p->load(ProductItem::$table);
    $p->category_id = $p->convertCategory();
    

    $pa = new ProductAttribute($this);
    $attributes = $pa->loadForProductIndexed($p->id);
    
    $ao = new AttributeOption($this);
    $attr_opt = $ao->build($attributes);
        
    $s = new StockItem($this);
    $stock = $s->loadAllForProduct($p->id);  


      
    $options = array();
    foreach($stock as $index => $row)
      {
	if($row['attributes'] != "")
	  {
	    $explode = explode("-", $row['attributes']);
	    foreach($explode as $expl1)
	      {
		$expl2  = explode("/", $expl1);
		$attr_id = $expl2[0];
		$opt_id = $expl2[1];
		$option[$attr_id]['option'] = $attr_opt[$attr_id]['options'][$opt_id];
		$option[$attr_id]['opt_id'] = $opt_id;
	      }
	    $stock[$index]['attributes'] = $option;
	  }
      }      
    

    $this->presenter->assign("product", $p);        
    $this->presenter->assign("attr_opt", $attr_opt);    
    $this->presenter->assign("stock", $stock);        
  }


  public function update()
  {
    foreach($_POST['update_stock'] as $el_index => $value)
      {
	//
      }

    require('empathy/include/EntityDBMS.php');
    require(DOC_ROOT.'/storage/StockItem.php');
   
    if(isset($_POST['update_stock']))
      {
	$product_id = $_POST['product_id'];
	$q = $_POST['qty'];
	$qty = $q[$el_index];

	$attribute = array();
	
	foreach($_POST as $index => $value)
	  {
	    if(preg_match('/^attr/', $index))
	      {						
		$attr_id = substr($index, 4, (strlen($index) - 4));
		$attribute[$attr_id] = $value[$el_index];								
	      }	    
	  }	

	$attributes = "";
	$i = 0;
	foreach($attribute as $index => $value)
	  {
	    $attributes .= $index."/".$value;
	    if(($i+1) < sizeof($attribute))
	      {
		$attributes .= "-";
	      }
	    $i++;
	  }	

	$s = new StockItem($this);    
	$s->attributes = $attributes;
	$s->status = 0;
	
	$s->deleteByAttribute($product_id, $qty);

	$s->insertByAttribute($product_id, $qty);

	$this->redirect("admin/stock/$product_id");
      }
  }

  
  public function remove()
  {
    require('empathy/include/EntityDBMS.php');
    require(DOC_ROOT.'/storage/ProductItem.php');

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
    require('empathy/include/EntityDBMS.php');
    require(DOC_ROOT.'/storage/ProductItem.php');
    require(DOC_ROOT.'/storage/Category.php');

    $p = new ProductItem($this);

    if(isset($_POST['submit_product']))
    {     
      $p->id = $_POST['id'];
      $p->load(ProductItem::$table);

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

    $this->presenter->assign("product", $p);

    $c = new Category($this);
    $category = $c->loadIndexed();

    $this->presenter->assign("categories", $category);

  }

  public function add()
  {
    require('empathy/include/EntityDBMS.php');
    require(DOC_ROOT.'/storage/StockItem.php');
    
    $s = new StockItem($this);

    if(isset($_POST['add_stock']))
      {
	$s->product_id = $_POST['product_id'];
	$attribute = array();
	foreach($_POST as $index => $value)
	  {
	    if(preg_match('/^add_attr/', $index))
	      {
		$attr_id = substr($index, 8, (strlen($index) - 8));
		$attribute[$attr_id] = $value;
	      }
	  }

	$attributes = "";
	$i = 0;
	foreach($attribute as $index => $value)
	  {
	    $attributes .= $index."/".$value;
	    if(($i+1) < sizeof($attribute))
	      {
		$attributes .= "-";
	      }
	    $i++;
	  }	
	$s->attributes = $attributes;
	$s->sanitize();
	$s->create($_POST['add_qty']);		
      }
    $this->redirect("admin/stock/$s->product_id");
  }



}
?>