


{include file="header.tpl"}

{include file="comp_admin.tpl"}

<table border=0>
<tr><th>Name</th><th>&nbsp;</th></tr>
{foreach from=$categories item=category key=cat_id}
<tr>
<td><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/category/edit/{$cat_id}/">{$category.name}</a></td>
<td>
<a class="action" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/category/edit/{$cat_id}/">Edit</a><br />
<a class="action" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/category/remove/{$cat_id}/">Remove</a>
</td>
</tr>
{/foreach}
</table>


<p>&nbsp;</p>
<p><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/category/add/">Add New</a></p>



{include file="footer.tpl"}