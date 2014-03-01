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
{include file="header.tpl"}

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




{include file="footer.tpl"}
