<script type="text/javascript">
	var qty = $('#qty_count_'+{$product.product_id});
	var params = new Array();
	params['id'] = '{$product.product_id}';
	params['name'] = '{$product.product}';
	params['sku'] = '{$product.product_code}';
	params['category'] = '{$product.product_id|fn_ga_get_main_category}';
	params['brand'] = '{$product.product_id|fn_ga_get_product_brand}';
	params['price'] = '{$product.price}';
    params['qty'] = qty.val();
</script>
{assign var="c_url" value=$config.current_url|escape:url}
{if $settings.General.allow_anonymous_shopping == "allow_shopping" || $auth.user_id}
	{assign var="but_click" value="`$but_onclick`; ga_cart_action(params, 'add');"}
	{include file="buttons/button.tpl" but_id=$but_id but_text=$but_text|default:__("add_to_cart") but_name=$but_name but_onclick=$but_click but_href=$but_href but_target=$but_target but_role=$but_role|default:"text"}
{else}

	{if $runtime.controller == "auth" && $runtime.mode == "login_form"}
		{assign var="login_url" value=$config.current_url}
	{else}
		{assign var="login_url" value="auth.login_form?return_url=`$c_url`"}
	{/if}

	{include file="buttons/button.tpl" but_id=$but_id but_text=__("sign_in_to_buy") but_href=$login_url but_role=$but_role|default:"text" but_name=""}
	<p>{__("text_login_to_add_to_cart")}</p>
{/if}