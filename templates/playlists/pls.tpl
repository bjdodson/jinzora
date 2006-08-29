{*
	You may use the following when building the playlist
	{$tracks[track].genre}
	{$tracks[track].artist}
	{$tracks[track].album}
	{$tracks[track].track}
	{$tracks[track].length}
*}
[playlist]
{section name=track loop=$tracks}
	File{$tracks[track].i}={$tracks[track].link}
	Title{$tracks[track].i}={$tracks[track].artist} - {$tracks[track].track}
	Length{$tracks[track].i}={$tracks[track].length}
{/section}
NumberOfEntries={$total}
Version=2