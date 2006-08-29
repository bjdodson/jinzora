{php}
	$blocks = new jzBlocks();
{/php}
{if $jukebox_display == true}
	{if $jukebox_display == "small"}
		<div id="smallJukebox">hey
		{php}
			$blocks->smallJukebox(false,"top");
		{/php}
		</div>
		<br>bye
	{/if}
	{if $jukebox_display == "full"}
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