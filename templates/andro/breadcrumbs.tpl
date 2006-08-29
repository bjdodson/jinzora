	<table width="100%" cellspacing="0" cellpadding="3">
		<tr class="and_head1">
			<td width="1%" valign="middle" nowrap>
				{$bread_crumbs}
			</td>
			<td valign="middle" nowrap>
				{$play_button}
				{$random_button}
				{if $help_access == "true" or $help_access == "all" or ($help_access == "admin" and $is_admin == true)}
					<a onclick="openPopup(this, 450, 450); return false;" href="{$info_button}"><img src="{$image_dir}more.gif" border="0"></a>
				{/if}
			</td>
			{if $allow_resample == "true"}
			<td align="right">
				{$resample_box}
			</td>
			{/if}
		</tr>
	</table>
