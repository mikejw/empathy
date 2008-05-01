<?php

class StockItem extends EntityDBMS
{
  public $id;
  public $product_id;
  public $attributes;
  public $status;
  
  public static $table = "stock_item";

  public function create($qty)
  {    
    $sql = "INSERT INTO ".StockItem::$table." VALUES("
      ."NULL, $this->product_id, '$this->attributes', DEFAULT)";
    $error = "Could not create new stock item.";
    for($i = 0; $i < $qty; $i++)
      {
	$this->query($sql, $error);
      }
  }


  public function stockExists($product_id)
  {
    $stock = 0;
    $sql = "SELECT * FROM ".StockItem::$table." WHERE product_id = $product_id";
    $error = "Could not check for stock.";
    $result = $this->query($sql, $error);
    $rows = mysql_num_rows($result);
    
    $stock = ($rows > 0);
    
    return $stock;
  }


  public function deleteByAttribute($product_id, $qty)
  {
    $sql = "SELECT * FROM ".StockItem::$table." WHERE product_id = $product_id";
    $sql .= " AND attributes = '$this->attributes'";

    $error = "Could not get matching stock items";
    $result = $this->query($sql, $error);
    
    $matching = "(0,";
    $i = 0;
    $rows = mysql_num_rows($result);
    while($row = mysql_fetch_array($result))
      {
	$matching .= $row['id'];
	if(($i+1) < $rows)
	  {
	    $matching .= ",";
	  }	
	$i++;
      }
    $matching .= ")";


    $sql = "DELETE FROM ".StockItem::$table." WHERE id IN $matching";
    $error = "Could not remove stock items";
    $this->query($sql, $error);
  }

  public function insertByAttribute($product_id, $qty)
  {
    $sql = "INSERT INTO ".StockItem::$table." VALUES(";
    $sql .= "NULL, $product_id, '$this->attributes', DEFAULT)";

    $error = "Could not insert stock item.";

    for($i = 0; $i < $qty; $i++)
      {
	$this->query($sql, $error);
      }
  }


  public function countAll()
  {
    $stock = 0;
    $sql = "SELECT COUNT(*) AS stock FROM ".StockItem::$table;
    $error = "Could not count stock items.";
    $result = $this->query($sql, $error);
    $row = mysql_fetch_array($result);
    $stock = $row['stock'];
    return $stock;
  }

  public function loadAllForProduct($id)
  {
    $built = array();
    $sql = "SELECT * FROM ".StockItem::$table." WHERE product_id = $id";
    $error = "Could not get stock items.";
    $result = $this->query($sql, $error);


    $i = 0;
    while($row = mysql_fetch_array($result))
    {
      $built[$i] = $row;      
      $i++;
    }

    $new  = array();
    $i = 0;    
    foreach($built as $row)
    {
      $foundAt = 0;
      if($this->containedIn($row, $new, $foundAt))
      {
	$new[$foundAt]['qty']++;
      }
      else
      {
	// insert row
	$new[$i] = $row;
	$new[$i]['qty'] = 1;
	$i++;
      }
    }
    return $new;    
  }
  


  public function containedIn($row, $array, &$foundAt)
  {
    $match = 0;
    
    if(sizeof($array) > 0)
    {
      $j = 0;
      foreach($array as $newRow)
      {
	$rowMatch = 1;  
	$i = 2; // start matching after product_id
	while($i < (sizeof($newRow) / 2) - 1)
	{
	  if($row[$i] != $newRow[$i])
	  {
	    $rowMatch = 0;
	  }
	  $i++;
	}
	
	if($rowMatch == 1)
	{
	  $match = 1;
	  $foundAt = $j;
	}
	
	$j++;
      }

    }
    return $match;
  }

}
?>