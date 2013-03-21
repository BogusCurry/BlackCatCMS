<div id="tool_blackcat">
    <p class="submit_settings fc_gradient1">
        <strong>{translate('BlackCat CMS Admin-Tool Settings')}</strong>
        <input type="submit" value="{translate('Save')}" name="submit">
    </p>
    <div class="fc_gradient1 fc_all_forms">
        <div class="clear_sp"></div>
        <form action="{$TOOL_URL}" method="post">
            <p>
            {foreach $settings as set}
                <label class="fc_label_300" for="{$set.name}">{translate($set.label)}</label>
                <input class="fc_input_300" type="{$set.type}" name="{$set.name}" id="{$set.name}" value="{$set.value}"{if $set.disabled} disabled="disabled"{/if} /><br />
            {/foreach}
            </p>
            <p class="submit_settings fc_gradient1">
				<input type="submit" value="{translate('Save')}" name="submit">
			</p>
        </form>
    </div>
</div>