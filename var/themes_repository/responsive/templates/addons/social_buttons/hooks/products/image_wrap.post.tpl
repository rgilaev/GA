{if $display_button_block}
    <ul class="ty-social-buttons ty-social-buttons__list">
    {foreach from=$provider_settings item="provider_data"}
        {if $provider_data && $provider_data.template}
        <li class="ty-social-buttons__list-item">{include file="addons/social_buttons/providers/`$provider_data.template`"}</li>
        {/if}
    {/foreach}
    </ul>
{/if}
