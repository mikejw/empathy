


{include file="header.tpl"}

{include file="comp_admin.tpl"}


{foreach from=$categories item=category key=cat_id}
<form action="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/category/edit/" method="post">
<p><label for="name">Name</label>
<input name="name" type="text" value="{$category.name}" /></p>
<p>Default Attributes</p>
{foreach from=$attributes item=attribute key=attr_id}
<p><label for="attr_{$attr_id}">{$attribute}</label>
<input id="attr_{$attr_id}" type="checkbox" name="attribute[]" value={$attr_id} {if in_array($attr_id, $category.attributes)}checked{/if} />
</p>
{/foreach}


<p>
<input type="hidden" name="category_id" value="{$cat_id}" />
<input type="submit" value="Save" name="save_cat" />
</p>

</form>
<div class="clear">&nbsp;</div>
{/foreach}

<p><a href="http://localhost/shop/public_html/admin/range/add/">Add New</a></p>



{include file="footer.tpl"}