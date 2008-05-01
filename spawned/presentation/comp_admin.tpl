

<h2 style="text-align: center;">
{if $class neq 'admin'}
<a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/">Admin</a>
{else}Admin{/if}


{if $class eq 'products'}
 / Products

{elseif $class eq 'product'}
 / <a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/products/">Products</a>


{if $event eq 'edit'}
/ Edit Product
{elseif $event eq 'upload_image'}
/ Upload Product Image
{elseif $event eq 'attributes'}
/ Product Attributes

{/if}

{elseif $class eq 'stock'}
/ <a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/products/">Products</a>
/ Product Stock
{elseif $class eq 'ranges'}
/ Product Ranges
{elseif $class eq 'range'}
/ <a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/ranges/">Product Ranges</a>
/ Edit Range
{elseif $class eq 'categories'}
/ Categories
{/if}

</h2>

<p>&nbsp;</p>


