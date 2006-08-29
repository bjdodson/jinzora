<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td height="23" class="jz_main_block_topl">&nbsp;</td>
		<td width="50%" class="jz_main_block_topm" nowrap>
			<span class="headertextshadow">
				<strong>
					{$title}
					<font color="{$jz_bg_color}" class="headertext">
						{$title}
					</font>
				</strong>
			</span>
			{$title2}
		</td>
		<td width="50%" align="right" class="jz_main_block_topm" nowrap>
		</td>
		<td class="jz_main_block_topr">&nbsp;</td>
	</tr>
</table>

<table width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td colspan="4" class="jz_block_td">
			{$art}
			{if $artist <> ""}
				{$artist_play_button}
				{$artist}
				<br />
			{/if}
			{$description}{$read_more}
		</td>
	</tr>
</table>