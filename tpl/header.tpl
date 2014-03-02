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
<!doctype html>
<html>
<head>
<meta charset="utf-8" />
{include file="comp_title.tpl"}
{include file="comp_keywords.tpl"}
{include file="comp_description.tpl"}

<meta name="viewport" content="initial-scale=1.0, width=device-width" />
{if $environment eq 'dev'}
<link rel="stylesheet" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/css/less/init.min.css" type="text/css" />
<link rel="stylesheet/less" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/css/less/empathy.less" type="text/css" />
{else}
<link rel="stylesheet" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/css/less/{$MODULE}.css" type="text/css" media="all" />
{/if}

<script type="text/javascript" src="http://{$WEB_ROOT}{$PUBLIC_DIR}/js/jquery.min.js"></script>
{if $environment eq 'dev'}
<script type="text/javascript" src="http://{$WEB_ROOT}{$PUBLIC_DIR}/js/less.min.js?id={$dev_rand}"></script>
<script type="text/javascript" src="http://{$WEB_ROOT}{$PUBLIC_DIR}/js/common.js?id={$dev_rand}"></script>
<script type="text/javascript" src="http://{$WEB_ROOT}{$PUBLIC_DIR}/js/{$NAME}.js?id={$dev_rand}"></script>
{else}
<script type="text/javascript" src="http://{$WEB_ROOT}{$PUBLIC_DIR}/js/{$NAME}.min.js"></script>
{/if}


</head>
<body id="{$module}"{if $alt eq 1} class="alt"{/if}>

<div id="top_bar">
<span>Version {$MVC_VERSION}</span>
</div>

<div id="page">
