<table width="100%" cellspacing="0" cellpadding="0"><tr height="2" style="background-image: url('{$image_dir}row-spacer.gif');"><td width="100%"></td></tr></table>
	<table width="100%" cellspacing="0" cellpadding="3">
		<tr class="and_head1">
			<td width="100%">
				<form action="{$playlist_form_link}" method="POST" name="playlistForm">
				<a style="cursor:hand" onClick="CheckBoxes('albumForm',true); return false;" href="javascript:;"><img src="{$image_dir}check.gif" border="0"></a><a style="cursor:hand" onClick="CheckBoxes('albumForm',false); return false;" href="javascript:;"><img src="{$image_dir}check-none.gif" border="0"></a>	
				{$addListButton}
				{$hidden_1}
				{$hidden_2}
				{$playlist_button}
				{$playlist_select}
				{$playlist_play_button}
				{$playlist_random_button}
				</form>
		</td>
	</tr>
</table>
</form>