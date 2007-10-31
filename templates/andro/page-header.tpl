<table width="100%" cellspacing="0" cellpadding="2">
	<tr class="and_head2">
		<td width="50%" nowrap>
			<a href="{$this_page}">{$img_home}</a>
		</td>
		<td width="50%" align="right" nowrap>
			{$word_search}
			<form action="{$this_page}" method="{$method}">
				{$formFields}
				<select class="jz_select" name="search_type" style="width:85px">
					<option value="ALL">{$word_all_media}</option>
					{$searchFields}
					<option value="tracks">{$word_tracks}</option>
					<option value="lyrics">{$word_lyrics}</option>
				</select>
				<input type="text" name="search_query" class="jz_input" style="width:125px; font-size:10px ">
				<input type="hidden" name="doSearch" value="true">
				<input type="submit" class="jz_submit" value="Go">
			</form>
		</td>
	</tr>
	<tr class="and_head2">
		<td>
		{if $cms_mode == "false"}
			{$login_link} - {$prefs_link}
		{/if}
		</td>
		<td valign="middle" align="right" nowrap>
			{$randomizer}
		</td>
	</tr>
</table>
<table width="100%" cellspacing="0" cellpadding="0"><tr class="and_head1"><td width="100%"></td></tr></table>
<table width="100%" cellspacing="0" cellpadding="1"><tr class="and_head2"><td width="100%"></td></tr></table>
<table width="100%" cellspacing="0" cellpadding="0"><tr class="and_head1"><td width="100%"></td></tr></table>
<table width="100%" cellspacing="0" cellpadding="5">
	<tr class="and_head2">
		<td width="100%"></td>
	</tr>
</table>