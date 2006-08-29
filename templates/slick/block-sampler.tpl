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
		</td>
		<td width="50%" align="right" class="jz_main_block_topm" nowrap>
			{$viewAll}
		</td>
		<td class="jz_main_block_topr">&nbsp;</td>
	</tr>
</table>
<table width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td colspan="4" class="jz_block_td">
			{php}
				$blocks = new jzBlocks();
				$node = new jzMediaNode($_GET['jz_path']);
				$blocks->displaySampler($node);
			{/php}
		</td>
	</tr>
</table>