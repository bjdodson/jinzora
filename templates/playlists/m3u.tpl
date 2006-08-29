{*
	You may use the following when building the playlist
	{$tracks[track].genre}
	{$tracks[track].artist}
	{$tracks[track].album}
	{$tracks[track].track}
	{$tracks[track].length} 
*}
#EXTM3U
{section name=track loop=$tracks}
#EXTINF:{$tracks[track].length},{$tracks[track].artist} - {$tracks[track].track}
{$tracks[track].link}
{/section}