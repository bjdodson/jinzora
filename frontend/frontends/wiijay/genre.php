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
		global $cellspacing, $this_page, $img_play, $artist_truncate, $main_table_width, $img_random_play, 
		  $directory_level, $web_root, $root_dir, $img_more, $media_dir, $show_sub_numbers, $show_all_checkboxes, 
		  $img_more_dis, $img_play_dis, $img_random_play_dis, $url_seperator, $days_for_new, $img_rate, $enable_ratings,
		  $enable_discussion, $img_discuss, $show_sub_numbers, $disable_random, $info_level, 
		  $enable_playlist, $track_play_only, $css, $skin, $bg_c, $text_c, $img_discuss_dis, $hierarchy, $random_albums, $frontend, $include_path,$show_frontpage_items,$show_alphabet,$chart_types,$fe,$num_artist_cols,$show_artist_art,$art_size,$artist_art_size;
		
		// Let's see if the theme is set or not, and if not set it to the default
               //if (isset($_SESSION['cur_theme'])){ $_SESSION['cur_theme'] = $skin; }
		
		// if you were looking this, sorry for the hack ;)
		// Override icons and other styles:
		handleFrontendOverrides();

		// Let's setup the display object
		$blocks = &new jzBlocks();
		$display = &new jzDisplay();
		$fe = &new jzFrontend();
		
		?>		
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<tr>
				<td align="center" valign="top">
					<?php
						// Now let's display the site description
						/*$news = $blocks->siteNews($node);
						if ($news <> ""){
							?>
							<table width="100%" cellpadding="3" cellspacing="0" border="0">
								<tr>
									<td class="jz_block_td" colspan="3">
										<strong><?php echo word("Site News"); ?></strong>
									</td>
								</tr>
								<tr>
									<td class="jz_nj_block_body" width="33%">
									<?php echo $news; ?>
									</td>
								</tr>
							</table>
							<br>
					<?php
						}*/

		   $lvl = isset($_GET['jz_letter']) ? ($_GET['jz_level'] + $node->getLevel() - 1): $node->getLevel();
		$show_art = false;
		switch ($hierarchy[$lvl]){
		case "genre":
		  $pg_title = word("ALL GENRES");
		  break;
		case "artist":
		  $pg_title = word("ALL ARTISTS");
		  if ($show_artist_art == "true") {
		    $show_art = true;
		    $my_art_size = $artist_art_size;
		  }
		  break;
		case "album":
		  $pg_title = word("ALL ALBUMS");
		  if ($show_album_art == "true") {
		    $show_art = true;
		    $my_art_size = $art_size;
		  }
		  break;
		default:
		  $pg_title = word("ALL GENRES");
		  break;
		}
					?>
					<table width="100%" cellpadding="3" cellspacing="0" border="0">
						<tr>
					   <?php
		                                       if (isset($_GET['jz_charts'])) {
							 $node = new jzMediaNode();
							 chartHelper("newalbums");
							 chartHelper("recentplayalbum");
							 
							 echo '</tr>';
							 echo '<tr><td colspan="2">';
							 jzBlock('randomAlbums');
							 echo '</td></tr>';
							 return;
						       }
 		                                       echo '<td class="jz_block_td" colspan="3">';
							       if (isset($_GET['jz_letter'])) {
								 $genres = $node->getAlphabetical($_GET['jz_letter'],"nodes",$_GET['jz_level']);
							       } else {
								 $genres = $node->getSubNodes("nodes");
							       }
							       
									// Now for the title
									if ($_GET['jz_path'] == ""){
										echo '<strong>'. $pg_title. "</strong>";
									} else {
										
										echo '<strong>ARTISTS IN ';
										$display->link($node,$node->getName());
										echo ' ('. $node->getSubNodeCount("nodes"). ")</strong>";
										
									}
								?>
							</td>
						</tr>
						<?php
							
							    $colwidth = floor(100/$num_artist_cols);
							$c=0;
							foreach($genres as $genre){
								// Now let's start our row
								if ($c % $num_artist_cols == 0){
								  if ($c > 0) {
								    echo '</tr>';
								  }
								  echo '<tr>';
								}
								
								echo '<td class="jz_nj_block_body" width="'.$colwidth.'%" valign="top">';
								$display->playButton($genre); 
								echo " ";
								$linktext = $display->returnShortName($genre->getName(),25);
								if ($show_art) {
								  if ($art = $genre->getMainArt($my_art_size . 'x' . $my_art_size)) {
								    $linktext .= '<br/>';
								    $linktext .= $display->returnImage($art);
								  }
								}
								
								$display->link($genre, $linktext, word("Browse: "). $genre->getName());						
								//echo " (". $genre->getSubNodeCount("both"). ")";
								echo '</td>';
								$c++;
							}
							// Now let's finish out
							while ($c % $num_artist_cols != 0){
							  echo '<td class="jz_nj_block_body">&nbsp;</td>';
							  $c++;
							}						
						?>
					  </tr>
					</table>
				</td>
				<td align="center">&nbsp;</td>
				
				<?php
					// Now what to show?
					if ($_GET['jz_path'] <> ""){
					/*
				?>
					<td align="center" valign="top">
						<table width="100%" cellpadding="3" cellspacing="0" border="0">
							<tr>
								<td class="jz_block_td" colspan="3">
									<strong>ALBUMS IN THIS GENRE [<?php $display->link($node,$node->getName()); ?>] (<?php echo $node->getSubNodeCount("nodes",2); ?>)</strong>
								</td>
							</tr>
							<?php
								$albums = $node->getSubNodes("nodes",distanceTo("album",$node));
								$c=0;
								foreach($albums as $album){
									// Now let's start our row
									if ($c == 0){echo '<tr>';}
									
									echo '<td class="jz_nj_block_body" nowrap width="33%">';
									$display->playButton($album); 
									echo " ";
									$display->randomPlayButton($album);
									echo " ";
									$display->link($album, $display->returnShortName($album->getName(),15), word("Browse: "). $album->getName());						
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
					</td>
				<?php
					*/
					} else {
					/*
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
					*/
					}
				?>
					
				
				
				
			</tr>
		</table>
		<br>
		<?php
	}

function chartHelper($type) {
  global $node;
  $blocks = new jzBlocks();

  switch($type) {
  case "newartists":
    $title = "LATEST ARTISTS";
    break;
  case "recentplayalbum":
    $title = "PLAYED ALBUMS";
    break;
  case "newalbums":
    $title = "LATEST ALBUMS";
    break;
  }
  ?>
 <td align="center" valign="top">
    <table width="100%" cellpadding="3" cellspacing="0" border="0">
      <tr>
        <td class="jz_block_td" width="50%">
          <strong><?php echo $title; ?></strong>
        </td>
      </tr>
      <tr>
        <td class="jz_nj_block_body" width="50%">
          <table width="100%" cellpadding="3" cellspacing="0" border="0">
<?php
          $blocks->showCharts($node,$type,8,false,false,array('allow_breaks' => 'true'));
?>
         </table>
        </td>
      </tr>
    </table>
  </td>
<?php
}

?>