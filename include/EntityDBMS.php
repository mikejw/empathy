<?php
require("empathy/include/Entity.php");

class EntityDBMS extends Entity
{
  protected $sql;
  protected $error;
  protected $result;
  protected $rows;


  public function getRows(&$result)
  {
    if(DBMS == "MYSQL")
      {
	$this->rows = mysql_num_rows($result);
      }
  }

    
  public function dbConnect()
  {
    if(!(DBMS == "MYSQL"))
    {
      $this->controller->error("Empathy does not yet support other database management systems to MySQL.");
    }
    $server    = DB_SERVER;
    $database  = DB_NAME;
    $mysqlUser = DB_USER;
    $mysqlPass = DB_PASS;

    if(false == @mysql_connect($server,$mysqlUser,$mysqlPass))
    {
      $this->controller->error("Could not connect to database server: ".mysql_error());
    }
    if(false == @mysql_select_db($database))
    {
      $this->controller->error("Could not select database: ".mysql_error());
    }
    $this->controller->connected = true;
  }
  
  public function query($sql, $error)    
  {
    $result = NULL;
    if(!(DBMS == "MYSQL"))
    {
      $this->controller->error("Empathy does not yet support other database management systems to MySQL.");
    }
    else
      {
	$result = @mysql_query($sql);
	if($result == false)
	  {
	    $this->controller->error("[$sql]<br /><strong>MySQL</strong>: ($error): ".mysql_error());
	  }
	else
	  {
	    $this->result = $result;
	    //    $this->getRows($result);
	  }

      }

    return $result;
  }
}
?>
