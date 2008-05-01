
{include file="header.tpl"}

{include file="comp_admin.tpl"}


<div class="contain_center">
<div class="product">
<img src="http://{$WEB_ROOT}{$PUBLIC_DIR}/img/{if $product->image eq ''}spacer.gif{else}uploads/{$product->image}{/if}" alt="" width=150 height=150 />
<h2><span class="light">{$product->id}.</span> {$product->name}</h2>
<p>({$product->category_id})</p>
{$product->description}
<p>&pound;{$product->price}</p>
<div class="clear">&nbsp;</div>
</div>
</div>

<form action="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/stock/" method="post">
<table border=1>
<tr>
{foreach from=$attr_opt item=attr}
<th>{$attr.name}</th>
{/foreach}
<th>Quantity</th>
</tr>
{counter assign=i start=0}
{section name=stock_item loop=$stock}
<tr>
{foreach item=option from=$stock[stock_item].attributes key=attr_id}
<td>{$option.option}</td>
{/foreach}

<td>
<input type="text" class="input_small" value="{$stock[stock_item].qty}" name="qty[{$i}]" />
<input type="hidden" name="product_id" value="{$product->id}" />

{foreach item=option from=$stock[stock_item].attributes key=attr_id}
<input type="hidden" name="attr{$attr_id}[{$i}]" value={$option.opt_id} />
{/foreach}
<input type="submit" name="update_stock[{$i}]" value="Update" />
</td>
</tr>
{counter print=false}
{/section}

<tr>
{foreach from=$attr_opt item=attr key=attr_id}
<td>
<select name="add_attr{$attr_id}">
{foreach from=$attr.options item=option key=opt_id}
<option value={$opt_id}>{$option}</option>
{/foreach}
</select>
</td>
{/foreach}
<td>
<input type="text" class="input_small" value=1 name="add_qty" />
<input type="hidden" name="product_id" value="{$product->id}" />
<input type="submit" name="add_stock" value="Add" /></td>
</tr>
</table>
</form>




{include file="footer.tpl"}