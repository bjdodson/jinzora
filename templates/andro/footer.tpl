{php}
	global $allow_interface_change, $allow_lang_choice, $jzUSER, $cms_mode, $show_page_load_time, $allow_style_choice; 
	
	$display = new jzDisplay();
{/php}
<table width="100%" cellspacing="0" cellpadding="0"><tr height="2" style="background-image: url('{$image_dir}row-spacer.gif');"><td width="100%"></td></tr></table>
<table width="100%" cellspacing="0" cellpadding="1">
	<tr class="and_head1">
		<td width="50%" nowrap>
			{php}
				if ($allow_interface_change == "true"){
					$display->interfaceDropdown();
				}
				if ($allow_style_choice == "true"){
					$display->styleDropdown();
				}
				if ($allow_lang_choice == "true") {
					$display->languageDropdown();
				}
			{/php}
		</td>
		<td width="50%" align="right">
			{php}
				if ($jzUSER->getSetting('admin')){
					$display->systemToolsDropdown($node);
				}
			{/php}
		</td>
	</tr>
</table>
<table width="100%" cellspacing="0" cellpadding="0"><tr height="2" style="background-image: url('{$image_dir}row-spacer.gif');"><td width="100%"></td></tr></table>
<table width="100%" cellspacing="0" cellpadding="3">
	<tr class="and_head1">
		<td width="25%" align="left">
			{$word_logged_in}: {$username}
		</td>
		<td width="50%" align="center">
			<br>
			powered by <a href="{$jinzora_url}">Jinzora</a> version {$version}
			<br><br>
		</td>
		<td width="25%" align="right">
			{$page_load}
		</td>
	</tr>
</table>
<table width="100%" cellspacing="0" cellpadding="0"><tr height="2" style="background-image: url('{$image_dir}row-spacer.gif');"><td width="100%"></td></tr></table>
</td></tr></table>