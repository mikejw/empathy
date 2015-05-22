


{include file="$DOC_ROOT/vendor/mikejw/empathy/tpl/eheader.tpl" mode="default"}


<header>
<p><img src="http://{$WEB_ROOT}{$PUBLIC_DIR}/img/empathy.png" alt="" width="105" /></p>
<h1>Empathy</h1>
</header>



<div id="content_inner">

{if $code eq 0}


{if $module eq 'blog' and $event eq 'tags'}
<h1 class="fail">Not found</h1>
<p>Please try a different combination of tags.</p>
{if $internal_referrer}
<p>&laquo; <a class="back" href="http://{$WEB_ROOT}{$PUBLIC_DIR}">Back</a><p>
{/if}

{else}

<h1 class="fail">Not found</h1>
<p>Sorry but the requested page has been moved or does not exist.</p>
<p><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}">Home</a></p>

{/if}

{elseif $code eq 1}

<h1 class="fail">Bad request</h1>
<p>That won't work. {$error}.</p>
<p><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}">Home</a></p>



{/if}



</div>

{include file="$DOC_ROOT/vendor/mikejw/empathy/tpl/efooter.tpl"}
