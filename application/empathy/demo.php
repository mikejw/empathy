<?php

class demo extends CustomController
{ 
  public function default_event()
  {
    require('empathy/include/EntityDBMS.php');
    require('empathy/storage/Country.php');

    $country = new Country($this);

    $list = $country->build();

    $this->presenter->assign('countries', $list);          	        
  }

  public function sim_error()
  {
    $this->error('This is not a real error.');
  }

  public function null()
  {
    echo 'fake event';
  }

}
?>