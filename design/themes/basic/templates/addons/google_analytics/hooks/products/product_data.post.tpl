{if !$details_page}
<script type="text/javascript">
//<![CDATA[
	ga('ec:addImpression', {
		'id': '{$product.product_id}',
		'name': '{$product.product}',
		'sku': '{$product.product_code}',
		'category': '{$product.product_id|fn_ga_get_main_category}',
		'brand': '{$product.product_id|fn_ga_get_product_brand}'
	});
	ga('send', 'event', 'ecommerce', 'Product Impression');
//]]>
</script>
{/if}