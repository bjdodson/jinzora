{php}
	$blocks = new jzBlocks();
{/php}
{if $jukebox_display == true}
	{if $jukebox_display == "small"}
		<div id="smallJukebox">
		{php}
			$blocks->smallJukebox(false,"top");
		{/php}
		</div>
		<br>
	{/if}
	{if $jukebox_display == "small"}
		{php}
			$blocks->blockHeader("Jukebox"); // - ". $link);
			$blocks->blockBodyOpen();
		{/php}
		<div id="jukebox">
		{php}
			$blocks->jukeboxBlock();
		{/php}
		</div>
	{/if}
{/if}
