{php}
	// There are a few PHP things we need to setup for this page
	// They are all setup here
	global $jzSERVICES, $jz_path;
	
	$blocks = new jzBlocks();
	$node = new jzMediaNode($jz_path);
	$fe = &new jzFrontend();
	$tracks = $node->getSubNodes("tracks");
{/php}
<table width="100%" cellpadding="1"><tr><td>

<table width="100%" cellpadding="0" cellspacing="0" height="100%">
	<tr>
		{if $show_profile_col}
			<td valign="top" width="10%" valign="top" height="100%">
				{if $show_album_block}
					{php}
						$blocks->artistAlbumsBlock($node);
					{/php}				
				{/if}
				{if $show_artist_profile}
					{php}
						$blocks->artistProfileBlock($node);
					{/php}				
				{/if}
				{php}
					$blocks->slickFillerBlock();
				{/php}
			</td>			
			<td valign="top" width="1" valign="top">&nbsp;</td>
			<td width="80%" valign="top">
		{else}
			<td width="100%" valign="top">
		{/if}
		
		{if $show_tracks}
			{php}
				$blocks->blockHeader($node->getName(). " Tracks",$playButtons);
				$blocks->blockBodyOpen();		
				
				$blocks->trackTable($tracks, "album");
				$blocks->blockBodyClose();
				$blocks->blockSpacer();
			{/php}
		{/if}

		{if $show_sampler}
			{php}
				// Now did they want to view all tracks or just the sampler?
				$viewall = false;
				if (isset($_GET['action'])){
					if ($_GET['action'] == "viewalltracks"){
						$viewall = true;
					}
				}					
				// Now let's display
				if (!$viewall){
					$blocks->displaySlickSampler($node);
				} else {
					$blocks->displaySlickAllTracks($node);
				}										
				// Now let's space
				$blocks->blockSpacer();
			{/php}
		{/if}
		
		{php}
			$blocks->artistAlbumArtBlock($node);
			$blocks->blockSpacer();
			$blocks->slickFillerBlock();
		{/php}
		</td>
		{if $show_sim_col}
			{php}
				global $show_radio, $show_similar;
				
				// let's make sure there are similar ones to show
				$simArray = $jzSERVICES->getSimilar($node);
				$simArray = seperateSimilar($simArray);
				if (sizeof($simArray['matches']) <> 0) {
					echo '<td valign="top" width="1" valign="top">&nbsp;</td><td width="10%" valign="top" height="100%">';
					if ($show_radio == "true"){
						$blocks->slickRadioBlock($node);
						$blocks->blockSpacer();
					}							
					if ($show_similar == "true"){
						$blocks->slickSimilarArtistBlock($node,false,8);
					}							
					// Now let's show the filler block
					$blocks->blockSpacer();
					$blocks->slickFillerBlock();							
					echo '</td>';
				}
			{/php}
		{/if}
	</tr>
</table>
{php}
	$blocks->blockSpacer();
	
	$fe->footer();
{/php}		
</td></tr></table>		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
