{php}
	$blocks = new jzBlocks();
	$node = new jzMediaNode($_GET['jz_path']);
	$fe = &new jzFrontend();
	
	global $jzSERVICES;
{/php}
<table width="100%" cellpadding="1" height="100%">
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="3" height="100%"></td>
				</tr>
				<tr>
					{if $show_profile_col}
					<td width="20%" height="100%" valign="top">
						{php}
							if ($node->getPType() == "album"){
								$blocks->albumAlbumBlock($node);
							}
							$blocks->artistProfileBlock($node->getAncestor("artist"));
							$blocks->slickFillerBlock();
						{/php}
					</td>
					<td valign="top" width="1" valign="top">&nbsp;</td>
					{/if}
					<td width="80%" valign="top" height="100%">
						{php}
							$blocks->albumTracksBlock($node);
						{/php}
						{if $show_other_albums}
							{php}
								$blocks->blockSpacer();
								$blocks->albumOtherAlbumBlock($node);
								$blocks->blockSpacer();
							{/php}
						{/if}
						{php}
							$blocks->slickFillerBlock();
						{/php}
					</td>
					{if $show_right_col}
						{if $show_similar}
							<td valign="top" width="1" valign="top" height="100%">&nbsp;</td>
							<td height="100%" width="10%" valign="top">
								{if $show_radio == "true"}						
									{php}
										$blocks->slickRadioBlock($node);
										$blocks->blockSpacer();
									{/php}
								{/if}
								{if $show_similar == "true"}
									{php}
										$blocks->slickSimilarArtistBlock($node,false,8); 
										$blocks->blockSpacer();
										$blocks->slickSimilarAlbumBlock($node,8); 
									{/php}
								{/if}
								{php}
									// Now let's show the filler block
									$blocks->blockSpacer();
									$blocks->slickFillerBlock();		
								{/php}
							</td>
						{/if}
					{/if}
				</tr>
			</table>
			{php}
				$blocks->blockSpacer();
				$fe->footer();
			{/php}
		</td>
	</tr>
</table>
