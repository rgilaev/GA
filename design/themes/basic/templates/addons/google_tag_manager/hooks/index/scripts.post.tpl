{if $addons.google_tag_manager.tracking_code}
    {*script src="js/addons/google_analitycs/google_analitycs.js"*}
    {if $runtime.controller == 'categories'}
        {assign var="list" value=$smarty.request.category_id|fn_get_category_name}
        {if !$list}
            {assign var="list" value="Category view page"}
        {/if}
    {elseif $runtime.controller == 'products' && $runtime.mode == 'search'}
        {assign var="list" value="Search page"}
    {/if}
    <script type="text/javascript">
    (function(i,s,o,g,r,a,m){
        i['GoogleAnalyticsObject']=r;
        i[r]=i[r]||function(){$ldelim}(i[r].q=i[r].q||[]).push(arguments){$rdelim},i[r].l=1*new Date();
        a=s.createElement(o), m=s.getElementsByTagName(o)[0];
        a.async=1;
        a.src=g;
        m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    {$url = fn_url($config.current_url, 'C', 'rel')}
    ga('create', '{$addons.google_tag_manager.tracking_code}', 'auto');
    ga('require', 'ec');
    ga('send', 'pageview');

    var page = '{$list}';
    function ga_product_click(params, url) {
        ga('ec:addProduct', {
            'id': params['id'],
            'name': params['name'],
            'sku': params['sku'],
            'category': params['category'],
            'brand': params['brand']
        });
        ga('ec:setAction', 'click', {ldelim}list: page{rdelim});
        ga('send', 'event', 'Products', 'click', 'Product click', {
            'hitCallback': function() {
                document.location = url;
            }
        });
    }

    function ga_cart_action(params, mode) {
        ga('ec:addProduct', {
            'id': params['id'],
            'name': params['name'],
            'sku': params['sku'],
            'category': params['category'],
            'brand': params['brand'],
            'price': params['price'],
            'quantity': params['qty']
        });
        ga('ec:setAction', mode);
        ga('send', 'event', 'Products', 'click', 'Add to cart');
    }
    </script>

    {if $runtime.controller == 'checkout' && $runtime.mode == 'checkout' && $smarty.session.init_checkout != 1}
        <script type="text/javascript">
        //<![CDATA[
        {foreach from=$cart_products item="p"}
            ga('ec:addProduct', {
                'id': '{$p.product_id}',
                'name': '{$p.product|escape:"javascript"}',
                'category': '{$p.product_id|fn_google_tag_manager_get_main_category|escape:"javascript"}',
                'brand': '{$p.product_id|fn_google_tag_manager_get_product_brand|escape:"javascript"}',
                'price': '{$p.price}',
                'quantity': '{$p.amount}'
            });
        {/foreach}
        ga('ec:setAction', 'checkout');
        ga('send', 'event', 'checkout', 'init');
        //]]>
        </script>
        {"1"|fn_google_tag_manager_init_checkout}
    {/if}
{/if}