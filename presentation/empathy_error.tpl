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