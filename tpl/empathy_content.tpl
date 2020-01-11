


<p><img src="http://{$WEB_ROOT}{$PUBLIC_DIR}/img/empathy.png" alt="" width="105" /></p>
<p><span>Version {$MVC_VERSION}</span></p>


{if $error neq ''}
    <h1>Server error :(</h1>

    {if $error neq ''}
        <p>&nbsp;</p>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong>
            <p>{$error}</p>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    {/if}

{elseif $about}

    <h2 class="h3 mb-3 font-weight-normal">
        Thank you for choosing Empathy<br />
        Congratulations!
    </h2>
    <p>You have successfully set up an empathy app.</p>

{elseif $status neq ''}

    <h1>Status is {$status}</h1>

{else}

    <h1>Empathy</h1>

{/if}


