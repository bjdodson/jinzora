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
		global $cols_in_genre, $cellspacing, $this_page, $img_play, $artist_truncate, $main_table_width, $img_random_play, 
		$directory_level, $web_root, $root_dir, $img_more, $media_dir, $show_sub_numbers, $show_all_checkboxes, 
		$img_more_dis, $img_play_dis, $img_random_play_dis, $url_seperator, $days_for_new, $img_rate, $enable_ratings,
		$enable_discussion, $img_discuss, $show_sub_numbers, $disable_random, $info_level, 
		$enable_playlist, $track_play_only, $css, $jinzora_skin, $bg_c, $text_c, $img_discuss_dis, $hierarchy, $random_albums, $frontend;
		
		// Let's see if the theme is set or not, and if not set it to the default
                //if (isset($_SESSION['cur_theme'])){ $_SESSION['cur_theme'] = $jinzora_skin; }
		
		// Let's setup the display object
		$display = &new jzDisplay();
		$blocks = &new jzBlocks();

		$fe = &new jzFrontend();
		// Now let's display the header 
		// Let's see if they are viewing the Genre or not 

		// Now let's get all the data for this level
		$retArray = $node->getSubNodes("both");
		
		// Now let's display the site description
		$news = $blocks->siteNews($node);
		if ($news <> ""){
			echo "<br><center>". $news. "<center>";
		}
		echo '<br><br>';
		//$blocks->blockHeader();
		$blocks->blockBodyOpen();
		// Now let's start our table to put the stuff in
		echo '<br>';
		jzTableOpen("100%","3","jz_col_table_main");
		jzTROpen("jz_col_table_tr");
		
		// Now let's figure out how wide our colum should be
		$cols_in_genre = 3;
		$col_width = returnColWidth(count($retArray));	
		
		// Now let's figure out how many artists per column 
		// adding the .49 make sure it always rounds up 
		$folder_per_column = round(count($retArray) / $cols_in_genre + .49,0);
		$first_loop = "";
		
		// Let's initialize some variables
 		$ctr = 1;
			
                // Let's setup our form for below
                echo '<form action="'. $_SESSION['prev_page']. '" name="trackForm" method="POST">';	
		// Now let's loop through that array, displaying the items 
		for ($i=0; $i < count($retArray); $i++){
			// Let's make sure that we found items to display
			if ($retArray[$i]->isLeaf()) {
				continue;
			}
			else {
			// Now let's see if this is a NEW directory or not
			//$new_from = checkForNew(jzstripslashes($genre. "/". $retArray[$i])); 
			// NEW BACKEND: don't know how to handle this yet.
				
			// Let's count so we know where we are in making the columns 
			// Then we'll add the links to the sub pages 
			if ($ctr == 1){
				if ($first_loop == ""){
					$first_loop = "no";
					jzTDOpen($col_width,"left","top","","0");
				} else {
					jzTDClose();
					jzTDOpen($col_width,"left","top","","0");
				}
			}

			// Let's see if we need to truncate the name 
			$displayItem = returnItemShortName($retArray[$i]->getName(),$artist_truncate);
			
			// Now let's get the number of sub items
			$fldr_ctr = $retArray[$i]->getSubNodeCount();
			
			

			// Let's open our table
			jzTableOpen("100", "0","jz_col_table");
			jzTROpen("jz_col_table_tr");
			
			// Let's see if they are only a viewing user or not 
			if ($_SESSION['jz_access_level'] <> "viewonly" and $_SESSION['jz_access_level'] <> "lofi"){
				// Now let's show them the info button
				if ($info_level == "all" or ($info_level == "admin" and $_SESSION['jz_access_level'] == "admin")){
					jzTDOpen("1","left","top","","0");
					$item_url = $root_dir. '/popup.php?type='. $hierarchy[$retArray[$i]->getLevel()]. '&info='.urlencode($retArray[$i]->getPath("String")). '&cur_theme='. $_SESSION['cur_theme'];
					jzHREF($item_url,"_blank","jz_col_table_href","openPopup(this, 320, 520, false, 'Popup'); return false;",$img_more);
					jzTDClose();
				}
				// Now let's see if they only wanted to see track plays
				if ($track_play_only <> "true"){
					jzTDOpen("1","left","top","","0");
					$display->playButton($retArray[$i]);
					jzTDClose();
				}
				// Now let's see if they wanted to see random icons
				if ($disable_random == "false"){
					jzTDOpen("1","left","top","","0");
					$display->randomPlayButton($retArray[$i]);
					jzTDClose();
				}
				// Now let's show them the rating link, if they wanted it
				if ($enable_ratings == "true"){
					jzTDOpen("1","left","top","","0");
					$display->rateButton($retArray[$i]);
					jzTDClose();
				}
				if ($enable_discussion == "true"){
					jzTDOpen("1","left","top","","0");
					$item_url = $root_dir. '/popup.php?type=discuss&info='. rawurlencode($retArray[$i]->getPath("String")). '&cur_theme='. $_SESSION['cur_theme'];
					// Now let's figure out which icon to use
					if ($retArray[$i]->getDiscussion() == ""){ $img = $img_discuss_dis; } else { $img = $img_discuss; }
					jzHREF($item_url,"_blank","","openPopup(this, 500, 500, false, 'Popup'); return false;",$img_discuss);
					jzTDClose();
				}
			} else {
				// Ok, they are view only, so let's show them the disabled icons
				jzTDOpen("1","left","top","","0");
				echo $img_more_dis;
				jzTDClose();
				
				jzTDOpen("1","left","top","","0");
				echo $img_play_dis;
				jzTDClose();
				
				if ($disable_random == "false"){
					jzTDOpen("1","left","top","","0");
					echo $img_random_play_dis;
					jzTDClose();
				}
			}				
			
			// Let's give them the check box for the playlist addition IF they wanted it
			if ($show_all_checkboxes == "true" and $enable_playlist <> "false"){
				jzTDOpen("1","left","top","","0");
				echo '<input class="jz_checkbox" name="track-'. $i. '" type=checkbox value="'. $root_dir. $media_dir. '/'. rawurlencode($genre). '/'. urlencode($retArray[$i]). '/">'; 
				jzTDClose();
			} // NEW BACKEND: This part will need a bit more work.
			
			jzTDOpen("1","left","top","","0");
			echo "&nbsp;";
			jzTDClose();
			
			// Let's see if the data is new or not
			$new_data = "";
			$new_from = "";
			if ($new_from <> ""){
				$new_data = "";
			}
							
			jzTDOpen("90","left","top","","0");

			$item_url = $this_page. $url_seperator. 'path='. rawurlencode($retArray[$i]->getPath("String"));

			// Now let's display the link
			$display->link($retArray[$i]);
					
			// Let's see if this is new or not
			if ($new_from <> ""){ echo ' <img src="'. $root_dir. '/style/'. $jinzora_skin. '/new.gif" border=0 '. $new_data. '>'; }
						
			// Now let's see if they wanted ratings
			if ($enable_ratings == "true"){
				// Ok, now let's see if there is a rating file
				//$display->rateButton($node);// NEW BACKEND: this needs work.
			}
			
			// Now let's return the description
			$descData = $retArray[$i]->getShortDescription();
			if ($descData){
				echo "<br>". stripslashes($descData). "<br><br>";
			}
			
			// Now let's close out
			jzTDClose();
			jzTRClose();
			jzTableClose();
                        
			// Now let's increment for out column counting
			if ($ctr == $folder_per_column){ $ctr=1; } else { $ctr++; }
			}
		} // go to next loop
                echo '</form>';
		
		// Now let's set a hidden form field so we'll know how many boxes there were
		echo '<input type="hidden" name="numboxes" value="'. $i. '">';
		
		// Now let's close our table 
		jzTableClose();
		echo "<br>\n";		
		// Now let's see if we should display random albums
		if ($random_albums <> "0"){
		  $blocks->randomAlbums($node, $node->getName());
		  echo "<br>";
		}

		// we might still have tracks.
		$tracks = $node->getSubNodes("tracks");
		if (sizeof($tracks) > 0) {
		  $blocks->trackTable($tracks, false);
		  echo '<br>';
		}

		$blocks->blockBodyClose();
		echo '<br>';
		// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
		//
		// Need to update this to new abstraction layer
		//
		// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
		
		// Now let's look and see if there are any songs in this directory that we need to display
		// This will also test for the chart boxes and display them
		// NEW BACKEND: This needs to be reworked. (Check array for isLeaf == ture
		//$songsFound = lookForMedia(jzstripslashes(urldecode($web_root. $root_dir. $media_dir. "/". $genre)));
		
		// Now let's show the playlist bar
		//if ($enable_playlist <> "false"){ displayPlaylistBar($songsFound); }
	}
?>
