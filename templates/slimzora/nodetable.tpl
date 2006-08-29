<table class="jz_track_table" width="100%" cellpadding="1" cellspacing="0" border="0">
	{section name=track loop=$tracks}
		<tr class="{$jz_row1}">				
			<td nowrap valign="top" colspan="2">
				<a href="{$tracks[track].playlink}">{$img_play}</a><a href="{$tracks[track].randomlink}">{$img_random_play}</a>
				<a href="{$tracks[track].link}">{$tracks[track].name}</a> 
				{$tracks[track].year}
			</td>
		</tr>
		<tr class="{$jz_row1}">				
			<td nowrap valign="top">
				{if $show_images == "true" and $art <> false}
					Image
				{/if}
				{if $show_descriptions == "true"}
					<span class="jz_artistDesc">{$tracks[track].description}</span>
				{/if}
			</td>
		</tr>
	{/section}
</table>