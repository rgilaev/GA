<script type="text/javascript">
$(document).ready(function() {ldelim}
	ga('ec:addProduct', {ldelim}
		'id': '{$product.product_id}',
		'name': '{$product.product}',
		'sku': '{$product.product_code}',
		'category': '{$product.product_id|fn_ga_get_main_category}',
		'brand': '{$product.product_id|fn_ga_get_product_brand}'
	{rdelim});
	ga('ec:setAction', 'detail');
	ga('send', 'pageview');
{rdelim});
</script>