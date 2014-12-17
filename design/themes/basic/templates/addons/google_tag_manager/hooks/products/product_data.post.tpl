{if $addons.google_tag_manager.tracking_code && !$details_page}
<script type="text/javascript">
//<![CDATA[
	ga('ec:addImpression', {
		'id': '{$product.product_id}',
		'name': '{$product.product|escape:"javascript"}',
		'sku': '{$product.product_code}',
		'category': '{$product.product_id|fn_google_tag_manager_get_main_category|escape:"javascript"}',
		'brand': '{$product.product_id|fn_google_tag_manager_get_product_brand|escape:"javascript"}'
	});
	ga('send', 'event', 'ecommerce', 'Product Impression');
//]]>
</script>
{/if}