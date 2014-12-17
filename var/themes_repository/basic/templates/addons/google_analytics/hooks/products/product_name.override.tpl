	{assign var="url" value="products.view?product_id=`$product.product_id`"|fn_url}
	<script type="text/javascript">
		var params = new Array();
		params['id'] = '{$product.product_id}';
		params['name'] = '{$product.product}';
		params['sku'] = '{$product.product_code}';
		params['category'] = '{$product.product_id|fn_ga_get_main_category}';
		params['brand'] = '{$product.product_id|fn_ga_get_product_brand}';
	</script>
    {if $show_name}
        {if $hide_links}<strong>{else}<a href="{$url}" class="product-title" onclick="ga_product_click(params, '{$url}'); return !ga.loaded;">{/if}{$product.product nofilter}{if $hide_links}</strong>{else}</a>{/if}
    {elseif $show_trunc_name}
        {if $hide_links}<strong>{else}<a href="{$url}" class="product-title" title="{$product.product|strip_tags}" onclick="ga_product_click(params, '{$url}'); return !ga.loaded;">{/if}{$product.product|truncate:45:"...":true nofilter}{if $hide_links}</strong>{else}</a>{/if}
    {/if}