<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *        
	* JINZORA | Web-based Media Streamer   
	*
	* Jinzora is a Web-based media streamer, primarily desgined to stream MP3s 
	* (but can be used for any media file that can stream from HTTP). 
	* Jinzora can be integrated into a CMS site, run as a standalone application, 
	* or integrated into any PHP website.  It is released under the GNU GPL. 
	* 
	* Jinzora Author:
	* Ross Carlson: ross@jasbone.com 
	* http://www.jinzora.org
	* Documentation: http://www.jinzora.org/docs	
	* Support: http://www.jinzora.org/forum
	* Downloads: http://www.jinzora.org/downloads
	* License: GNU GPL <http://www.gnu.org/copyleft/gpl.html>
	* 
	* Contributors:
	* Please see http://www.jinzora.org/modules.php?op=modload&name=jz_whois&file=index
	*
	* Code Purpose: This page contains all the Genre/Artist display related functions
	* Created: 9.24.03 by Ross Carlson
	*
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	
	// This function displays all the Genres or Artists 
	function drawPage(&$node){
		global $media_dir, $jinzora_skin, $hierarchy, $album_name_truncate, $jzUSER;					

		$display = &new jzDisplay();
		$blocks = &new jzBlocks();
		$fe = &new jzFrontend();
		
		$nodes = $node->getSubNodes("nodes");
		$tracks = $node->getSubNodes("tracks");
		
		echo '<br>';
		$blocks->blockBodyOpen();

		?>
			<table width="100%">
			<tr valign="top"><td width="60%">
		<?php
			if (($art = $node->getMainArt()) !== false) {
				$display->image($art,$node->getName(),250,false,"limit",false,false,"left");
				$blocks->description($node);
			} else {
			    if (false === $blocks->description($node)) {
			         echo '&nbsp;';
			    }
			}
            
		
		?>
			</td><td align="right">
		<?php

			echo "<b>" . $node->getName() . "</b> ";
		        
			$display->playButton($node);
			//echo "&nbsp";
			$display->randomPlayButton($node);
			echo "<br><br>";
			foreach ($nodes as $album) {
				$year = $album->getYear();
				if (!isNothing($year)) {
					$display->link($album, $album->getName() . " (" . $album->getYear() . ")");
				} else {
					$display->link($album, $album->getName());
				}
				echo "&nbsp";
				$display->playButton($album);
				//echo "&nbsp";
				$display->randomPlayButton($album);
				if ($jzUSER->getSetting('download') === true) {
				  $display->downloadButton($album);
				}
				echo "<br>";
			}
		
		?>
			</td></tr>
			<tr><td colspan="2" align="left">
			<br><br>
			<table cellpadding="2"><tr><td>
		<?php
			$i = 0;
			foreach ($nodes as $album) {
				if (($art = $album->getMainArt('150x150')) !== false) {
					if ($i > 0 && $i % 5 == 0) {
						echo "</td></tr><tr><td>";
					}
					echo "<td width=\"150\">";
					$display->link($album,$display->returnImage($art,$album->getName(),150,false,"fixed"));
					echo "</td>";
					$i++;
				}
			}
			echo "</td></tr>";
		?>
					
			</table>
			</td></tr></table>
		<?php
		if (sizeof($tracks) > 0) {
		  echo '<br>';
		  $blocks->trackTable($tracks, false);
		}
		$blocks->blockBodyClose();
		echo '<br>';
	}
?>
