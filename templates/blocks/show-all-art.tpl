<table width="100%" cellpadding="3" class="jz_col_table_main" border="0" cellspacing="0">
	<tr>
	{section name=item loop=$items}
		<td align="center">
			{$items[item].image}
		</td>
		{$items[item].row}
	{/section}
	</tr>
</table>