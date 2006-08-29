<table width="100%" cellspacing="0" cellpadding="2">
	<tr class="and_head1">
		<td colspan="4" class="">
			<table width="100%" cellpadding="{$padding}" cellspacing="0">
				<tr><nobr>
				{foreach key=letter item=url from=$alpha_list}
					<td align="center"><a href="{$url}">{$letter}</a></td>
				{/foreach}
				</nobr></tr>
			</table>
		</td>
	</tr>
</table>
