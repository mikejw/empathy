
{include file="header.tpl"}

{include file="comp_admin.tpl"}


<form action="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/range/edit/" method="post">
<p><label for="name">Name</label>
<input name="name" type="text" value="{$range->name}" /></p>

<p><label for="avail">Date Available</label>
<span id="avail">
<select name="day_avail">
{html_options options=$day selected=$avail.0}
</select> 
<select name="month_avail">
{html_options options=$month selected=$avail.1}
</select> 
<select name="year_avail">
{html_options options=$year selected=$avail.2}
</select>
</span>
</p>

<p><label for="exp">Date Expires</label>
<span id="exp">
<select name="day_exp">
{html_options options=$day selected=$exp.0}
</select> 
<select name="month_exp">
{html_options options=$month selected=$exp.1}
</select> 
<select name="year_exp">
{html_options options=$year selected=$exp.2}
</select>
</span>
</p>

<p><label for="description">Description</label>
<textarea cols=30 rows=9 name="description">{$range->description}</textarea></p>

<p><label>&nbsp;</label><input type="submit" name="submit_range" value="Save" /></p>
<input type="hidden" name="id" value="{$range->id}" />
</form>
<div class="clear">&nbsp;</div>



{include file="footer.tpl"}