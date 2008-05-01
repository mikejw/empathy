
{include file="header.tpl"}

{include file="comp_admin.tpl"}

<form action="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/products/" method="get">
<p><label for="id">Limit category to: </label>
<select name="id" onchange="javascript: this.form.submit();">
{html_options options=$categories selected=$category}
</select></p>
</form>
<div class="clear">&nbsp;</div>

{if sizeof($products) < 1}
<p>No products to display.</p>
{else}
<table border=0>
<tr>
<th>ID</th><th>Category</th><th>Name</th><th>Description</th><th>Image</th><th>Price</th><th>Stock</th><th>&nbsp;</th>
</tr>
{section name=product_item loop=$products}
<tr>
<td class="light">{$products[product_item].id}</td>
<td>{$products[product_item].category_id}</td>
<td><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/product/edit/{$products[product_item].id}/">{$products[product_item].name}</a></td>
<td>{$products[product_item].description|truncate:20:"..."}</td>
<td>
<img src="http://{$WEB_ROOT}{$PUBLIC_DIR}/img/{if $products[product_item].image eq ''}spacer.gif{else}uploads/{$products[product_item].image}{/if}" alt="" width=66 height=66/></td>
<td>{$products[product_item].price}</td>
<td>{$products[product_item].stock}</td>
<td>
<a class="action" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/product/edit/{$products[product_item].id}/">Edit</a><br />
<a class="action" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/product/attributes/{$products[product_item].id}/">Attributes</a><br />
<a class="action" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/stock/{$products[product_item].id}/">Stock</a><br />
<a class="action" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/product/upload_image/{$products[product_item].id}/">Upload Image</a><br />
<a class="action" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/product/remove/{$products[product_item].id}/?category={$category}/">Remove</a>

</td>
</tr>
{/section}
</table>

{/if}

<p><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/product/add/{$category}/">Add New</a></p>

{include file="footer.tpl"}