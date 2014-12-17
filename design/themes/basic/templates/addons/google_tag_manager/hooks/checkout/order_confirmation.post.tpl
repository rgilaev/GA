{if $addons.google_tag_manager.tracking_code}
    <script type="text/javascript">
        ga('require', 'ecommerce', 'ecommerce.js');
        {foreach from=$orders_info item="ga_order_info"}
            ga('ecommerce:addTransaction', {
                'id': '{$ga_order_info.order_id}',
                'affiliation': '{$ga_order_info.ga_company_name|escape:"javascript"}',
                'revenue': '{$ga_order_info.total}',
                'shipping': '{$ga_order_info.shipping_cost}',
                'tax': '{$ga_order_info.tax_subtotal}',
                'currency': '{$ga_order_info.secondary_currency}'
            });

            {foreach from=$ga_order_info.products item="product" key="key"}
            ga('ecommerce:addItem', {
                'id': '{$ga_order_info.order_id}',
                'name': '{$product.product|escape:"javascript"}',
                'sku': '{$product.product_code|escape:"javascript"}',
                'category': '{$product.ga_category_name|escape:"javascript"}',
                'price': '{$product.price}',
                'quantity': '{$product.amount}'
            });
            {/foreach}
        {/foreach}
        ga('ecommerce:send');
        ga('ecommerce:clear');
    </script>
{if}