<form name="albumForm" method="POST" action="{$form_action}">
{$hidden_field}
{section name=item loop=$items}
	<table width="100%" cellspacing="0" cellpadding="4">
		<tr class="{$items[item].row}">
			<td width="1%" valign="middle">
				<input type="checkbox" name="jz_list[]" value="{$items[item].path}">
			</td>
			<td width="1%" valign="middle">
				{$items[item].link}
			</td>
			<td width="96%" valign="middle">
				{$items[item].name}
			</td>	
			<td width="1%" valign="middle" nowrap align="right">
				{$items[item].items}
			</td>
			<td width="1%" valign="middle" nowrap align="right">
				{$items[item].play_button}&nbsp;{$items[item].random_button}&nbsp;
			</td>
		</tr>		
		{if $items[item].subitems}
		<tr class="{$items[item].row}">
			<td width="1%" valign="middle"></td>
			<td width="99%" valign="middle" colspan="4">
				{$items[item].art}{$items[item].desc}{$items[item].read_more}
			</td>	
		</tr>
		{/if}
	</table>
	<table width="100%" cellspacing="0" cellpadding="0"><tr bgcolor="#D2D2D2"><td width="100%"></td></tr></table>	
{/section}