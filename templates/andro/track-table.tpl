<form name="albumForm" action="{$this_page}" method="POST">
<input type="hidden" name="{$form_action}" value="{$form_action_val}">
<input type="hidden" name="{$form_path}" value="{$form_path_val}">
<input type="hidden" name="{$form_list_type}" value="{$form_list_type_val}">
{section name=item loop=$items}
	<table width="100%" cellspacing="0" cellpadding="4">
		<tr class="{$items[item].row_color}">
			<td width="1" valign="top">
				<input type="checkbox" name="jz_list[]" value="{$items[item].path}">
			</td>
			<td width="1" valign="top">
				<nobr>
					{$items[item].play_button}&nbsp;{$items[item].download_button}
				</nobr>
			</td>
			<td width="1" valign="top" align="right">
				{$items[item].track_num}
			</td>
			<td width="100%" valign="top" nowrap>
				{$items[item].track_name}
				{if $items[item].show_artist_album == "true" and $items[item].artist <> "" and $items[item].album <> ""}
					<br />
					<div style="font-size:9px;">
						{$items[item].artist} 
						{if $items[item].artist <> ""}
							- {$items[item].album}
						{/if}
					</div>
				{/if}
			</td>	
			<td width="1" valign="top" nowrap align="right">
				<table width="100%" cellspacing="5" cellpadding="0">
					<tr>
						<td align="right" valign="middle" nowrap>
							<div style="font-size: 8px;">
								{$items[item].length}
								&#183;
								{$items[item].bitrate} Kbit/s
								&#183;
								{$items[item].size} MB
								&#183;
								{$items[item].type}
							</div>	
						</td>
					</tr>
				</table>						
			</td>
		</tr>
	</table>
	<table width="100%" cellspacing="0" cellpadding="0"><tr bgcolor="#D2D2D2"><td width="100%"></td></tr></table>
{/section}