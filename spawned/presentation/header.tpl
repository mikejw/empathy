{*
 Copyright 2008 Mike Whiting (mail@mikejw.co.uk).
 This file is part of the Empathy MVC framework.

 Empathy is free software: you can redistribute it and/or modify
 it under the terms of the GNU Lesser General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 Empathy is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Lesser General Public License for more details.

 You should have received a copy of the GNU Lesser General Public License
 along with Empathy.  If not, see <http://www.gnu.org/licenses/>.
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>{$TITLE}</title>
<link rel="stylesheet" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/css/init.css" type="text/css" media="all" />
<link rel="stylesheet" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/css/{$NAME}.css" type="text/css" media="all" />
<link rel="stylesheet" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/css/{$module}.css" type="text/css" media="all" />
</head>

<body id="ai-em">
<div id="page">

<p class="logo"><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/"><img src="http://{$WEB_ROOT}{$PUBLIC_DIR}/img/yell_small.png" alt="" /></a></p>
<!--<p class="sub_title">Intimacy with medium.</p>-->


<ul id="nav">
<li><a {if $module eq 'news'}class="selected" {/if}href="http://{$WEB_ROOT}{$PUBLIC_DIR}/">News</a></li>
<li><a {if $module eq 'reviews'}class="selected" {/if}href="http://{$WEB_ROOT}{$PUBLIC_DIR}/reviews/">Reviews</a></li>
<li><a {if $module eq 'events'}class="selected" {/if}href="http://{$WEB_ROOT}{$PUBLIC_DIR}/events">Events</a></li>
<li><a {if $module eq 'podcast'}class="selected" {/if}href="http://{$WEB_ROOT}{$PUBLIC_DIR}/podcast">Podcast</a></li>
<li><a {if $module eq 'people'}class="selected" {/if}href="http://{$WEB_ROOT}{$PUBLIC_DIR}/people">People</a></li>
<li><a {if $module eq 'stores'}class="selected" {/if}href="http://{$WEB_ROOT}{$PUBLIC_DIR}/stores">Stores</a></li>
<li><a {if $module eq 'help'}class="selected" {/if}href="http://{$WEB_ROOT}{$PUBLIC_DIR}/help">Help</a></li>
</ul>

<div id="content">

