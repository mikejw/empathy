

{include file="eheader.tpl"}


<header>
<p><img src="http://{$WEB_ROOT}{$PUBLIC_DIR}/img/empathy.png" alt="" width="105" /></p>



{if $error neq ''}


<h1>Server error :(</h1>

<div id="error">
<h2>{$error}</h2>
</div>

{elseif $about}

<h1>Thank you<br />for choosing Empathy.</h1>

<div id="about">
<h2>Congratulaions!</h2>
<p>You have successfully set up an empathy app.</p>
</div>


{else}

<h1>Empathy</h1>

{/if}

</header>




{include file="efooter.tpl"}
