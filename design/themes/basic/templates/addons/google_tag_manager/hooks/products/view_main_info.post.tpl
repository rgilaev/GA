{if $addons.google_tag_manager.tracking_code}
    <script type="text/javascript">
    $(document).ready(function() {
        ga('ec:addProduct', {
            'id': '{$product.product_id}',
            'name': '{$product.product|escape:"javascript"}',
            'sku': '{$product.product_code}',
            'category': '{$product.product_id|fn_google_tag_manager_get_main_category|escape:"javascript"}',
            'brand': '{$product.product_id|fn_google_tag_manager_get_product_brand|escape:"javascript"}'
        });
        ga('ec:setAction', 'detail');
        ga('send', 'pageview');
    });
    </script>
{/if}