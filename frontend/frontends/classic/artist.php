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
			   $disable_random, $allow_download, $allow_send_email, $amg_search, $echocloud, 
			   $include_path, $enable_ratings,$truncate_artist_description,$sort_by_year, $cms_mode;			
							
		$display = &new jzDisplay();
		$blocks = &new jzBlocks();
		$fe = &new jzFrontend();
		
		// Let's see if the theme is set or not, and if not set it to the default
                //if (isset($_SESSION['cur_theme'])){ $_SESSION['cur_theme'] = $skin; }
		
		$nodes = $node->getSubNodes("nodes");
		$tracks = $node->getSubNodes("tracks");
		echo '<br>';
		$blocks->blockBodyOpen();
		// Now let's setup the big table to display everything
		?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="100%" valign="top" colspan="2">
					<?php
						// Now let's include the menu system for the artist
						// First we'll need to setup some variables
						$play_all_artist = $display->link($node, false, false, false, true, true, false, true);
						$play_all_artist_rand = $display->link($node, false, false, false, true, true, true, true);
						$artist = $node->getName();

						// Now let's inlucde the artist menu
						include_once($include_path. "frontend/menus/artist-menu.php");
					?>
				</td>
			</tr>
			<tr><td width="100%" valign="top" colspan="2" height="5"></td></tr>
			<tr>
				<td width="35%" valign="top">
					<!-- Let's show the albums -->
					<?php
						// First let's sort this up
						if ($sort_by_year == "true"){
							sortElements($nodes,"year");
						} else {
							sortElements($nodes,"name");
						}
						
						foreach ($nodes as $child) {
							$display->playButton($child);
							$display->randomPlayButton($child);
							if ($enable_ratings == "true"){
								$display->rateButton($child);
							}
							echo '<input type="checkbox">';
							echo '&nbsp;';

							$year = $child->getYear();
							$dispYear = "";
							if (!isNothing($year)){
								$dispYear = " (". $year. ")";
							}
							$display->link($child, $display->returnShortName($child->getName(),$album_name_truncate) . $dispYear, $child->getName() . $dispYear);
							// *******************************
							// Need to know how to return NEW information HERE
							// *******************************
							echo "<br>";
						}
						
						include_once($include_path. 'frontend/frontends/classic/general.php');
						//displayArtistPLBar();
						echo "<br>";

						// Now let's show the art
						if (($art = $node->getMainArt('200x200')) !== false) {
							$display->image($art,$node->getName(),200,200,"limit",false,false,"left","5","5");
						}
						// Now let's show the description
						if ($cms_mode == "false"){
							echo '<span class="jz_artistDesc">';
						}
						$desc = $node->getDescription();
						echo $display->returnShortName($desc,$truncate_artist_description);
						if (strlen($desc) > $truncate_artist_description){
							$url_array = array();
							$url_array['jz_path'] = $node->getPath("String");
							$url_array['action'] = "popup";
							$url_array['ptype'] = "readmore";
							echo ' <a href="'. urlize($url_array). '" onclick="openPopup(this, 450, 450); return false;">read more</a>';
						}
						if ($cms_mode == "false"){
							echo '</span>';
						}
					?>
				</td>
				<td width="65%" valign="top">
					<!-- Let's show the art -->
					<?php
					// Ok, now let's set our table for the art
					echo '<table width="100%" cellpadding="5" cellspacing="0" border="0">';
					$c=0;
					foreach ($nodes as $child) {
						$year = $child->getYear();
						$dispYear = "";
						if (!isNothing($year)){
							$dispYear = " (". $year. ")";
						}
						// Now let's see if we should start a row
						if ($c==0){ echo '</tr><tr>'; }
							// Now let's display the data
							echo '<td width="50%" align="center"><center>';
							if (($art = $child->getMainArt('200x200')) !== false) {								
							} else {
								// TODO: Create the default image here IF they want it
								$art = "style/images/default.jpg";
							}
							$display->link($child, $display->returnShortName($child->getName(),$album_name_truncate) . $dispYear, $child->getName() . $dispYear, "jz_artist_album");
							echo '<br>';
							$display->link($child,$display->returnImage($art,$child->getName(),200,false,"fit",false,false,false,false,false,"0","jz_album_cover_picture"), $child->getName() . $dispYear);
							echo '<br><br></center></td>';
						// Now let's increment so we'll know where to make the table
						$c++;
						if ($c==2){$c=0;}
					}
					echo '</table>';
					?>			
				</td>
			</tr>
		</table>
		<br>
		<?php
		$blocks->trackTable($tracks, false);
		$blocks->blockBodyClose();
		echo '<br>';
	}
?>