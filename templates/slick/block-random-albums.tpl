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
			{$showLink}
		</td>
		<td class="jz_main_block_topr">&nbsp;</td>
	</tr>
</table>
<table width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td colspan="4" class="jz_block_td">
			<table width="100%" cellpadding="5" cellspacing="0" border="0">
				<tr>
					{php}
						global  $show_album_art, $random_albums, $random_per_slot, $random_rate, $jz_path;
						
						// Now let's get a random amount of albums with album art
						$node = new jzMediaNode($jz_path);
						$artArray = $node->getSubNodes("nodes",distanceTo("album",$node),true,$random_albums*$random_per_slot,true);
			
						// Now let's figure out how wide to make the colums
						if (($random_albums * $random_per_slot) > count($artArray)){
							// Now we've got to figure out how many we've got
							$numArt = count($artArray);
							if ($numArt > $random_albums){
								$random_per_slot = round(count($artArray) / $random_albums - .49,0);
							} else {
								$random_albums = count($artArray);
								$random_per_slot = 1;
							}
						}
						$colWidth = 100 / $random_albums;
						$c=1;
						while ($c < ($random_albums+1)){
							echo '<td align="center" valign="middle" width="'. $colWidth. '%">';
							echo '<div id="div'. $c. '"></div>';
							echo '</td>';
							$c++;
						}
					{/php}
				</tr>
			</table>
		</td>
	</tr>
</table>
<table width="100%" cellpadding="2" cellspacing="0" border="0"><tr><td width="100%" height="5"></td></tr></table>
