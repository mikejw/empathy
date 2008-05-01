<?php

class ProductItem extends EntityDBMS
{
  public $id;
  public $category_id;
  public $name;
  public $description;
  public $image;
  public $price;
  
  public static $table = "product";

  public function remove()
  {
    require(DOC_ROOT."/storage/StockItem.php");
      
    $sql1 = "DELETE FROM ".StockItem::$table. " WHERE product_id = $this->id";    
    $sql2 = "DELETE FROM ".ProductItem::$table. " WHERE id = $this->id";
    $error = "Could not delete product.";
    $this->query($sql1, $error);
    $this->query($sql2, $error);       
  }
  

  public function convertCategory()
  {
    require(DOC_ROOT."/storage/CategoryItem.php");    
    $sql = "SELECT name from ".CategoryItem::$table." WHERE id = $this->category_id";
    $error = "Could not get category name.";
    $result = $this->query($sql, $error);
    $row = mysql_fetch_array($result);
    return $row['name'];
  }
  
  public function create($category)
  {
    $sql = "INSERT INTO ".ProductItem::$table." VALUES("
      ."NULL, $category, 'New Product', 'No Description.', NULL, '0.00')";
    $error = "Could not create new product.";
    $result = $this->query($sql, $error);

    $product_id = mysql_insert_id();
    return $product_id;
  }
    
  
  public function loadAllWithStock($cat)
  {
    $product = array();
    
    require(DOC_ROOT."/storage/StockItem.php");
    require(DOC_ROOT."/storage/CategoryItem.php");
    
    $sql = "SELECT p.id, c.name AS category_id, p.name, p.description, p.image, p.price, COUNT(s.id) AS stock FROM "
      .CategoryItem::$table." c, "
      .ProductItem::$table." p"            
      ." LEFT JOIN ".StockItem::$table." s ON p.id = s.product_id WHERE p.category_id = c.id";

    if($cat != 0)
    {
      $sql .= " AND c.id = $cat";
    }

    $sql .= " GROUP BY p.id ORDER BY p.id";

    $error = "Could not fetch all products";
    $result = $this->query($sql, $error);
    
    $i = 0;
    while($row = mysql_fetch_array($result))
    {
      $product[$i] = $row;
      $i++;
    }
      
    return $product;
  }
  

}
?>