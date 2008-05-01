<?php

class range extends CustomController
{
  
  public function edit()
  {
    require('empathy/include/EntityDBMS.php');
    require(DOC_ROOT.'/storage/RangeItem.php');

    $r = new RangeItem($this);

    if(isset($_POST['submit_range']))
    {     
      $r->id = $_POST['id'];
      $r->load(RangeItem::$table);

      $r->name = $_POST['name'];
      
      // validate dates
      $r->date_avail = mktime(0, 0, 0, $_POST['month_avail'], $_POST['day_avail'], $_POST['year_avail']);
      $r->date_exp = mktime(0, 0, 0, $_POST['month_exp'], $_POST['day_exp'], $_POST['year_exp']);

      $r->description = $_POST['description'];
      $r->sanitize();
      $r->save(RangeItem::$table);
      $this->redirect("admin/ranges");
    }
    else
      {	
	$r->id = $_GET['id'];
	$r->load(RangeItem::$table);
	
	$avail[0] = date("d", $r->date_avail);
	$avail[1] = date("m", $r->date_avail);
	$avail[2] = date("Y", $r->date_avail);
	$this->presenter->assign("avail", $avail);
	
	$exp[0] = date("d", $r->date_exp);
	$exp[1] = date("m", $r->date_exp);
	$exp[2] = date("Y", $r->date_exp);
	$this->presenter->assign("exp", $exp);
	
	// build date arrays
	$day[0] = "-";
	$month[0] = "-";
	$year[0] = "-";
	for($i = 0; $i < 31; $i++)
	  {
	    $day[$i+1] = $i+1; 
	  }
	$month_arr = array("January", "February", "March", "April", "May", "June", "July", "August",
			   "September", "October", "November", "December");
	$i = 1;
	for($j = 0; $j < sizeof($month_arr); $j++)
	  {
	    $month[$i] = $month_arr[$j];
	    $i++;
	  }
	$current_year = date('Y') - 2;
	for($i = 0; $i < 7; $i++)
	  {
	    $year[$current_year] = $current_year;
	    $current_year++;
	  }
      }

    $this->presenter->assign("day", $day);
    $this->presenter->assign("month", $month);
    $this->presenter->assign("year", $year);

    $this->presenter->assign("range", $r);
  }



  public function add()
  {
    require('empathy/include/EntityDBMS.php');
    require(DOC_ROOT.'/storage/RangeItem.php');
    
    $r = new RangeItem($this);
    $r->create();
    $this->redirect("admin/ranges");
  }

  public function remove()
  {
    require('empathy/include/EntityDBMS.php');
    require(DOC_ROOT.'/storage/RangeItem.php');
    
    $r = new RangeItem($this);
    $r->id = $_GET['id'];
    $r->remove();
    $this->redirect("admin/ranges");    
  }



}


?>