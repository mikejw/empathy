<?php

// Copyright 2008-2015 Mike Whiting (mikejw3@gmail.com).
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

if(false == @include('../vendor/autoload.php')) {
    
    include('./site_down.html');
} else {
    $container = Empathy\MVC\DI::init(realpath(dirname(__FILE__).'/../'));
	$empathy = $container->get('Empathy');
	$empathy->init();
}
