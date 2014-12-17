{if !$cart.products.$key.extra.configuration}
    {if $cart.products.$key.extra.points_info.price}
    <div class="ty-reward-points__product-info">
        <label for="price_in_points_{$key}" class="ty-control-group__label">{__("price_in_points")}:</label>
        <span class="ty-control-group__item" id="price_in_points_{$key}">{$cart.products.$key.extra.points_info.display_price}&nbsp;{__("points_lower")}</span>
    </div>
    {/if}
    {if $cart.products.$key.extra.points_info.reward}
    <div class="ty-reward-points__product-info">
        <label for="reward_points_{$key}" class="ty-control-group__label">{__("reward_points")}:</label>
        <span class="ty-control-group__item" id="reward_points_{$key}">{$cart.products.$key.extra.points_info.reward}&nbsp;{__("points_lower")}</span>
    </div>
    {/if}
{/if}