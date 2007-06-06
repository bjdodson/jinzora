{*
	You may use the following when building the playlist
	{$tracks[track].genre}
	{$tracks[track].artist}
	{$tracks[track].album}
	{$tracks[track].track}
	{$tracks[track].length}
*}
<ASX version="3">
	<TITLE>Jinzora Playlist</Title>
	<LOGO HREF="{$this_site}{$root_dir}/style/favicon.ico" STYLE="ICON" />
	<BANNER HREF="{$this_site}{$root_dir}/style/asx-banner.gif" />
	{section name=track loop=$tracks}
		{if $tracks[track].track <> ""}
			<ENTRY>
				<TITLE>{$tracks[track].artist}{$tracks[track].track}</TITLE>
				<REF HREF="{$tracks[track].link}"/>
				{if $asx_show_trackdetail == "true"} 			
				<PARAM name="HTMLView" value="{$tracks[track].url}/popup.php?action=popup&ptype=wmptrack&jz_path={$tracks[track].path}&totalTracks={$totalTracks}"/>
				{/if}
			</ENTRY>
		{/if}
	{/section}
</ASX>		
