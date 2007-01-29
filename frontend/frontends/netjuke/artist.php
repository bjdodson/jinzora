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
		global $media_dir, $skin, $hierarchy, $album_name_truncate, $web_root, $root_dir, 
		       $jz_MenuItemLeft, $jz_MenuSplit, $jz_MenuItemHover, $jz_MainItemHover, $jz_MenuItem,
			   $disable_random, $allow_download, $allow_send_email, $amg_search, $echocloud, $include_path, 
			   $img_play, $img_random_play, $this_page, $img_check, $img_check_none, $jzUSER, $img_play_dis, $img_random_play_dis,
 		       $show_sampler, $show_similar, $show_radio, $show_album_art, $days_for_new, $img_new;			
							
		$display = &new jzDisplay();
		$blocks = &new jzBlocks();
		$fe = &new jzFrontend();
		?>
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<tr>
				<td align="center" valign="top">
					<table width="100%" cellpadding="3" cellspacing="0" border="0">
						<tr>
							<td class="jz_block_td" colspan="3">
								<?php
									$parent = $node->getParent();
								?>
								<strong>ARTISTS IN THIS GENRE [<?php echo $display->link($parent,$parent->getName()); ?>] (<?php echo $parent->getSubNodeCount("nodes"); ?>)</strong>
							</td>
						</tr>
						<?php
							// Now let's get all the genres
							$parent = $node->getParent();
							$items = $parent->getSubNodes("nodes");
							$c=0;
							foreach($items as $genre){
								// Now let's start our row
								if ($c == 0){echo '<tr>';}
								
								echo '<td class="jz_nj_block_body">';
								//$display->playButton($genre); 
								//echo " ";
								//$display->randomPlayButton($genre);
								//echo " ";
								$display->link($genre, $genre->getName(), word("Browse: "). $genre->getName());						
								echo " (". $genre->getSubNodeCount("nodes"). ")";
								echo '</td>';
								$c++;
								if ($c==3){$c=0;}
							}
							// Now let's finish out
							if ($c <> 0){
								while($c<3){
									echo '<td class="jz_nj_block_body">&nbsp;</td>';
									$c++;
								}	
							}						
						?>
					</table>
				</td>
				<td align="center">&nbsp;</td>
				
				<?php
					// Now what to show?
					if (isset($_GET['jz_path'])){
				?>
					<td align="center" valign="top">
						<table width="100%" cellpadding="3" cellspacing="0" border="0">
							<tr>
								<td class="jz_block_td" colspan="3">
									<strong>ALBUMS BY <?php echo $display->link($node,$node->getName()); ?> (<?php echo $node->getSubNodeCount("nodes"); ?>) [<?php echo $display->link($parent,$parent->getName()); ?>] (<?php echo $parent->getSubNodeCount("nodes"); ?>)</strong>
								</td>
							</tr>
							<?php
								$albums = $node->getSubNodes("nodes",distanceTo("album",$node));
								$c=0;
								foreach($albums as $album){
									// Now let's start our row
									if ($c == 0){echo '<tr>';}
									
									echo '<td class="jz_nj_block_body">';
									$display->playButton($album); 
									echo " ";
									$display->randomPlayButton($album);
									echo " ";
									$display->link($album, $album->getName(), word("Browse: "). $album->getName());						
									echo " (". $album->getSubNodeCount("tracks"). ")";
									echo '</td>';
									$c++;
									if ($c==3){$c=0;}
								}
								// Now let's finish out
								if ($c <> 0){
									while($c<3){
										echo '<td class="jz_nj_block_body">&nbsp;</td>';
										$c++;
									}	
								}
							?>
						</table>
						<br>
						<table width="100%" cellpadding="3" cellspacing="0" border="0">
							<tr>
								<td class="jz_block_td" colspan="2">
									<strong>ALBUM ART BY <?php echo $display->link($node,$node->getName()); ?> (<?php echo $node->getSubNodeCount("nodes"); ?>) [<?php echo $display->link($parent,$parent->getName()); ?>] (<?php echo $parent->getSubNodeCount("nodes"); ?>)</strong>
								</td>
							</tr>
							<?php
								$albums = $node->getSubNodes("nodes",distanceTo("album",$node));
								$c=0;
								foreach($albums as $child){
									if (($art = $child->getMainArt("150x150")) == false) {								
										// TODO: Create the default image here IF they want it
										$art = "style/images/default.jpg";
									}
									
									// Now let's start our row
									if ($c == 0){echo '<tr>';}
									
									echo '<td class="jz_nj_block_body" align="center">';
									
									$display->link($child, $display->returnShortName($child->getName(),$album_name_truncate) . $dispYear, $child->getName() . $dispYear, "jz_artist_album");
									echo "<br>";
									$display->link($child,$display->returnImage($art,$child->getName(),150,false,"fit"), $child->getName() . $dispYear);
									echo '</td>';
									$c++;
									if ($c==2){$c=0;}
								}
								// Now let's finish out
								if ($c <> 0){
									while($c<2){
										echo '<td class="jz_nj_block_body">&nbsp;</td>';
										$c++;
									}	
								}
							?>
														
						</table>
					</td>
				<?php
					} else {
				?>
					<td align="center" valign="top">
						<table width="100%" cellpadding="3" cellspacing="0" border="0">
							<tr>
								<td class="jz_block_td">
									<strong>LATEST ARTISTS</strong>
								</td>
							</tr>
							<tr>
								<td class="jz_nj_block_body">
									<table width="100%" cellpadding="3" cellspacing="0" border="0">
									<?php
										// Now how many should we show?
										$show = round(((count($genres) / 3)) * 1.5);
										$blocks->showCharts($node,"newartists",$show,false,false);
									?>
									</table>
								</td>
							</tr>
						</table>
					</td>
					<td align="center">&nbsp;</td>
					<td align="center" valign="top">
						<table width="100%" cellpadding="3" cellspacing="0" border="0">
							<tr>
								<td class="jz_block_td">
									<strong>LATEST ALBUMS</strong>
								</td>
							</tr>
							<tr>
								<td class="jz_nj_block_body">
									<table width="100%" cellpadding="3" cellspacing="0" border="0">
									<?php
										$show = round(((count($genres) / 3)) * 1.5);
										$blocks->showCharts($node,"newalbums",$show,false,false);
									?>
									</table>
								</td>
							</tr>
						</table>
					</td>
				<?php
					}
				?>
					
				
				
				
			</tr>
		</table>
		<br>
		<?php
	}
?>
