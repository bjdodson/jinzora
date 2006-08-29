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
	
	// Let's require the main classes for all the functions below
	require_once($include_path. 'frontend/class.php');
	require_once($include_path. 'frontend/blocks.php');	
	
	// Now let's set some variables for this specific frontend
	//$css = $include_path. "frontend/frontends/mediacenter/style/$jinzora_skin/default.php";
	//$image_dir = $include_path. 'frontend/frontends/mediacenter/style/'. $jinzora_skin. '/';
	//include($include_path. "frontend/icons.lib.php");	
	
	// override the blocks and frontend
	// This allows me to have specific display items
	// for only this frontend
	class jzBlocks extends jzBlockClass {
	
		// The TrackTable block displays a small table of our tracks
		function trackTable($tracks, $purpose = false) {
			global $row_colors, $jinzora_skin, $root_dir, $show_artist_album, $show_track_num, $this_page;
			
			// Let's setup the objects
			$display = &new jzDisplay();
			
			// Now lets setup our form
			// First we need to know the node for these tracks
			// We can create this by getting the ancestor from the first track
			$node = $tracks[0]->getAncestor('album');
			if (!$node) { $node = $tracks[0]->getParent(); }
			?>
			<form name="albumForm" action="<?php echo $this_page; ?>" method="POST">
			<input type="hidden" name="<?php echo jz_encode("action"); ?>" value="<?php echo jz_encode("mediaAction"); ?>">
			<input type="hidden" name="<?php echo jz_encode("jz_path"); ?>" value="<?php echo htmlentities(jz_encode($node->getPath("String"))); ?>">
			<input type="hidden" name="<?php echo jz_encode("jz_list_type"); ?>" value="<?php echo jz_encode("tracks"); ?>">
			<?php
			
			$i=0; // Counter
			// Now let's loop through the track nodes
			foreach($tracks as $track){
				// Let's get the meta data
				// This will return the meta data (like length, size, bitrate) into a keyed array
				$meta = $track->getMeta();
				
				// Now let's display
				?>
				<table width="100%" cellspacing="0" cellpadding="4">
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td width="1" valign="top">
							<input type="checkbox" name="jz_list[]" value="<?php echo jz_encode($track->getPath("String")); ?>">
						</td>
						<td width="1" valign="top">
							<?php 
								// Now let's link to download this track
								$display->downloadButton($track);
							?>
						</td>
						<td width="1" valign="top" align="right">
							<?php 
								// Did they want track numbers?
								if ($show_track_num == "true"){
									// Now let's link to this track
									$number = $meta['number'];
									if ($number <> ""){
										if (strlen($number) < 2){echo "&nbsp;"; }
										echo $number. ". ";
									}
								}
							?>
						</td>
						<td width="100%" valign="top">
							<?php 
								// Let's display the name of the track with a link to it using our display object
								echo "<nobr>";
								$display->playLink($track, $meta['title']);
								echo "</nobr>";
								
								if ($show_artist_album == "true"){
									echo "<br>";
									// Now let's get the parents
									$parent = $track->getAncestor("album");
									$gparent = $track->getAncestor("artist");
									if ($parent !== false) {
									  $txt1 = $parent->getName();
									} else {
									  $txt1 = "";
									}

									if ($gparent !== false) {
									  $txt2 = $gparent->getName();
									} else {
									  $txt2 = "";
									}
									if ($txt1 != "" && $txt2 != "") {
									  $split = " - ";
									} else {
									  $split = "";
									}
									echo '<div style="font-size:9px;"><nobr>'. $txt2. $split."</nobr><nobr>". $txt1. '</nobr></div>';
								}
							?>
						</td>	
						<td width="1" valign="middle" nowrap align="right">
							
						</td>
						<td width="1" valign="top" nowrap align="right">
							<table width="100%" cellspacing="5" cellpadding="0">
								<tr>
									<td align="right" valign="middle" nowrap>
										<div style="font-size: 8px;">
										<?php 
											// Now let's link to this track
											echo convertSecMins($meta['length']); 
											echo ' &#183; ';
											echo $meta['bitrate']. " Kbit/s";
											echo ' &#183; ';
											echo $meta['size']. " MB";
											echo ' &#183; ';
											$eArr = explode(".",$meta['filename']);
											echo strtoupper($eArr[count($eArr)-1]);
											echo " ";
											
										?>
										</div>	
									</td>
									<td align="left" valign="middle">
										<?php
											$display->playButton($track); 
										?>
									</td>
								</tr>
							</table>						
						</td>
					</tr>
				</table>
				<table width="100%" cellspacing="0" cellpadding="0"><tr bgcolor="#D2D2D2"><td width="100%"></td></tr></table>
				<?php
			}
			// a bit of a hack.. don't know why this wasn't here.
			if ($purpose == "search"){
			  $this->playlistBar();
				echo "</form>";
			}
			$this->blockSpacer();

		}
	}

	class jzFrontend extends jzFrontendClass {
		function jzFrontend() {
			parent::_constructor();
		}
			
		function pageTop($node) {
			global $img_home, $jinzora_skin, $root_dir, $css, $this_page, $cms_mode, 
				   $jzUSER, $include_path, $desc_truncate, $image_size, $jinzora_url, 
				   $image_dir, $jukebox, $jzSERVICES, $jukebox_display, $cms_mode,
				   $show_artist_alpha, $show_artist_list, $allow_resample;

			// Let's setup our objects
			$display = new jzDisplay();
			$blocks = new jzBlocks();
			$smarty = smartySetup();

			// Now let's make sure our Node is set and if not set one
			if (!is_object($node)) $node = new jzMediaNode();			

			// Let's include the settings file
			include_once($include_path. 'frontend/frontends/mediacenter/settings.php');
			
			// Let's set some variables
			$smarty->assign('root_dir', $root_dir);
			
			// Ok, now let's include the first template
			$smarty->display(SMARTY_ROOT. 'templates/mediacenter/header.tpl');
			
			
			
			
			
			
			exit();

			$display->preHeader();	
			
			$smarty->assign('this_page', $this_page);
			$smarty->assign('img_home', $img_home);
			$smarty->assign('cms_mode', $cms_mode);
			$smarty->assign('image_dir', $image_dir);
			$smarty->assign('jinzora_url', $jinzora_url);
			$smarty->display(SMARTY_ROOT. 'templates/mediacenter/header.tpl');
			
			// Now let's see if we should show the jukebox iframe
			$smarty->assign('jukebox_queue', false);
			if (checkPermission($jzUSER,"jukebox_queue")){
				$smarty->assign('jukebox_queue', true);
				if ($jukebox_display == "small" or $jukebox_display == "minimal"){
					$smarty->assign('jukebox_display', "small");
				} else {
					$smarty->assign('jukebox_display', "full");
				}
			}	
			$smarty->display(SMARTY_ROOT. 'templates/mediacenter/jukebox.tpl');
			
			// Let's show the news
			$siteNews = $blocks->siteNews($node);
			$smarty->assign('site_news', $siteNews);
			if ($siteNews <> ""){
				$smarty->display(SMARTY_ROOT. 'templates/mediacenter/site-news.tpl');
			}


			
			
			// Now do we have art or image or desc at the album level
			if ($node->getPType() == "album"){
				if (($art = $node->getMainArt($image_size. "x". $image_size)) <> false or (($desc = $node->getDescription()) <> "")) {	
					$desc = $node->getDescription();
					// Ok, let's display
					echo '<table width="100%" cellspacing="0" cellpadding="5">';
					echo '<tr class="and_head1">';
					echo '<td width="100%" align="left">';
					
					// Let's display the name
					$artist = $node->getAncestor('artist');
					echo "<strong>";
					if ($artist !== false) {
						$display->link($artist,$artist->getName()); 
						echo " - "; 
					}
					echo $node->getName();
					if (!isNothing($node->getYear())){
						echo " (". $node->getYear(). ")";
					}
					echo '</strong><br>';
					if ($art){
						if ($desc){$align="left";}else{$align="center";}
						$display->image($art,$node->getName(),$image_size,$image_size,"limit",false,false,$align,"4","4");
					}
					if ($cms_mode == "false"){
						echo '<div class="jz_artistDesc">';	
					}
					echo $display->returnShortName($desc,$desc_truncate);
					// Do we need the read more link?
					if (strlen($desc) > $desc_truncate){
						$url_array = array();
						$url_array['jz_path'] = $node->getPath("String");
						$url_array['action'] = "popup";
						$url_array['ptype'] = "readmore";
						echo ' <a href="'. urlize($url_array). '" onclick="openPopup(this, 450, 450); return false;">...read more</a>';
					}
					if ($cms_mode == "false"){
						echo '</div>';
					}
					echo '</td></tr></table>';
				}
			}

			// Can this user powersearch?
			$on=true;
			if ($jzUSER->getSetting('powersearch') and $on == true){
				
			if ($cms_mode == "true") {
				$method = "GET";
			} else {
				$method = "POST";
			}
			?>
				
				<table width="100%" cellspacing="0" cellpadding="0"><tr height="2" style="background-image: url('<?php echo $image_dir; ?>row-spacer.gif');"><td width="100%"></td></tr></table>
			<?php
				}
			?>
			<?php
			if (isset($_POST['jz_path'])){
				$jzPath = $_POST['jz_path'];
			} else {
				$jzPath = $_GET['jz_path'];
			}
			
			// Now should we show this bar?
			$bcArray = explode("/",$jzPath);
			?>
			<table width="100%" cellspacing="0" cellpadding="5">
				<tr class="and_head1">
					<td width="50%" valign="middle">
						<?php 
							$url = array();
							echo '<a href="'.urlize($url).'"><img src="'.$image_dir.'open-folder.gif" border="0"></a>';
							// Now let's see if we need the breadcrumbs
							unset($bcArray[count($bcArray)-1]);
							$path = "";
							echo ' <a href="'.urlize($url).'">'. word("Home"). '</a>';	
							foreach($bcArray as $item){
								if ($item <> ""){
									$path .= "/". $item;
									$arr['jz_path'] = $path;
										echo ' / <a href="'. urlize($arr). '">'. $item. '</a>';										
								}
								unset($arr);
							}
						?>
					</td>
					<td width="50%" valign="middle" align="right" nowrap="nowrap">
							<?php
								if ($show_artist_list == "true"){
								?>
								<?php echo word("Artist"). ": "; ?>
								<form action="<?php echo $this_page; ?>" method="post">
								<?php
									$display->hiddenPageVars(); 
									$display->dropdown("artist"); 
								?>
								</form>
								&nbsp; | &nbsp;
							<?php
								}
							?>
							
				   			
				   			<?php echo word('Search:'); ?>
							<form action="<?php echo $this_page  ?>" method="<?php echo $method; ?>">
							<?php foreach (getURLVars($this_page) as $key => $val) { echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) . '">'; } ?>
								<select class="jz_select" name="search_type" style="width:85px">
									<option value="ALL"><?php echo word("All Media"); ?></option>
									<?php
										if (distanceTo("artist") !== false){
											echo '<option value="artists">'. word("Artists"). '</option>'. "\n";
										}
										if (distanceTo("album") !== false) {
										  echo '<option value="albums">' . word("Albums"). '</option>'. "\n";
										}
									?>
									<option value="tracks"><?php echo word("Tracks"); ?></option>
									<option value="lyrics"><?php echo word("Lyrics"); ?></option>
								</select>
								<input type="text" name="search_query" class="jz_input" style="width:125px; font-size:10px ">
								<input type="hidden" name="doSearch" value="true">
								<input type="submit" class="jz_submit" value="Go">
							</form>
							
							
					</td>
				</tr>
			</table>
				
			<?php
			
			
			// Now let's see if we need the breadcrumbs
			if ($_GET['jz_path'] <> ""){
				?>
				<table width="100%" cellspacing="0" cellpadding="0"><tr height="2" style="background-image: url('<?php echo $image_dir; ?>row-spacer.gif');"><td width="100%"></td></tr></table>
				<table width="100%" cellspacing="0" cellpadding="3">
					<tr class="and_head1">
						<td width="1%" valign="middle" nowrap>
							<?php
								if ($cms_mode == "true"){
									$mode = "POST";
								} else {
									$mode = "GET";
								}
								$mode = "GET";
								
								if (isset($_POST['jz_path'])){
									$bcArray = explode("/",$_POST['jz_path']);
								} else {
									$bcArray = explode("/",$_GET['jz_path']);
								}

								// Now we need to cut the last item off the list
								$bcArray = array_slice($bcArray,0,count($bcArray)-1);

								// Now let's display the dropdown for where we are
								echo '<form action="'. $this_page. '" method="'. $mode. '">'. "\n";
								echo '<select style="width:175px" class="jz_select" name="'. jz_encode('jz_path'). '" onChange="form.submit();">'. "\n";
								$parent = $node->getParent();
								$nodes = $parent->getSubNodes("nodes");
								sortElements($nodes);
								foreach ($nodes as $child) {
									$path = $child->getPath("String");
									echo '<option ';
									// Is this the current one?
									if ($child->getName() == $node->getName()){
										echo ' selected ';
									}
									echo 'value="'. jz_encode($path). '">'. $display->returnShortName($child->getName(),20). '</option>'. "\n";
								}
								echo '</select>'. "\n";
								//$display->hiddenVariableField("jz_path");
								$display->hiddenPageVars();					
								echo '<input type="hidden" name="frontend" value="'. $_GET['frontend']. '">'. "\n";
								echo "</form>";
							?>
						</td>
						<td valign="middle" nowrap>
							<?php
								$display->playButton($node);
								echo "&nbsp;";
								$display->randomPlayButton($node);
								
								$url_array = array();
								$url_array['jz_path'] = $node->getPath("String");
								$url_array['action'] = "popup";
								$url_array['ptype'] = "iteminfo"; 
								echo ' <a onclick="openPopup(this, 450, 450); return false;" href="'. urlize($url_array). '"><img src="'. $image_dir. 'more.gif" border="0"></a>';
							?>
						</td>
					<?php if ($display->wantResampleDropdown($node)) { ?>
					<td align="right">
						<?php
								$display->displayResampleDropdown($node, word("Resample") . ": ");
						?>
					</td>
				<?php } ?>
				</tr>
				</table>
				<table width="100%" cellspacing="0" cellpadding="0"><tr height="2" style="background-image: url('<?php echo $image_dir; ?>row-spacer.gif');"><td width="100%"></td></tr></table>
				<?php
			} else if ($display->wantResampleDropdown($node)) { ?>
					<table width="100%" cellspacing="0" cellpadding="0"><tr>
					<td align="right">
						<?php
								$display->displayResampleDropdown($node, word("Resample") . ": ");
						?>
					</td>
				</tr></table>
				<?php }

			// ARTIST ALPHA: in header or only for root? Put the following in pageTop for the first...
			if (/*$node->getLevel() == 0 && */$show_artist_alpha == "true") {
				$blocks->alphabeticalList($node,"artist",0);
			}

		}

		function footer($node=false) {
			global $root_dir, $jinzora_skin, $img_check, $img_check_none, $jzUSER, $version, $jinzora_url, $show_page_load_time,
				   $allow_lang_choice, $allow_style_choice, $allow_interface_change, $image_dir, $jzSERVICES, $jzUSER, $cms_mode, $allow_theme_change;

			if ($node === false) {
			  $node = new jzMediaNode();
			}
			$display = new jzDisplay();
			?>
			<table width="100%" cellspacing="0" cellpadding="0"><tr height="2" style="background-image: url('<?php echo $image_dir; ?>row-spacer.gif');"><td width="100%"></td></tr></table>
			<table width="100%" cellspacing="0" cellpadding="1">
				<tr class="and_head1">
					<td width="50%">
						<?php
							if ($allow_interface_change == "true"){
								$display->interfaceDropdown();
							}
						?>
					</td>
					<td width="50%" align="right">
						<?php
							// Now let's show the admin tools
							if ($allow_lang_choice == "true") {
							  $display->languageDropdown();
							} else if ($jzUSER->getSetting('admin')){
							  $display->systemToolsDropdown($node);
							}
						?>
					</td>
				</tr>
				<tr>
					    <td>
					    <?php
					    if ($allow_style_choice == "true"){
					      $display->styleDropdown();
					    }
						?>
							</td>
							    <td align="right">
							    <?php
							    if ($jzUSER->getSetting('admin') && $allow_lang_choice == "true"){
							      $display->systemToolsDropdown($node);
							    } else {
							      echo '&nbsp;';
							    }
							    
							?>
							</td>
							    
			</table>
			
			
			<table width="100%" cellspacing="0" cellpadding="0"><tr height="2" style="background-image: url('<?php echo $image_dir; ?>row-spacer.gif');"><td width="100%"></td></tr></table>				
			<table width="100%" cellspacing="0" cellpadding="3">
				<tr class="and_head1">
					<td width="100%" align="center">
						<?php
							if ($cms_mode == "false"){
							      $display->loginLink();
							    }
			
							if ($jzUSER->getSetting('edit_prefs') !== false) {
									if ($cms_mode == "false"){echo " | ";}
									//echo " - ";
									$display->popupLink("preferences");
							}
						?>
						<br><br>
						powered by <a href="<?php echo $jinzora_url; ?>">Jinzora</a> version <?php echo $version; ?>
						<br><br>
						<?php
							if ($show_page_load_time == "true" and $_SESSION['jz_load_time'] <> ""){
								// Ok, let's get the difference
								$diff = round(microtime_diff($_SESSION['jz_load_time'],microtime()),3);
								 echo '<br><span class="jz_artistDesc">'. word("generated in"). ": ". $diff. " ". word("seconds"). "</span>&nbsp;<br><br>";
							}
						?>
					</td>
				</tr>
			</table>
			<table width="100%" cellspacing="0" cellpadding="0"><tr height="2" style="background-image: url('<?php echo $image_dir; ?>row-spacer.gif');"><td width="100%"></td></tr></table>
			</td></tr></table>
			<?php
			
			$jzSERVICES->cmsClose();
		}
		
		function standardPage(&$node) {
			global $jinzora_skin, $root_dir, $row_colors, $image_size, $desc_truncate, $image_dir, $jzSERVICES, $show_frontpage_items, $show_artist_alpha, $sort_by_year;

			// Let's setup the objects
			$blocks = &new jzBlocks();
			$display = &new jzDisplay();
			$fe = &new jzFrontend();
						
			// Let's display the header
			$this->pageTop($node);
                        
			// Now let's get the sub nodes to where we are
			if (isset($_GET['jz_letter'])) {
				$root = new jzMediaNode();
				$nodes = $root->getAlphabetical($_GET['jz_letter'],"nodes",distanceTo("artist"));
			} else if ($node->getLevel() == 0 && $show_frontpage_items == "false") {
				$nodes = array();
			} else {
			  $nodes = $node->getSubNodes("nodes");
			}

			// Now let's sort
			if ($sort_by_year == "true" and $node->getPType() =="artist"){
				sortElements($nodes,"year");
			} else {
				sortElements($nodes,"name");
			}
			
			echo '<form name="albumForm" method="POST" action="'.urlize().'">';
			echo '<input type="hidden" name="'.jz_encode('jz_list_type').'" value="'.jz_encode('nodes').'">';
			// Now let's loop through the nodes
			$i=0;
			foreach($nodes as $item){
				?>
				<table width="100%" cellspacing="0" cellpadding="4">
					<tr class="<?php $i = 1 - $i; echo $row_colors[$i];?>">
						<td width="1%" valign="middle">
							<input type="checkbox" name="jz_list[]" value="<?php echo jz_encode($item->getPath("String")); ?>">
						</td>
						<td width="1%" valign="middle">
							<?php
								$display->link($item,'<img src="'. $image_dir. 'folder.gif" border="0">'); 
							?>
						</td>
						<td width="96%" valign="middle">
							<?php 
								// Now let's link to this item
								$name = $item->getName();
								if (!isNothing($item->getYear()) and $item->getPType() == "album"){
									$name .= " (". $item->getYear(). ")";
								}
								$display->link($item,$name); 
							?>
						</td>	
						<td width="1%" valign="middle" nowrap align="right">
							<?php 
								// Now let's show the sub items
								if (($count = $item->getSubNodeCount("nodes")) <> 0){
								  if ($count > 1) {
								    $folder = word("folders");
								  } else {
								    $folder = word("folder");
								  }
									$display->link($item,$count. " ". $folder);
								} else {
									if (($count = $item->getSubNodeCount("tracks")) <> 0){
									  if ($count > 1) {
									    $files = word("files");
									  } else {
									    $files = word("file");
									  }
										$display->link($item,$count. " ". $files);
									}
								}
							?>
						</td>
						<td width="1%" valign="middle" nowrap align="right">
							<?php
								// Let's show a play button
								$display->playButton($item);
								echo "&nbsp;";
								$display->randomPlayButton($item); 
							?>
							&nbsp;
						</td>
					</tr>
					<?php
						// Now do we hvae another row?
						if (($art = $item->getMainArt($image_size. "x". $image_size)) <> false or (($desc = $item->getDescription()) <> "")) {
							// Ok, we had stuff let's do a new row
							?>
							<tr class="<?php echo $row_colors[$i]; ?>">
								<td width="1%" valign="middle">
									
								</td>
								<td width="99%" valign="middle" colspan="4">
									<?php
										if ($art){
											$display->link($item,$display->returnImage($art,$node->getName(),$image_size,$image_size,"limit",false,false,"left","4","4"));
										}
										echo $display->returnShortName($item->getDescription(),$desc_truncate);
										// Do we need the read more link?
										if (strlen($item->getDescription()) > $desc_truncate){
											$url_array = array();
											$url_array['jz_path'] = $item->getPath("String");
											$url_array['action'] = "popup";
											$url_array['ptype'] = "readmore";
											echo ' <a href="'. urlize($url_array). '" onclick="openPopup(this, 450, 450); return false;">...read more</a>';
										}
									?>
								</td>	
							</tr>
							<?php
						}
					?>
				</table>
				<table width="100%" cellspacing="0" cellpadding="0"><tr bgcolor="#D2D2D2"><td width="100%"></td></tr></table>
				<?php
			}
			// Now are there any tracks?
			if (isset($_GET['jz_letter'])) {
				$root = new jzMediaNode();
				//$tracks = $root->getAlphabetical($_GET['jz_letter'],"tracks",-1);
				$tracks = array();
			} else {
			  $tracks = $node->getSubNodes("tracks");
			}

			if (count($tracks) <> 0){
				$blocks->trackTable($tracks);
			}
			if (sizeof($nodes) > 0 || sizeof($tracks) > 0) {
				?>
				<table width="100%" cellspacing="0" cellpadding="0"><tr height="2" style="background-image: url('<?php echo $image_dir; ?>row-spacer.gif');"><td width="100%"></td></tr></table>
					<table width="100%" cellspacing="0" cellpadding="3">
						<tr class="and_head1">
							<td width="100%">
								<a style="cursor:hand" onClick="CheckBoxes('albumForm',true); return false;" href="javascript:;"><img src="<?php echo $image_dir; ?>check.gif" border="0"></a><a style="cursor:hand" onClick="CheckBoxes('albumForm',false); return false;" href="javascript:;"><img src="<?php echo $image_dir; ?>check-none.gif" border="0"></a>
									<?php $display->addListButton(); ?>
									<?php $display->hiddenVariableField('action','mediaAction'); ?>
									<?php $display->hiddenVariableField('path',$_GET['jz_path']); ?>
									
									<?php
										$url_array = array();
										$url_array['action'] = "popup";
										$url_array['ptype'] = "playlistedit";
										echo '<a href="javascript:;" onClick="openPopup('. "'". urlize($url_array). "'". ',600,600); return false;"><img src="'. $image_dir. 'playlist.gif" border="0"></a>';
										echo '&nbsp;'; $display->playlistSelect(115,false,"all");
			
									?>						
								</form>
						</td>
					</tr>
				</table>
				<?php
			}
      echo '</form>';			
			// Now let's close out
			$this->footer($node);
		}
	}
?>