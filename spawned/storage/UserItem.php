<?php
require_once(DOC_ROOT."/storage/Person.php");

class UserItem extends Person
{
  public $id;
  public $email;
  public $admin;
  public $username;
  public $password;
  public $image;


  public static $table = "user";
           
  
  public function getList()
  {
    $sql = "SELECT id, username FROM ".UserItem::$table
      ." ORDER BY username ASC";
    $error = "Could not get users.";
    $result = $this->query($sql, $error);
    while($row = mysql_fetch_array($result))
    {
      $id = $row['id'];
      $user[$id] = $row['username'];
    }
    return $user;
  }

  
  public static function getUsername($id)
  {
    $sql = "SELECT username FROM ".UserItem::$table." WHERE id = $id";
    $error = "Could not get username.";
    $result = Object::query($sql, $error);
    $row = mysql_fetch_array($result);
    return $row['username'];
  }
    
      
  public function buildInvalid($username, $password)
  {
    $this->id = 0;
    $this->username = $username;
    $this->password = $password;
  }

/*
  public function load($id)
  {
    $sql = "SELECT * FROM ".UserItem::$table
      ." WHERE id = $id";
    $error = "Could not load user.";
    $result = Object::query($sql, $error);
    if(1 == mysql_num_rows($result))
    {
      $row = mysql_fetch_array($result);
      {
	foreach($row as $index => $value)
	{
	  $this->$index = $value;	 
	}
      }
    }
  }
*/

  
  public static function getID($username, $password)
  {    
    $sql = "SELECT id FROM ".UserItem::$table
      ." WHERE username = '$username'"
      ." AND password = '$password'";
    //." AND password = '".md5($this->password)."'";
    $error = "Could not verify user.";  
    $result = Object::query($sql, $error);
    if(1 == mysql_num_rows($result))
    {
      $row =  mysql_fetch_array($result);
      return $row['id'];
    }
    else
    {
      return 0;
    }
  }

  public static function login()
  {
    $user_id = 0;
    $sql = "SELECT * FROM ".UserItem::$table." WHERE username = '".$_POST['username']."'"
      ." AND password = '".$_POST['password']."'";
    $error = "Could not login.";
    $result = Object::query($sql, $error);
    $rows = mysql_num_rows($result);
    if($rows == 1)
    {
      $row = mysql_fetch_array($result);
      $user_id = $row['id'];
    }
    
    return $user_id;    
  }

  public static function getAdmin($id)
  {
    $admin = 0;
    $sql = "SELECT admin FROM ".UserItem::$table." WHERE id = $id";
    $error = "Could not get admin code.";
    $result = Object::query($sql, $error);
    if(mysql_num_rows($result) == 1)
    {
      $row = mysql_fetch_array($result);
      $admin = $row['admin'];
    }
    return $admin;
  }
  
  public static function getAll($orderBy)
  {
    $sql = "SELECT * FROM ".UserItem::$table;
    if($orderBy != "")
    {
      $sql .= " ORDER BY '$orderBy'";
    }
    
    $error = "Could get all users.";
    $result = Object::query($sql, $error);
    $i = 0;
    while($row = mysql_fetch_array($result))
    {
      $user[$i] = $row;
      $i++;
    }
    return $user;
  }
}
?>