<?php

class ranges extends CustomController
{

  public function default_event()
  {
    require('empathy/include/EntityDBMS.php');
    require(DOC_ROOT.'/storage/RangeItem.php');

    $r = new RangeItem($this);
    $this->presenter->assign('ranges', $r->loadAll());

  }


}


?>