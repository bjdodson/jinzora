<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	/**
	* - JINZORA | Web-based Media Streamer -  
	* 
	* Jinzora is a Web-based media streamer, primarily desgined to stream MP3s 
	* (but can be used for any media file that can stream from HTTP). 
	* Jinzora can be integrated into a CMS site, run as a standalone application, 
	* or integrated into any PHP website.  It is released under the GNU GPL.
	* 
	* - Resources -
	* - Jinzora Author: Ross Carlson <ross@jasbone.com>
	* - Web: http://www.jinzora.org
	* - Documentation: http://www.jinzora.org/docs	
	* - Support: http://www.jinzora.org/forum
	* - Downloads: http://www.jinzora.org/downloads
	* - License: GNU GPL <http://www.gnu.org/copyleft/gpl.html>
	* 
	* - Contributors -
	* Please see http://www.jinzora.org/team.html
	* 
	* - Code Purpose -
	* - Contains the Slimzora display functions
	*
	* @since 02.17.04 
	* @author Ross Carlson <ross@jinzora.org>
	* @author Ben Dodson <ben@jinzora.org>
	*/
	require_once($include_path. 'frontend/class.php');
	require_once($include_path. 'frontend/blocks.php');		
	
	// override the blocks and frontend to be slim:
	class jzBlocks extends jzBlockClass {
	
		function trackTable($tracks, $showNumbers = true, $showArtist = false) {
			global $media_dir, $jinzora_skin, $hierarchy, $album_name_truncate, $row_colors, 
			$img_more, $img_email, $img_rate, $img_discuss, $num_other_albums, $show_images, $jzUSER;					
			
			if (sizeof($tracks) == 0) return;
			
			// Let's setup the new display object
			$display = &new jzDisplay();
			
			// Now let's setup the big table to display everything
			$i=0;
			?>
			<table class="jz_track_table" width="100%" cellpadding="3">
			<?php
			foreach ($tracks as $child) {
				// First let's grab all the tracks meta data
				$metaData = $child->getMeta();
				?>
				<tr class="<?php echo $row_colors[$i]; ?>">				
					<td width="99%" valign="top" nowrap>
						<?php
						$display->downloadButton($child);
						echo "&nbsp;";
						$display->playButton($child,false,false);
						echo "&nbsp;";
						if ($jzUSER->getSetting('stream')){
							$display->link($child);
						} else {
							echo $child->getName();
						}
						echo " (". convertSecMins($metaData['length']). ")";
						?>
					</td>
				</tr>
				<?php
				$i = 1 - $i;
			}
			// Now let's set a field with the number of checkboxes that were here
			echo "</table>";
		}
		
		function nodeTable($nodes){
			global $media_dir, $jinzora_skin, $hierarchy, $album_name_truncate, $row_colors, 
			$img_more, $img_email, $img_rate, $img_discuss, $num_other_albums, $show_images, 
			$sort_by_year, $show_descriptions, $item_truncate;					

			if (sizeof($nodes) == 0) return;
			// Let's setup the new display object
			$display = &new jzDisplay();
			
			$album = false;
			if ($nodes[0]->getPType() == "album"){
				$album = true;
			}
			
			if ($sort_by_year == "true" and $album){
				sortElements($nodes,"year");
			} else {
				sortElements($nodes,"name");
			}
			
			if ($item_truncate == ""){
				$item_truncate = "25";
			}
			
			// Now let's setup the big table to display everything
			$i=0;
			?>
			<table class="jz_track_table" width="100%" cellpadding="3" cellspacing="0" border="0">
			<?php
			foreach ($nodes as $child) {
				$year = $child->getYear();
				$dispYear = "";
				if ($year <> "-" and $year <> "" and $album == true){
					$dispYear = " (". $year. ")";
				}
				?>
				<tr class="<?php echo $row_colors[$i]; ?>">
					<td nowrap valign="top" colspan="2">
					<?php 
						$display->playButton($child,false,false);
						echo "&nbsp;";
						$display->randomPlayButton($child,false,false);
						echo "&nbsp;";
						$name = $display->returnShortName($child->getName(),$item_truncate);
						$display->link($child, $name);
						echo $dispYear;
					?>
					</td>
				</tr>
				<?php
					// Let's see if we need the next row
					$art = $child->getMainArt("75x75");
					$desc = $display->returnShortName($child->getDescription(),200);
					if (($art <> "" or $desc <> "") and $show_images == "true"){
						?>
						<tr class="<?php echo $row_colors[$i]; ?>" nowrap>
							<td valign="top">
							<?php
								if ($show_images == "true" && (($art = $child->getMainArt("40x40")) !== false)) {
									$display->link($child,$display->returnImage($art,$child->getName(),40,40,"limit",false,false));
								}
							?>
							</td>
							<td valign="top" >
								<?php
									if ($desc <> "" and $show_descriptions == "true"){
										echo '<span class="jz_artistDesc">'. $desc. '</span>';
									}
								?>
							</td>
						</tr>
						<?php
					}
				?>
				<?php
				$i = 1 - $i; // cool trick ;)
			}
			echo "</table>";
		}
	}

	class jzFrontend extends jzFrontendClass {
		function jzFrontend() {
			parent::_constructor();
		}
			
		function pageTop($title) {
			global $img_up_arrow, $row_colors;
			
			$display = new jzDisplay();
			
			if (isset($_GET['jz_path']) || isset($_POST['jz_path'])) {
				if (isset($_POST['jz_path'])){
					$bcArray = explode("/",$_POST['jz_path']);
					$me = new jzMediaNode($_POST['jz_path']);
				} else {
					$bcArray = explode("/",$_GET['jz_path']);
					$me = new jzMediaNode($_GET['jz_path']);
				}
			
				// Now we need to cut the last item off the list
				$bcArray = array_slice($bcArray,0,count($bcArray)-1);
				// Now let's display the crumbs
				$path = "";
				$arr = array();
				if (isset($_GET['frame'])){
					$arr['frame'] = $_GET['frame'];
				}
				
				?>
				<table class="jz_track_table" width="100%" cellpadding="3">
					<tr class="<?php echo $row_colors[1]; ?>">
						<td>
							<?php
								$link = urlize($arr);
								echo $img_up_arrow. "&nbsp;";
								jzHREF($link,"","","","Home");
								echo "&nbsp;";
								
								for ($i=0; $i < count($bcArray); $i++){
									echo $img_up_arrow. "&nbsp;";
									$path .= $bcArray[$i] ."/";
									$curPath = substr($path,0,strlen($path)-1);
									
									$arr = array();
									$arr['jz_path'] = $curPath;
									if (isset($_GET['frame'])){
										$arr['frame'] = $_GET['frame'];
									}
									
									$link = urlize($arr);
									jzHREF($link,"","","",$bcArray[$i]);
									echo "&nbsp;";
								}
								if (sizeof($bcArray) > 0) {
									echo "<br>";
								}
							?>
						</td>
					</tr>
				<?php
				
				if ($_GET['jz_path'] <> ""){
					?>
						<tr class="<?php echo $row_colors[1]; ?>">
							<td>
								<?php
									$display->playButton($me,false,false);
									echo "&nbsp;";
									$display->randomPlayButton($me,false,false);
									echo "&nbsp;";
									echo $title; 
								?>
							</td>
						</tr>
					<?php
				}
				echo '</table>';
				
				
			} else {
				echo $title;
			}
		}

		function footer() {
			global $root_dir, $jinzora_url, $jzSERVICES, $cms_mode, $jzUSER; 
			
			$display = new jzDisplay();		
			
			echo "<center>";
			
			if ($cms_mode == "false"){
				$display->loginLink();
			}
			if ($jzUSER->getSetting('edit_prefs') !== false) {
					if ($cms_mode == "false"){echo " | ";}
					$display->popupLink("preferences");
			}
			echo '<br><a href="'. $jinzora_url. '" target="_blank"><img src="'. $root_dir. '/style/images/slimzora.gif" border="0"></a><br><br>';
			echo '</td></tr></table>';
			
			$jzSERVICES->cmsClose();
		}
		
		function standardPage(&$node) {
			global $include_path;
			
			$blocks = &new jzBlocks();
			$display = &new jzDisplay();
			
			$display->preheader($node->getName(),$this->width,$this->align,true,true,true,true);
			include_once($include_path. "frontend/frontends/slimzora/css.php");
			$this->pageTop($node->getName());
			
			$nodes = $node->getSubNodes('nodes');
			$tracks = $node->getSubNodes('tracks');
			
			$blocks->trackTable($tracks, false, false);
			$blocks->nodeTable($nodes);
			
			$this->footer();
		}
	}
?>