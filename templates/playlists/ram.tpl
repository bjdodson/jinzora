{*
	You may use the following when building the playlist
	{$tracks[track].genre}
	{$tracks[track].artist}
	{$tracks[track].album}
	{$tracks[track].track}
	{$tracks[track].length}
*}
{section name=track loop=$tracks}
{$tracks[track].link}&clipinfo="title={$tracks[track].track}|artist name={$tracks[track].artist}|album name={$tracks[track].album}|genre={$tracks[track].genre}|year={$tracks[track].year}"&mode=normal
{/section}