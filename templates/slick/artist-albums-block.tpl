<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td height="23" class="jz_main_block_topl">&nbsp;</td>
		<td width="50%" class="jz_main_block_topm" nowrap>
			<span class="headertextshadow">
				<strong>
					{$title}
					<font color="{$jz_bg_color}" class="headertext">
						{$title}
					</font>
				</strong>
			</span>
		</td>
		<td width="50%" align="right" class="jz_main_block_topm" nowrap>
			{$playButtons}
		</td>
		<td class="jz_main_block_topr">&nbsp;</td>
	</tr>
</table>

<table width="100%" cellspacing="0" cellpadding="2"><tr><td colspan="4" class="jz_block_td">
<table width="100%" cellpadding="2">
	<tr>
		<td width="100%" nowrap>
			<form name="albumForm" action="{$formaction}" {$formhandler} method="POST">
				<input type="hidden" name="{$action}" value="{$action_value}">
				<input type="hidden" name="{$jz_path}" value="{$jz_path_value}">
				<input type="hidden" name="{$jz_list_type}" value="{$jz_list_type_value}">
				{php}
					global $jzUSER, $album_name_truncate, $img_play, $img_random_play, $img_play_dis, $img_random_play_dis, $sort_by_year, $web_root, $root_dir, $jz_path, $show_album_clip_play, $img_clip;
					
					$node = new jzMediaNode($jz_path);
					$nodes = $node->getSubNodes("nodes");	
					$display = new jzDisplay();
					$blocks = new jzBlocks();
					$mysort = $jzUSER->getSetting('sort');
					// First let's sort this up
					if (($sort_by_year == "true" or $mysort == "year") and $mysort <> "alpha"){
						sortElements($nodes,"year");
					} else {
						sortElements($nodes,"name");
					}	
					
					foreach ($nodes as $child) {
						$display->playButton($child);
						$display->randomPlayButton($child);
						if ($show_album_clip_play == "true"){
							$display->playLink($child,$img_clip,false,false,false,false,false,true);
						}
						$display->purchaseButton($child);
						$display->downloadButton($child,false,false,true);
						$display->podcastLink($child, false);
						$display->rateButton($child);
						$display->addToFavButton($child);
						
						echo '<input type="checkbox" name="jz_list[]" value="' . jz_encode($child->getPath("String")) . '">&nbsp;';
						
						// FYI, you can getYear for any media element. It's guessed from the tracks.
						$year = $child->getYear();
						$dispYear = "";
						if (!isNothing($year)){
							$dispYear = " (". $year. ")";
						}
						$display->link($child, $display->returnShortName($child->getName(),$album_name_truncate) . $dispYear, word("View album"). ": ". $child->getName() . $dispYear);
						// Now should we show new data?
						if ($days = $child->newSince($days_for_new)){
							echo ' <img src="'. $raw_img_new. '" border="0" '. $display->returnToolTip($days. " ". word("days ago"), word("New Since")). '>';
						}
						
						echo " &nbsp; ";
						$display->rating($child);
						echo "<br>";
					}
					
					$blocks->blockSpacer();
					$blocks->playlistBar();
				{/php}
			</form>		
		</td>
	</tr>
</table>
</td></tr></table>
<table width="100%" cellpadding="2" cellspacing="0" border="0"><tr><td width="100%" height="5"></td></tr></table>