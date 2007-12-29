<table class="jz_track_table" width="100%" cellpadding="1">
{section name=track loop=$tracks}
	<tr class="{$jz_row1}">				
		<td width="99%" valign="top" nowrap>
			<a href="{$tracks[track].playlink}">{$img_play}</a><a href="{$tracks[track].downloadlink}">{$img_download}</a>
				<a href="{$tracks[track].playlink}">{$tracks[track].name}</a>
		</td>
		<td width="1%" valign="top" nowrap>
			{$tracks[track].length}
		</td>
	</tr>
{/section}
</table>