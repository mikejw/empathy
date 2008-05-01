
{include file="empathy_header.tpl"}


<h1>Uh Oh</h1>
<p>This web application has encountered an error. Details follow...</p>

<div id="error">
<p><strong>Occurring on:</strong> {$app_error.3|date_format:"%d/%m/%y at %H:%M:%S"}</p>
<p><strong>In module:</strong> {$app_error.0}</p>
<p><strong>In class:</strong> {$app_error.1}</p>
<p><strong>Message:</strong> {$app_error.2}</p>
</div>
<p class="retry"><a href="http://{$failed_uri}">Retry</a></p>


{include file="empathy_footer.tpl"}