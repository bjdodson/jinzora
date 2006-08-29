<div id="slickLeftBlock">
	<div id="slickLeftBlockPad">
		<strong>{$word_options}</strong>
		<br />
		{if $show_group_options == true}
			<form name="groupform" action="{$this_page}" method="POST">
				<select class="jz_select" name="action" style="width:125px" onChange="openPopup(this.form.action.options, 400, 400, false, MediaManagement)">
					<option value="">Group Features</option>
					<option value="{$rate_popup_link}">{$word_rate_item}</option>
					<option value="{$discuss_popup_link}">{$word_discuss_item}</option>
					<option value="{$request_popup_link}">{$word_request_manager}</option>
				</select>
			</form>
			<br>
		{/if}
		{php}
			$display = new jzDisplay();
		{/php}
		{if $allow_lang_choice == "true"}	
			{php}
			$display->languageDropdown();
			{/php}
		{/if}		
		{if $allow_interface_choice == "true"}	
			{php}
			$display->interfaceDropdown();
			{/php}
		{/if}		
		{if $allow_style_choice == "true"}	
			{php} 
			$display->styleDropdown();
			{/php}
		{/if}		
	</div>	
</div>
<div id="slickLeftBlockSpace"></div>