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
		$display = new jzDisplay();
		$simArray = $jzSERVICES->getSimilar($node);
		$simArray = seperateSimilar($simArray);
		
		if (!$onlyMatches && sizeof($simArray['matches']) > 0){
			echo "<strong>". word("Available"). "</strong><br>";
		}
		$i=0;
		echo "<nobr>";
		for ($e=0;$e<count($simArray['matches']);$e++){
			$arr['jz_path'] = $simArray['matches'][$e]->getPath("String");
			$title = returnItemShortName($simArray['matches'][$e]->getName(),$album_name_truncate);
			if ($jzUSER->getSetting('stream')){
				$display->playLink($simArray['matches'][$e], $img_tiny_play, $title);
			} else {
				echo $img_tiny_play_dis;
			}
			echo '<a title="'. $simArray['matches'][$e]->getName(). '" href="'. urlize($arr). '">'. $title. '</a><br>';
			$i++;
			if ($_SESSION['sim_limit']){
				if ($i>$_SESSION['sim_limit']){ break; }
			}
		}
		if (count($simArray['matches']) > 0){
			echo "<br>";
		}
		$i=0;
		if (!$_SESSION['sim_onlyMatches'] && sizeof($simArray['nonmatches']) > 0){
			echo "<strong>". word("Not Available"). "</strong><br>";
			for ($e=0;$e<count($simArray['nonmatches']);$e++){
				$title = returnItemShortName($simArray['nonmatches'][$e],$album_name_truncate);
				echo '<a title="'. $simArray['nonmatches'][$e]. '" href="' . $jzSERVICES->link("artist", $simArray['nonmatches'][$e]) . '" target="_blank">'. $title. '</a><br>';
				$i++;
				if ($limit){
					if ($i>$limit){ break; }
				}
			}
		}
		echo "<nobr>";
	{/php}
</div>