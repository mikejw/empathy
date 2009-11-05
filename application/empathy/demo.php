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

namespace empathy;

class demo extends Controller
{ 
  public function default_event()
  {
    $country = new Country($this);

    $list = $country->build();

    $this->presenter->assign('countries', $list);          	        
    $this->templateFile = 'empathy.tpl';
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