<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	// Let's set the image directory
        global $image_dir,$jzUSER,$jzSERVICES;

	// Let's clean up the variables
	$genreDispName = str_replace("'","",getInformation($node,"genre")); 
	$artistDispName = str_replace("'","",getInformation($node,"artist"));

	// Now we need to include the menu library
	include_once($include_path. "frontend/menus/menu.lib.php");
	
	// Now we need to do a fix on all the words to remove the ' from them (if they had them)
	$word_play_all_albums_from = str_replace("'","",word("Play all albums from")); 
	$word_randomize_all_albums_from = str_replace("'","",word("Randomize all albums from")); 
	$word_play = str_replace("'","",word("Play")); 
	$word_selected = str_replace("'","",word("Selected")); 
	$word_session_playlist = str_replace("'","",word("Session Playlist")); 
	$word_new_playlist = str_replace("'","",word("New Playlist")); 
	$word_new = str_replace("'","",word("New")); 
	$word_media_management = str_replace("'","",word("Media Management")); 
	$word_actions = str_replace("'","",word("Actions")); 
	$word_search = str_replace("'","",word("Search")); 
	$word_rewrite_tags = str_replace("'","",word("Rewrite tags")); 
	$word_download_album = str_replace("'","",word("Download album")); 
	$word_group_features = str_replace("'","",word("Group Features")); 
	$word_rate = str_replace("'","",word("Rate")); 
	$word_play_album = str_replace("'","",word("Play album")); 
	$word_play_random = str_replace("'","",word("Play Random")); 
	$word_discuss = str_replace("'","",word("Discss")); 
	$word_item_information = str_replace("'","",word("Item Information")); 
	$word_change_art = str_replace("'","",word("Change Art")); 
	$word_browse_album = str_replace("'","",word("Browse Albums")); 
	$word_edit_album_info = str_replace("'","",word("Edit information")); 
	$word_information = str_replace("'","",word("Information")); 
	$word_echocloud = str_replace("'","",word("Similar Artists")); 
	$word_update_cache = str_replace("'","",word("Update Cache")); 
	$word_show_hidden = str_replace("'","",word("Show Hidden")); 
	$word_bulk_edit = str_replace("'","",word("Bulk edit")); 
	$word_retrieve_lyrics = str_replace("'","",word("Retrieve Lyrics")); 	
	$word_show_all_tracks_from = str_replace("'","",word("Show all tracks from")); 	
	$word_share_via_email = str_replace("'","",word("Share via email")); 	
?>
<script language="JavaScript" type="text/javascript">
var myMenu =
[
	[null,'<?php echo $word_actions; ?>',null,null,'',
		<?php
			// Let's see if they only wanted track plays
			if ($track_play_only <> "true"){
		?>
		['<img src="<?php echo $image_dir; ?>play.gif" />','<?php echo $word_play_all_albums_from. ' <em>'. $artistDispName. '</em>'; ?>','<?php echo $display->link($node, false, false, false, true, true, false, true); ?>',null,''],
		<?php
			}
		?>
		<?php
			if ($disable_random == "false"){
		?>
			['<img src="<?php echo $image_dir; ?>random.gif" />','<?php echo $word_randomize_all_albums_from. ' <em>'. $artistDispName. '</em>'; ?>','<?php echo $display->link($node, false, false, false, true, true, true, true); ?>',null,''],
		<?php
			}
		?>
		
		['<img src="<?php echo $image_dir; ?>browse.gif" />','<?php echo $word_browse_album; ?>',null,null,'',
		<?php
			// Let's loop through
			foreach ($nodes as $child) {
				$year = $child->getYear();
				if ($year <> "-"){ $dispYear = " (". $year. ")"; } else { $dispYear = ""; }
				if (($art = $child->getMainArt("20x20")) !== false) {
					$image = $display->returnImage($art,$child->getName(),30,false,"fit");
				} else {
					$image = '<img src="'. $image_dir. "browse.gif". '">';
				}
				$albumDispName = $child->getName(). $dispYear;
				$brws_link = $display->link($child,false, false, false, false, true, false);
				?>['<?php echo $image; ?>','<em><?php echo $albumDispName; ?></em>','<?php echo $brws_link; ?>',null,''],<?php
			}
		?>
		],
		<?php
			// Let's see if they only wanted track plays
			if ($track_play_only <> "true"){
		?>
		['<img src="<?php echo $image_dir; ?>play.gif" />','<?php echo $word_play_album; ?>',null,null,'',
		<?php
			// Let's loop through
			foreach ($nodes as $child) {
				$year = $child->getYear();
				if ($year <> "-"){ $dispYear = " (". $year. ")"; } else { $dispYear = ""; }
				if (($art = $child->getMainArt("20x20")) !== false) {
					$image = $display->returnImage($art,$child->getName(),30,false,"fit");
				} else {
					$image = '<img src="'. $image_dir. "browse.gif". '">';
				}
				$albumDispName = $child->getName(). $dispYear;
				$play_song_link = $display->link($child, false, false, false, true, true, false, true);
				?>['<?php echo $image; ?>','<em><?php echo $albumDispName; ?></em>','<?php echo $play_song_link; ?>',null,''],<?php
			}
		?>
		],
		<?php
			}
		?>
		<?php
			if ($disable_random == "false"){
		?>
		['<img src="<?php echo $image_dir; ?>random.gif" />','<?php echo $word_play_random; ?>',null,null,'',
	
		<?php
			// Let's loop through
			foreach ($nodes as $child) {
				$year = $child->getYear();
				if ($year <> "-"){ $dispYear = " (". $year. ")"; } else { $dispYear = ""; }
				if (($art = $child->getMainArt("20x20")) !== false) {
					$image = $display->returnImage($art,$child->getName(),30,false,"fit");
				} else {
					$image = '<img src="'. $image_dir. "browse.gif". '">';
				}
				$albumDispName = $child->getName(). $dispYear;
				$play_song_link = $display->link($child, false, false, false, true, true, true, true);
				?>['<?php echo $image; ?>','<em><?php echo $albumDispName; ?></em>','<?php echo $play_song_link; ?>',null,''],<?php
			}
		?>
		],
		<?php
			}
		?>
		
		<?php
			if (checkPermission($jzUSER,"download")){				
				?>
				['<img src="<?php echo $image_dir; ?>download.gif" />','<?php echo $word_download_album; ?>',null,null,'',
				<?php
					// Let's loop through
					foreach ($nodes as $child) {
						$year = $child->getYear();
						if ($year <> "-"){ $dispYear = " (". $year. ")"; } else { $dispYear = ""; }
						if (($art = $child->getMainArt("20x20")) !== false) {
							$image = $display->returnImage($art,$child->getName(),30,false,"fit");
						} else {
							$image = '<img src="'. $image_dir. "browse.gif". '">';
						}
						$albumDispName = $child->getName(). $dispYear;
						$dnl_url = $display->downloadButton($child, true);
						?>['<?php echo $image; ?>','<em><?php echo $albumDispName; ?></em>','<?php echo $dnl_url; ?>',null,''],<?php
					}
				?>
				],
				<?php
			}
			// Now let's see if they wanted to enable email sending
			/*
			if ($allow_send_email == "true"){
				?>
				['<img src="<?php echo $image_dir; ?>email.gif" />','<?php echo $word_share_via_email; ?>','',null,'',
				<?php
					// Let's loop through
					foreach ($nodes as $child) {
						$year = $child->getYear();
						if ($year <> "-"){ $dispYear = " (". $year. ")"; } else { $dispYear = ""; }
						if (($art = $child->getMainArt("20x20")) !== false) {
							$image = $display->returnImage($art,$child->getName(),30,false,"fit");
						} else {
							$image = '<img src="'. $image_dir. "browse.gif". '">';
						}
						$albumDispName = $child->getName(). $dispYear;
						$share_url = "";
						?>['<?php echo $image; ?>','<?php echo "<em>". $albumDispName. "</em>"; ?>','<?php echo $share_url; ?>',null,'',],<?php
					}
				?>
				],
				<?php
			}
			*/
		?>
	],
	_cmSplit,	
	
	
	
	
	
	
	<?php
		/*
		// Now we'll show the Admin menus
		if ($_SESSION['jz_access_level'] == "admin"){
	?>
		[null,'<?php echo $word_media_management; ?>',null,null,'',
		<?php
			if ($_SESSION['jz_access_level'] == "admin"){
		?>
			['<img src="<?php echo $image_dir; ?>art.gif" />','<?php echo $word_change_art; ?>',null,null,'',
			<?php
				// Let's loop through
				foreach ($nodes as $child) {
					$year = $child->getYear();
					if ($year <> "-"){ $dispYear = " (". $year. ")"; } else { $dispYear = ""; }
					if (($art = $child->getMainArt("20x20")) !== false) {
						$image = $display->returnImage($art,$child->getName(),30,false,"fit");
					} else {
						$image = '<img src="'. $image_dir. "art.gif". '">';
					}
					$albumDispName = $child->getName(). $dispYear;
					$chg_url = "";
					?>['<?php echo $image; ?>','<em><?php echo $albumDispName; ?></em>','<?php echo $chg_url; ?>',null,''],<?php
				}

			?>	
			],
			<?php
				$this_url = $this_page. $url_seperator. 'ptype=updatealltag&info='. urlencode($genre). "/". urlencode($artist). "&return=". urlencode($genre). "|". urlencode($artist);
			?>
			['<img src="<?php echo $image_dir; ?>edit.gif" />','<?php echo $word_rewrite_tags; ?>','<?php echo $this_url; ?>',null,''],
			
			
			
			
			<?php
				if($enable_meta_search){
					$this_url = $this_page. $url_seperator. 'ptype=retrievemetadata&info='. urlencode($genre). "/". urlencode($artist);
				?>
				['<img src="<?php echo $image_dir; ?>art.gif" />','Attempt Meta Data Retrevial','',null,'',
					<?php
						$image = '<img src="'. $image_dir. "art.gif". '">';
						if (is_file($web_root. $root_dir. $media_dir. "/". $genre. "/". $artist. "/". $artist. ".jpg")){
							$image = '<img height="30" src="'. str_replace("%2F","/",rawurlencode($root_dir. $media_dir. "/". $genre. "/". $artist. "/". $artist. ".jpg")). '">';
						} else {
							// Now let's see if there is art with a different name
							$retArray = readDirInfo($web_root. $root_dir. $media_dir. "/". $genre. "/". $artist,"file");
							for ($e=0; $e < count($retArray); $e++){		
								if (preg_match("/\.($ext_graphic)$/i", $retArray[$e])){
									$image = '<img width="30" src="'. str_replace("%2F","/",rawurlencode($root_dir. $media_dir. "/". $genre. "/". $artist. "/". $retArray[$e])). '">';
								}
							}
						}
					?>
					['<?php echo $image; ?>','<?php echo $artistDispName; ?>','<?php echo $this_url; ?>',null,''],
					_cmSplit,
					<?php
						// Let's loop through
						foreach ($nodes as $child) {
							$year = $child->getYear();
							if ($year <> "-"){ $dispYear = " (". $year. ")"; } else { $dispYear = ""; }
							if (($art = $child->getMainArt("20x20")) !== false) {
								$image = $display->returnImage($art,$child->getName(),30,false,"fit");
							} else {
								$image = '<img src="'. $image_dir. "art.gif". '">';
							}
							$albumDispName = $child->getName(). $dispYear;
							$this_url = "";
							?>['<?php echo $image; ?>','<em><?php echo $albumDispName; ?></em>','<?php echo $this_url; ?>',null,''],<?php
						}
					?>	
				],
			<?php
			}
			?>
			<?php
				$this_url = $this_page. $url_seperator. 'ptype=retrievelyrics&info='. urlencode($genre). "/". urlencode($artist). "&return=". urlencode($genre). "|". urlencode($artist);
			?>
			['<img src="<?php echo $image_dir; ?>discuss.gif" />','<?php echo $word_retrieve_lyrics; ?>','<?php echo $this_url; ?>',null,''],
			
			<?php
				$this_url = $this_page. $url_seperator. 'ptype=artist&genre='. urlencode($genre). '&artist='. urlencode($artist). '&showhidden='. urlencode($artist);
			?>
			['<img src="<?php echo $image_dir; ?>browse.gif" />','<?php echo $word_show_hidden; ?>','<?php echo $this_url; ?>',null,''],
			

			_cmSplit,
			['<img src="<?php echo $image_dir; ?>edit.gif" />','<?php echo $word_bulk_edit; ?>','',null,'',
				<?php
					// Let's loop through
					foreach ($nodes as $child) {
						$year = $child->getYear();
						if ($year <> "-"){ $dispYear = " (". $year. ")"; } else { $dispYear = ""; }
						if (($art = $child->getMainArt("20x20")) !== false) {
							$image = $display->returnImage($art,$child->getName(),30,false,"fit");
						} else {
							$image = '<img src="'. $image_dir. "edit.gif". '">';
						}
						$albumDispName = $child->getName(). $dispYear;
						$chg_url = "";
						?>['<?php echo $image; ?>','<em><?php echo $albumDispName; ?></em>','<?php echo $chg_url; ?>',null,''],<?php
					}
				?>	
			],
			['<img src="<?php echo $image_dir; ?>edit.gif" />','<?php echo $word_item_information; ?>','',null,'',
				<?php
					$this_url = $this_page. $url_seperator. 'ptype=artist&genre='. urlencode($genre). '&artist='. urlencode($artist). '&editmenu=artist&info='. urlencode($artist);
				?>
				<?php
					$image = '<img src="'. $image_dir. "art.gif". '">';
					if (is_file($web_root. $root_dir. $media_dir. "/". $genre. "/". $artist. "/". $artist. ".jpg")){
						$image = '<img height="30" src="'. str_replace("%2F","/",rawurlencode($root_dir. $media_dir. "/". $genre. "/". $artist. "/". $artist. ".jpg")). '">';
					}
				?>
				['<?php echo $image; ?>','<?php echo $artistDispName; ?>','<?php echo $this_url; ?>',null,''],
				_cmSplit,
				<?php
					// Let's loop through
					foreach ($nodes as $child) {
						$year = $child->getYear();
						if ($year <> "-"){ $dispYear = " (". $year. ")"; } else { $dispYear = ""; }
						if (($art = $child->getMainArt("20x20")) !== false) {
							$image = $display->returnImage($art,$child->getName(),30,false,"fit");
						} else {
							$image = '<img src="'. $image_dir. "edit.gif". '">';
						}
						$albumDispName = $child->getName(). $dispYear;
						$chg_url = "";
						?>['<?php echo $image; ?>','<em><?php echo $albumDispName; ?></em>','<?php echo $chg_url; ?>',null,''],<?php
					}
				?>	
			],		
		<?php
			}
		?>		
		],
		<?php
			}
			*/
		?>
		
		
		
		
		<?php
			// Ok, now lets show the search menu
			if ($enable_discussion == "true" or $enable_ratings == "true" or $echocloud <> "0" or $amg_search == "true"){
		?>
		<?php 
			// Let's see if they wanted to auto search Echocloud
			if ($echocloud <> "0" or $amg_search == "true"){
		?>	
			[null,'<?php echo $word_information; ?>',null,null,'',
				<?php
					if ($amg_search == "true"){
				?>
				['<img src="<?php echo $image_dir; ?>more.gif" />','<?php echo $word_search. " AMG"; ?>',null,null,'',
					<?php
						// Let's loop through
						foreach ($nodes as $child) {
							$year = $child->getYear();
							if ($year <> "-"){ $dispYear = " (". $year. ")"; } else { $dispYear = ""; }
							if (($art = $child->getMainArt("20x20")) !== false) {
								$image = $display->returnImage($art,$child->getName(),30,false,"fit");
							} else {
								$image = '<img src="'. $image_dir. "edit.gif". '">';
							}
							$albumDispName = $child->getName(). $dispYear;
							$search_url = "";
							?>['<?php echo $image; ?>','<em><?php echo $albumDispName; ?></em>','<?php echo $search_url; ?>',null,''],<?php
						}
					?>,
					_cmSplit,
					<?php
						$image = '<img src="'. $image_dir. "art.gif". '">';
						if (is_file($web_root. $root_dir. $media_dir. "/". $genre. "/". $artist. "/". $artist. ".jpg")){
							$image = '<img height="30" src="'. str_replace("%2F","/",rawurlencode($root_dir. $media_dir. "/". $genre. "/". $artist. "/". $artist. ".jpg")). '">';
						}
					?>
					['<?php echo $image; ?>','<em><?php echo $artistDispName; ?></em>','<?php echo $dnl_url; ?>',null,''],
				],
				<?php
					// This ends the IF from way above
					}
				?>
				<?php 
					// Let's see if they wanted to auto search Echocloud
					if ($echocloud <> "0"){				
						// Ok, now we need to search Echocloud to get matches to this artist
						$sim = $jzSERVICES->getSimilar($node);
						$simArr = seperateSimilar($sim);
						// Let's make sure that opened ok
						
						if ($sim != array()){							
						  // Ok, now let's clean up what we got back
						  // Now let's see if there are results
						  $ecArray = $simArr['matches'];
							if (count($ecArray) >= 1){
								?>
									['<img src="<?php echo $image_dir; ?>more.gif" />','<?php echo $word_echocloud; ?>',null,null,'',
								<?php
							}
							$ctr=0;
							$urla = array();
							
							for ($i=0; $i < count($ecArray); $i++){
							  $ecArtist = $ecArray[$i]->getName();
							  if (($ecArtist <> $artist) and ($ecArtist <> "")){
							    $urla['jz_path'] = $ecArray[$i]->getPath("String");
							    $dnl_url = urlize($urla);
										?>
											['<img src="<?php echo $image_dir; ?>more.gif" />','<em><?php echo $ecArtist; ?></em>','<?php echo $dnl_url; ?>',null,''],
										<?php
										$ctr++;
									}
							}
							if (count($ecArray) > 1){
								?>
									],
								<?php
							}
						}
					}
				?>
			],
		<?php
			}
		?>		
		<?php
			if ($enable_discussion == "true" or $enable_ratings == "true"){
				?>
				_cmSplit,
				[null,'<?php echo $word_group_features; ?>',null,null,'',
					['<img src="<?php echo $image_dir; ?>rate.gif" />','<?php echo $word_rate; ?>',null,null,'',
					<?php
						for ($i=0; $i < count($dataArray); $i++){
							if ($dataArray[$i]['name'] <> ""){
								$albumDispName = str_replace("'","",$dataArray[$i]['name']);
								$album = $dataArray[$i]['name'];
								if ($sort_by_year == "true"){
									if ($dataArray[$i]['year'] <> ""){
										$albumDispName .= " (". $dataArray[$i]['year']. ")";
									} 
								}
								$rate_url = $root_dir. '/popup.php?type=rate&info='. base64_encode(urlencode($genre). "/". urlencode($artist). "/". urlencode($album)). '&cur_theme='. $_SESSION['cur_theme'];
								// Now let's see if there is an album image, and if so display it
								$image = '<img src="'. $image_dir. "rate.gif". '">';
								if (is_file($web_root. $root_dir. $media_dir. "/". $genre. "/". $artist. "/". $album. "/". $album. ".jpg") and $show_thumbnails_in_menu == "true"){
									$image = '<img width="30" src="'. str_replace("%2F","/",rawurlencode($root_dir. $media_dir. "/". $genre. "/". $artist. "/". $album. "/". $album. ".jpg")). '">';
								} else {
									// Now let's see if there is art with a different name
									$retArray = readDirInfo($web_root. $root_dir. $media_dir. "/". $genre. "/". $artist. "/". $album,"file");
									for ($e=0; $e < count($retArray); $e++){		
										if (preg_match("/\.($ext_graphic)$/i", $retArray[$e])){
											$image = '<img width="30" src="'. str_replace("%2F","/",rawurlencode($root_dir. $media_dir. "/". $genre. "/". $artist. "/". $album. "/". $retArray[$e])). '">';
										}
									}
								}
								?>
									[_cmNoAction,'<td class="jzMenuItemLeft"><?php echo $image; ?></td><td class="jzMenuItem">&nbsp;<em><a target="_blank" onclick="openPopup(this,300,125); return false;" style="font-size: 11px; text-decoration: none;" href="<?php echo $rate_url; ?>"><em><?php echo $albumDispName; ?></em></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>'],
								<?php
							}
						}
					?>	
					],
					['<img src="<?php echo $image_dir; ?>discuss.gif" />','<?php echo $word_discuss; ?>',null,null,'',
					<?php
						for ($i=0; $i < count($dataArray); $i++){
							if ($dataArray[$i]['name'] <> ""){
								$albumDispName = str_replace("'","",$dataArray[$i]['name']);
								$album = $dataArray[$i]['name'];
								if ($sort_by_year == "true"){
									if ($dataArray[$i]['year'] <> ""){
										$albumDispName .= " (". $dataArray[$i]['year']. ")";
									} 
								}
								$rate_url = $root_dir. '/popup.php?type=discuss&info='. base64_encode(urlencode($genre). "/". urlencode($artist). "/". urlencode($album)). '&cur_theme='. $_SESSION['cur_theme'];
								
								// Now let's see if there is an album image, and if so display it
								$image = '<img src="'. $image_dir. "discuss.gif". '">';
								if (is_file($web_root. $root_dir. $media_dir. "/". $genre. "/". $artist. "/". $album. "/". $album. ".jpg") and $show_thumbnails_in_menu == "true"){
									$image = '<img width="30" src="'. str_replace("%2F","/",rawurlencode($root_dir. $media_dir. "/". $genre. "/". $artist. "/". $album. "/". $album. ".jpg")). '">';
								} else {
									// Now let's see if there is art with a different name
									$retArray = readDirInfo($web_root. $root_dir. $media_dir. "/". $genre. "/". $artist. "/". $album,"file");
									for ($e=0; $e < count($retArray); $e++){		
										if (preg_match("/\.($ext_graphic)$/i", $retArray[$e])){
											$image = '<img width="30" src="'. str_replace("%2F","/",rawurlencode($root_dir. $media_dir. "/". $genre. "/". $artist. "/". $album. "/". $retArray[$e])). '">';
										}
									}
								}
								?>
									[_cmNoAction,'<td class="jzMenuItemLeft"><?php echo $image; ?></td><td class="jzMenuItem">&nbsp;<em><a target="_blank" onclick="openPopup(this,500,400); return false;" style="font-size: 11px; text-decoration: none;" href="<?php echo $rate_url; ?>"><em><?php echo $albumDispName; ?></em></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>'],
								<?php
							}
						}
					?>	
					],
				],
				<?php
			}
		?>
	<?php
		}
	?>
];

</script>
<div id="myMenuID"></div>
<script language="JavaScript" type="text/javascript">
	cmDraw ('myMenuID', myMenu, 'hbr', cmjz, 'jz');
</script>