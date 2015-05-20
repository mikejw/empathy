<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>Empathy {$MVC_VERSION}</title>

<meta name="viewport" content="initial-scale=1.0, width=device-width" />
{if $environment eq 'dev'}
<link rel="stylesheet" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/css/less/init.min.css" type="text/css" />
<link rel="stylesheet/less" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/css/less/empathy.less" type="text/css" />
{else}
<link rel="stylesheet" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/css/less/empathy.css" type="text/css" media="all" />
{/if}

<script type="text/javascript" src="http://{$WEB_ROOT}{$PUBLIC_DIR}/js/jquery.min.js"></script>
{if $environment eq 'dev'}
<script type="text/javascript" src="http://{$WEB_ROOT}{$PUBLIC_DIR}/js/less.min.js?id={$dev_rand}"></script>
<script type="text/javascript" src="http://{$WEB_ROOT}{$PUBLIC_DIR}/js/common.js?id={$dev_rand}"></script>
<script type="text/javascript" src="http://{$WEB_ROOT}{$PUBLIC_DIR}/js/{$NAME}.js?id={$dev_rand}"></script>
{else}
<script type="text/javascript" src="http://{$WEB_ROOT}{$PUBLIC_DIR}/js/{$NAME}.min.js"></script>
{/if}

{if $mode eq 'site'}
<link href='http://fonts.googleapis.com/css?family=News+Cycle|Quattrocento+Sans' rel='stylesheet' type='text/css' />
{/if}

</head>
<body id="{$module}"{if $mode neq ''} class="{$mode}"{/if}>

<div id="top_bar">
<span>Version {$MVC_VERSION}</span>
</div>

<div id="page">
