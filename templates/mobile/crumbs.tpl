<table class="jz_track_table" width="100%" cellpadding="3">
	<tr class="{$jz_row1}">
		<td>
			{$img_up_arrow} <a href="{$home_link}">{$word_home}</a>
			{section name=item loop=$bcArray}
				{$img_up_arrow} <a href="{$bLinkUrl[item]}">{$bcArray[item]}</a>
			{/section}	
		</td>
	</tr>
</table>