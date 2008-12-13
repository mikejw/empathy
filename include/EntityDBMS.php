<?php
  // Copyright 2008 Mike Whiting (mail@mikejw.co.uk).
  // This file is part of the Empathy MVC framework.

  // Empathy is free software: you can redistribute it and/or modify
  // it under the terms of the GNU Lesser General Public License as published by
  // the Free Software Foundation, either version 3 of the License, or
  // (at your option) any later version.

  // Empathy is distributed in the hope that it will be useful,
  // but WITHOUT ANY WARRANTY; without even the implied warranty of
  // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  // GNU Lesser General Public License for more details.

  // You should have received a copy of the GNU Lesser General Public License
  // along with Empathy.  If not, see <http://www.gnu.org/licenses/>.

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
      $this->controller->error("Could not connect to database server: ".mysql_error(), 0);
    }
    if(false == @mysql_select_db($database))
    {
      $this->controller->error("Could not select database: ".mysql_error(), 0);
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
	    $this->controller->error("[$sql]<br /><strong>MySQL</strong>: ($error): ".mysql_error(), 0);
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
