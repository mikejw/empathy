


{include file="header.tpl"}

{include file="comp_admin.tpl"}

<table border=0>
<tr><th>Name</th><th>Date Available</th><th>Date Expires</th><th>Description</th><th>&nbsp;</th></tr>
{section name=range loop=$ranges}
<tr>
<td><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/range/edit/{$ranges[range].id}">{$ranges[range].name}</a></td>
<td>{$ranges[range].date_avail|date_format:"%d/%m/%Y"}</td>
<td>{$ranges[range].date_exp|date_format:"%d/%m/%Y"}</td>
<td>{$ranges[range].description|truncate:30:"..."}</td>
<td>
<a class="action" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/range/edit/{$ranges[range].id}/">Edit</a><br />
<a class="action" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/range/remove/{$ranges[range].id}/">Remove</a>
</td>
</tr>
{/section}
</table>

<p><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/range/add/">Add New</a></p>



{include file="footer.tpl"}