{*
	You may use the following when building the playlist
	{$tracks[track].genre}
	{$tracks[track].artist}
	{$tracks[track].album}
	{$tracks[track].track}
	{$tracks[track].length}
*}
<?xml version="1.0" encoding="UTF-8"?>
<playlist version="1" xmlns="http://xspf.org/ns/0/">
	<title>Jinzora Playlist</title>
	<trackList>
	{section name=track loop=$tracks}			
		<track>
			<location>{$tracks[track].link}</location>
			<title>{$tracks[track].artist}{$tracks[track].track}</title>
		</track>
	{/section}	
	</trackList>
</playlist>
