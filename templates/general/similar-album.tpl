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
	{php}
		global $jzSERVICES, $jzUSER, $album_name_truncate, $img_tiny_play;
		
		$node = new jzMediaNode($_GET['jz_path']);
		$element = $node->getAncestor("artist");
		$display = new jzDisplay();
		$simArray = $jzSERVICES->getSimilar($element);
		$simArray = seperateSimilar($simArray);
		
		$i=0;
		shuffle($simArray['matches']);
		echo "<nobr>";
		for ($e=0;$e<count($simArray['matches']);$e++){
			if (isset($simArray['matches'][$e])){		
				// Let's setup our objects to get the data from
				$artist = $simArray['matches'][$e];
				$item = $artist->getSubNodes("nodes");
				if (count($item) == 0){continue;}
				foreach ($item as $album) {
					$arr['jz_path'] = $album->getPath("String");
					break;						
				}
				if (isset($arr['jz_path'])){
					// Now let's get 1 random album from this artist
					// Now let's setup and display the data
					$title = returnItemShortName($album->getName(),$album_name_truncate);
					if ($jzUSER->getSetting('stream')) {
						$display->playLink($simArray['matches'][$e], $img_tiny_play, $title);
					} else {
						echo $img_tiny_play_dis;
					}
					echo '<a title="'. $artist->getName(). " - ". $simArray['matches'][$e]->getName(). '" href="'. urlize($arr). '">'. $title. '</a><br>';
					$i++;
					if ($_SESSION['sim_limit']){
						if ($i>$_SESSION['sim_limit']){ break; }
					}
				}
				unset($arr);
			}
		}
		echo "<nobr>";
	{/php}
</div>