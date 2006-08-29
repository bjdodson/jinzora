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
	* Code Purpose: This page contains all the album related related functions
	* Created: 9.24.03 by Ross Carlson
	*
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

 	// This function displays all the Genres or Artists 
	function drawPage(&$node){
		global $media_dir, $jinzora_skin, $hierarchy, $album_name_truncate, $row_colors, 
		       $img_download, $img_more, $img_email, $img_play, $img_random_play, $img_rate, $img_discuss, 
			   $num_other_albums, $root_dir, $enable_ratings, $short_date, $jzUSER, $img_play_dis, $img_random_play_dis,
			   $img_download_dis, $show_similar, $show_radio, $jzSERVICES, $show_album_art, $this_page;					
							
		// Let's setup the new display object
		$display = &new jzDisplay();
		$blocks = &new jzBlocks();
		$fe = &new jzFrontend();
		$parent = $node->getAncestor("artist");
		
		?>			
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<tr>
				<td>
					<table width="100%" cellpadding="5" cellspacing="0" border="0">
						<tr>
							<td class="jz_block_td" nowrap colspan="4">
								<strong>SEARCH RESULTS AND OPTIONS</strong>
							</td>
						</tr>
						<tr>
							<?php
								if (($art = $node->getMainArt("100x100")) !== false) {					
									echo '<td width="1%" class="jz_nj_block_body" align="center" rowspan="2">';
									
									$display->playLink($node,$display->returnImage($art,$node->getName(),100,100,"fit"));
									

									echo '</td>';
								}			
							?>
							<td width="33%" class="jz_nj_block_body" align="center">
								<?php
									echo $node->getSubNodeCount();
								?> Track(s) Found.
							</td>
							<td width="33%" class="jz_nj_block_body" align="center">
								Page 1 of 1
							</td>
							<td width="33%" class="jz_nj_block_body" align="center">
								<?php
									$stats = $node->getStats();
									echo $stats['total_length_str'];
									echo " for ";
									echo $stats['total_size_str'];
								?>
							</td>
						</tr>
						<tr>
							<td width="25%" class="jz_nj_block_body" align="center">
								<a onClick="CheckBoxes('tracklist',true);" href="javascript:void()">Select All</a>
							</td>
							<td width="25%" class="jz_nj_block_body" align="center">
								<a onClick="CheckBoxes('tracklist',false);" href="javascript:void()">Release All</a>
							</td>
							<td width="25%" class="jz_nj_block_body" align="center">
									<a onClick="document.tracklist.randomize.value = 'false'; <?php echo $display->embeddedFormHandler('tracklist'); ?> document.tracklist.submit();" href="javascript:void()"><?php echo word('Play'); ?></a>
									 | 
									<a onClick="document.tracklist.randomize.value = 'true';  <?php echo $display->embeddedFormHandler('tracklist'); ?> document.tracklist.submit();" href="javascript:void()"><?php echo word('Randomize'); ?></a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<tr>
				<td>
					<table width="100%" cellpadding="5" cellspacing="0" border="0">
						<tr>
							<td width="25%" class="jz_nj_block_body" style="border-top: 1px solid black;">&nbsp;
							
							</td>
							<td width="25%" align="center" class="jz_nj_block_body" style="border-top: 1px solid black;">
								<a href="#pageBottom">To Bottom</a>
							</td>
							<td width="25%" class="jz_nj_block_body" style="border-top: 1px solid black;">&nbsp;
								
							</td>
							<td width="25%" class="jz_nj_block_body" style="border-top: 1px solid black;">&nbsp;
							
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<tr>
				<td>
					<table width="100%" cellpadding="3" cellspacing="0" border="0">
						<tr>
							<td class="jz_block_td" nowrap>
								<strong>Options</strong>
							</td>
							<td class="jz_block_td" nowrap>
								<strong>Track Name</strong>
							</td>
							<td class="jz_block_td" nowrap>
								<strong>Time</strong>
							</td>
							<td class="jz_block_td" nowrap>
								<strong>Artist</strong>
							</td>
							<td class="jz_block_td" nowrap>
								<strong>Album</strong>
							</td>
							<td class="jz_block_td" nowrap align="center">
								<strong>#</strong>
							</td>
							<td class="jz_block_td" nowrap>
								<strong>Genre</strong>
							</td>
						</tr>
						
						<form name="tracklist" method="POST" action="<?php echo urlize(array()); ?>">
						<input type="hidden" name="<?php echo jz_encode("action"); ?>" value="<?php echo jz_encode("mediaAction"); ?>">
						<input type="hidden" name="<?php echo jz_encode("jz_path"); ?>" value="<?php echo htmlentities(jz_encode($node->getPath("String"))); ?>">
						<input type="hidden" name="<?php echo jz_encode("jz_list_type"); ?>" value="<?php echo jz_encode("tracks"); ?>">
							    <input type="hidden" name="<?php echo jz_encode("sendList"); ?>" value="<?php echo jz_encode("true"); ?>">
							    <input type="hidden" name="randomize" value="false">
						<?php
							$tracks = $node->getSubNodes('tracks',-1);
							foreach($tracks as $track){
								$meta = $track->getMeta();
								echo '<tr><td nowrap width="1%" class="jz_nj_block_body" valign="top">';
								echo '<input type="checkbox" name="jz_list[]" value="'.jz_encode($track->getPath("String")).'" style="width:10px;height:10px;">';
								$display->playButton($track);
								echo " ";
								$display->downloadButton($track);
								
								
								
								echo '</td><td class="jz_nj_block_body" >';
								$display->playLink($track, $track->getName(), "Play ". $track->getName());
								
								
								echo '</td><td width="1%" class="jz_nj_block_body" align="center">';
								echo convertSecMins($meta['length']);
								
								
								echo '</td><td class="jz_nj_block_body" >';
								$artist = $track->getAncestor("artist");
								if ($artist){
									$display->playButton($artist);
									echo " ";
									$display->randomPlayButton($artist);
									echo " ";
									$display->link($artist,$artist->getName());
								} else {
								  echo $meta['artist'];
								} 
								
								echo '</td><td class="jz_nj_block_body" >';
								$display->playButton($node);
								echo " ";
								$display->randomPlayButton($node);
								echo " ";
								$display->downloadButton($node);
								echo " ";
								$display->link($node,$node->getName());
								
								echo '</td><td nowrap width="1%" align="center" class="jz_nj_block_body" >';
								echo $meta['number'];
								
								
								echo '</td><td class="jz_nj_block_body" >';
								if ($artist === false) {
								  $genre = false;
								} else {
								  $genre = $artist->getAncestor("genre");
								}
								if ($genre){
									$display->playButton($genre);
									echo " ";
									$display->randomPlayButton($genre);
									echo " ";
									$display->link($genre,$genre->getName());
								} else {
								  if (!isNothing($meta['genre'])) {
								    echo $meta['genre'];
								  } else {
								    echo '&nbsp;';
								  }
								} 								
								echo '</td></tr>';
							}
						?>
					</table>
					</form>
				</td>
			</tr>
		</table>

		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<tr>
				<td>
					<table width="100%" cellpadding="5" cellspacing="0" border="0">
						<tr>
							<td width="25%" class="jz_nj_block_body" style="border-top: 1px solid black;">&nbsp;
							
							</td>
							<td width="25%" align="center" class="jz_nj_block_body" style="border-top: 1px solid black;">
								<a href="#pageTop">To Top</a>
							</td>
							<td width="25%" class="jz_nj_block_body" style="border-top: 1px solid black;">&nbsp;
								
							</td>
							<td width="25%" class="jz_nj_block_body" style="border-top: 1px solid black;">&nbsp;
							
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?php
	}
?>
