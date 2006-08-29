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
<div id="slickMainBlockBody">
	<table width="95%">
		<tr>
		{php}
			$node = new jzMediaNode($_GET['jz_path']);
			$parent = $node->getNaturalParent(); 
			$nodes = $parent->getSubNodes("nodes",false,true,$num_other_albums * 2,true); // randomized, only with art.
			$display = new jzDisplay();
			$jzUSER = new jzUser();
			
			global $num_other_albums, $album_name_truncate;
			
			// Now let's get some other random images											
			$ctr=0;
			foreach ($nodes as $child) {
				if ($child->getName() <> $node->getName()){	
					$year = $child->getYear();
					if ($year <> "-"){ $dispYear = " (". $year. ")"; } else { $dispYear = ""; }
					echo '<td align="center"><center>';
					$display->link($child,returnItemShortName($child->getName(),$album_name_truncate));
					echo "<br>";
					$display->link($child,$display->returnImage($child->getMainArt("100x100"),$child->getName(),100,false,"fit"), $child->getName() . $dispYear);
					echo "<br>";
					if ($jzUSER->getSetting('stream')){
						$display->playLink($child,word("Play"), word("Play"), "");
						echo " - ";
						$display->playLink($child,word("Play Random"), word("Play Random"), "",false,true);
					}														
					echo '</center></td>';
					$ctr++;
				}
				if ($ctr == $num_other_albums){break;}
			}
		{/php}
		</tr>
	</table>
</div>