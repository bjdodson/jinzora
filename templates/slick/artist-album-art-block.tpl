{php}
	global $show_album_art, $sort_by_year, $album_name_truncate,$jz_path;
	
	// Let's get all the subnodes
	$node = new jzMediaNode($jz_path);
	$nodes = $node->getSubNodes("nodes");
	$display = new jzDisplay();
	
	if ($sort_by_year == "true"){
		sortElements($nodes,"year");
	} else {
		sortElements($nodes,"name");
	}
	$artarr = $node->getSubNodes("nodes",false,false,0,true);
	if (count($artarr) == 0){
		return;
	}
{/php}
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
		</td>
		<td class="jz_main_block_topr">&nbsp;</td>
	</tr>
</table>

<table width="100%" cellspacing="0" cellpadding="2"><tr><td colspan="4" class="jz_block_td">
{php}	
	// Now let's show the album art
	if ($show_album_art <> "false" && sizeof($artarr) > 0){
		echo '<table width="100%" cellpadding="5" cellspacing="0">';
		$c=0;
		
		// Now let's figure out how many colums to have
		$alb_truncate = $album_name_truncate;
		if (count($nodes) < 8){
			$colWidth = "50";
			$imageSize = "150";
			$num=2;
		} elseif (count($nodes) < 15){
			$colWidth = "33";
			$imageSize = "100";
			$alb_truncate = 10;
			$num=3;
		} else {
			$colWidth = "25";
			$imageSize = "75";
			$alb_truncate = 6;
			$num=4;
		}
		
		foreach ($nodes as $child) {
			$year = $child->getYear();
			$dispYear = "";
			if ($year <> "-" and $year <> ""){
				$dispYear = " (". $year. ")";
			}
			// Now let's see if we should start a row
			if ($c==0){ echo '</tr><tr>'; }
				// Now let's display the data
				echo '<td width="'. $colWidth. '%" align="center"><center>';
				if (($art = $child->getMainArt($imageSize. "x". $imageSize)) == false) {								
					// TODO: Create the default image here IF they want it
					$art = "style/images/default.jpg";
				}
				echo '<nobr>';
				$display->link($child, $display->returnShortName($child->getName(),$alb_truncate) . $dispYear, $child->getName() . $dispYear, "jz_artist_album");
				echo '</nobr>';
				echo '<br>';
				$display->link($child,$display->returnImage($art,$child->getName(),$imageSize,false,"fit"), $child->getName() . $dispYear);
				echo '<br><br></center></td>';
			// Now let's increment so we'll know where to make the table
			$c++;
			if ($c==$num){$c=0;}
		}
		echo '</table>';		
	}
{/php}
</td></tr></table>