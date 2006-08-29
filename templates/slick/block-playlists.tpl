<div id="slickLeftBlock">
	<div id="slickLeftBlockPad">
		<form action="{$playlist_form_link}" method="POST" name="playlistForm">
			<input type="hidden" name="path" value="{$cur_path}" />
			<strong>{$word_playlists}</strong>
			<br />
			<select class="jz_select" name="jz_playlist" style="width:125px;margin-bottom:3px">
				<option value= "session"> - Session Playlist - </option>
				{section name=plist loop=$playlists}
					<option {$playlists[plist].selected} value="{$playlists[plist].value}">{$playlists[plist].name}</option>
				{/section}
			</select>
			<br />
			<nobr>
			<input type="hidden" name="playlistname" value="">&nbsp;
			{$playlist_play_button}
			{$playlist_play_random_button}
			{$playlist_download_button}
			{$playlist_create_button}
			{$playlist_manager_button}
			{$playlist_hidden_action}
			{$playlist_hidden_path}
			</nobr>
		</form>
	</div>
</div>
<div id="slickLeftBlockSpace"></div>