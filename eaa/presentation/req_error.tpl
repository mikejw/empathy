{include file="$EMPATHY_DIR/tpl/eheader.tpl"}

<div class="container">
    <p>&nbsp;</p>
    {if $code eq \Empathy\MVC\RequestException::NOT_FOUND}

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

    {elseif $code eq \Empathy\MVC\RequestException::BAD_REQUEST}

        <h1 class="fail">Bad request</h1>
        <p>That won't work. {if $error neq ''}{$error}{/if}</p>
        <p><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}">Home</a></p>

    {elseif $code eq \Empathy\MVC\RequestException::NOT_AUTHORIZED}

        <h1 class="fail">Forbidden</h1>
        <p>That won't work. {if $error neq ''}{$error}{/if}</p>
        <p><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}">Home</a></p>
    {/if}

</div>

{include file="$EMPATHY_DIR/tpl/efooter.tpl"}
