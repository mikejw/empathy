<?

class SectionItem extends Entity
{
  public $id;
  public $module;
  public $type;
  public $parent_id;
  public $position;
  public $label;
  public $friendly_url;
  public $layout;
  public $hidden;
  public $owns_inline;
  public $link;
  public $banner;

  public static $table = "gt_section";

  public static function getLevel($id)
  {
    $id;
    $level = 0;
    if($id != 0)
    {
      do
      {
	$sql = "SELECT parent_id, label, friendly_url FROM ".SectionItem::$table." WHERE id = $id";
	$error = "Could not find area.";
	$result = Object::query($sql, $error);
	$row = mysql_fetch_array($result);
	$id = $row['parent_id'];
	$level++;
      } while($id != 0);
    }

    return $level;
  }

  public function insert()
  {
    $sql = "SELECT MAX(position) as max FROM ".SectionItem::$table." WHERE parent_id = $this->parent_id";
    $error = "Could not check last position.";
    $result = Object::query($sql, $error);
    $rows = mysql_num_rows($result);    
    if($rows > 0)
    {
      $row = mysql_fetch_array($result);
      $position = $row['max'] + 1;
    }
    else
    {
      $positon = 1;
    }

    if($this->parent_id == 0)
    {
      $template = "'B'";
    }
    else
    {
      $template = "DEFAULT";
    }
    
    
    $sql = "INSERT INTO ".SectionItem::$table." VALUES(";
    $sql .= "NULL, 'default', DEFAULT, $this->parent_id, $position, 'New Child Section', NULL, $template, 0, 0, NULL)";
  
    $error = "Could not insert new section.";
    Object::query($sql, $error);
    return mysql_insert_id();
  }
 
  

  public static function getDataStatus($id)
  {
    $sql = "SELECT hidden FROM ".DataItem::$table. " WHERE section_id = $id";
    $error = "Could not get data status for child section.";
    $result = Object::query($sql, $error);

    $status = 0;
    if(mysql_num_rows($result) > 0)
    {
      $allHidden = 1;
      $allShown = 1;
      while($row = mysql_fetch_array($result))
      {
	if($row['hidden'] == 0)
	{
	  $allHidden = 0;
	}
	else
	{
	  $allShown = 0;
	}
      }
      if($allHidden)
      {
	$status = 1;
      }
      elseif((!($allHidden)) && (!($allShown)))
      {
	$status = 2;
      }
      elseif($allShown)
      {
	$status = 3;
      }
      
    }
    return $status;
  }
  
  
  public function remove()
  {    
    $sql = "DELETE FROM ".SectionItem::$table." WHERE id = $this->id";
    $error = "Could not delete from section.";
    Object::query($sql, $error);

    $sql = "UPDATE ".SectionItem::$table." SET position = (position - 1) WHERE position > $this->position"
      ." AND parent_id = $this->parent_id";
    $error = "Could not adjust position values after section removal.";
    Object::query($sql, $error);
  }
  

  public function parentToGalleries()
  {
    $result = Object::query("SELECT type FROM ".SectionItem::$table." WHERE parent_id = $this->id",
			    "Could not check for galleries as sub-sections.");

    $galleries = 0;
    while($row = mysql_fetch_array($result))
    {
      if($row['type'] == "GALLERY")
      {
	$galleries = 1;
      }
    }
    return $galleries;
  }
  

  public function getItem($id)
  {
    $sql = "SELECT * FROM ".SectionItem::$table." WHERE id = $id";
    $error = "Could not load record.";
    $result = Object::query($sql, $error);
    if(1 == mysql_num_rows($result))
    {
      $row = mysql_fetch_array($result);
      foreach($row as $index => $value)
      {
	$this->$index = $value;
      }
      $this->url_name = str_replace(" ", "", $this->label);
      $this->url_name = strtolower($this->url_name);
    }
    else
    {
      //echo "Whoops!";
    }
  }


  public static function removeChildren($parent_id)
  {
    $sql = "SELECT id FROM ".SectionItem::$table." WHERE parent_id = $parent_id";
    $error = "Could not get children ids.";
    $result = Object::query($sql, $error);
    $children = "(0,";
    $i = 0;
    $j = mysql_num_rows($result);
    while($row = mysql_fetch_array($result))
    {
      $children .= $row['id'];
      if(($i+1) != $j)
      {
	$children .= ",";
      }
      $i++;
    }
    $children .= ")";

    if($children != "(0,)")
    {
      $sql = "SELECT image FROM ".DataItem::$table." WHERE section_id IN $children";
      $error = "Could not get images flagged for removal.";
      $result = Object::query($sql, $error);
      $i = 0;
      while($row = mysql_fetch_array($result))
      {
	$image[$i] = $row['image'];
      } 
    
      $sql = "DELETE FROM ".DataItem::$table." WHERE section_id IN $children";
      $error = "Could not remove children data items.";
      Object::query($sql, $error);
      $sql = "DELETE FROM ".SectionItem::$table." WHERE id IN $children";
      $error = "Could not remove children sections.";
      Object::query($sql, $error);
    }

    return $image;
  }
  
  
  public static function getURIData()
  {
    $sql = "SELECT id, label, friendly_url FROM ".SectionItem::$table;
    $error = "Could not get URI data.";
    $result = Object::query($sql, $error);
    $i = 0;
    while($row = mysql_fetch_array($result))
    {
      $uri_data[$i] = $row;
      $i++;
    }
    return $uri_data;
  }


  public static function getFirstChild($id)
  {
    $sql = "SELECT id FROM ".SectionItem::$table. " WHERE parent_id = $id LIMIT 0, 1";
    $error = "Could not get first child.";
    $result = Object::query($sql, $error);
    $row = mysql_fetch_array($result);
    return $row['id'];
  }
  
 
  // better performance through multi-join?
  public static function buildURL($id)
  {    
    $i = 0;
    $build = 1;
    while($build)
    {      
      $sql = "SELECT parent_id, label, friendly_url, link FROM ".SectionItem::$table
        ." WHERE id = $id";
      $error = "Could not build URL.";
      $result = Object::query($sql, $error);
      $row = mysql_fetch_array($result);

      if($row['friendly_url'] != NULL)
      {
	$url[$i] = $row['friendly_url'];
      }
      else
      {
	$url[$i] = $row['label'];
      }

      $id = $row['parent_id'];
      if($id == 0)
      {
	$build = 0;
      }
      
      $i++;
    }
    return $url;
  }


  // used in Navigation::buildAdminNav()
  public static function buildURLID($id)
  {    
    $i = 0;
    $build = 1;
    while($build)
    {      
      $sql = "SELECT id, parent_id, label  FROM ".SectionItem::$table
        ." WHERE id = $id";
      $error = "Could not build URL.";
      $result = Object::query($sql, $error);
      if(mysql_num_rows($result) > 0)
      {
	$row = mysql_fetch_array($result);             

	$url[$i]['id'] = $row['id'];
	$url[$i]['label'] = $row['label'];
	$id = $row['parent_id'];
      }
      else
      {
	$id = 0;
      }
      
      if($id == 0)
      {
	$build = 0;
      }
      
      $i++;
    }
    
    $url[$i]['id'] = 0;
    $url[$i]['label'] = "Top Level";
    
    return $url;
  }

   
  public function ownsInline($id)
  {
    $sql = "SELECT owns_inline FROM ".SectionItem::$table." WHERE id = $id";
    $error = "Could not determine inline menu.";
    $result = Object::query($sql, $error);
    $row = mysql_fetch_array($result);
    return $row['owns_inline'];    
  }

  public function getParentID($id)
  {
    $sql = "SELECT parent_id FROM ".SectionItem::$table." WHERE id = $id";
    $error = "Could not get parent id.";
    $result = Object::query($sql, $error);
    $row = mysql_fetch_array($result);
    return $row['parent_id'];
  }
  
  public static function getChildren($id, $hide_hidden)
  {
    $child = array();
    $sql = "SELECT * FROM ".SectionItem::$table." WHERE parent_id = $id";
    if($hide_hidden)
    {
      $sql .= " AND hidden = 0";
    }
    $sql .= " ORDER BY position";
    $error = "Could not get children.";    
    $result = Object::query($sql, $error);
    $i = 0;
    while($row = mysql_fetch_array($result))
    {
      $child[$i] = $row;
      $child[$i]['url_name'] = str_replace(" ", "", $child[$i]['label']);
      $child[$i]['url_name'] = strtolower($child[$i]['url_name']);     
      $i++;
    }  
    return $child;
  }

  public static function inlineMenu($id)   
  {
    $owns_inline = 0;
    
    $sql = "SELECT parent_id, owns_inline FROM ".SectionItem::$table." WHERE id = $id";
    $error = "Could not determine inline menu.";
    $result = Object::query($sql, $error);
    $row = mysql_fetch_array($result);
    
    if($row['owns_inline'])
    {
      $owns_inline = $id;
    }
    else
    {
      $parent_id = $row['parent_id'];
      $sql = "SELECT owns_inline FROM ".SectionItem::$table." WHERE id = $parent_id";
      $error = "Could not determine inline menu.";
      $result = Object::query($sql, $error);
      $row = mysql_fetch_array($result);
      if($row['owns_inline'])
      {
	$owns_inline = $parent_id;
      }
    }
    return $owns_inline;
  }

  public static function getItems($id)
  {
    $sql = "SELECT id, label FROM ".SectionItem::$table." WHERE parent_id = $id";
    $error = "Could not generate inline menu items.";
    $result = Object::query($sql, $error);
    $i = 0;
    while($row = mysql_fetch_array($result))
    {
      $inline[$i] = $row;
      $inline[$i]['url_name'] = str_replace(" ", "", $inline[$i]['label']);
      $inline[$i]['url_name'] = strtolower($inline[$i]['url_name']);     
      $i++;
    }
    return $inline;
  }

  public function getArea()
  {
    $area = "";
    $id = $this->id;
    do
    {
      $sql = "SELECT parent_id, label, friendly_url FROM ".SectionItem::$table." WHERE id = $id";
      $error = "Could not find area.";
      $result = Object::query($sql, $error);
      $row = mysql_fetch_array($result);
      $id = $row['parent_id'];
    } while($id != 0);
            
    if($row['friendly_url'] != "")
    {
      $area = $row['friendly_url'];
    }
    else
    {
      $area = strtolower(str_replace(" ", "", $row['label']));
    }
    
    return $area;    
  }
  
  
}
?>