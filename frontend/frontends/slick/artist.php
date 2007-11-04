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
		global  $album_name_truncate, $web_root, $root_dir,						
						$disable_random, $allow_download, $allow_send_email, $amg_search, $echocloud, $include_path, $enable_ratings,
						$img_play, $img_random_play, $this_page, $img_check, $img_check_none, $jzUSER, $img_play_dis, $img_random_play_dis,
						$show_sampler, $show_similar, $show_radio, $show_album_art, $days_for_new, $sort_by_year, $jzSERVICES;			
		
		// Let's setup our objects
		$display = &new jzDisplay();
		$blocks = &new jzBlocks();
		$fe = &new jzFrontend();
		$smarty = smartySetup();
		
		// Let's grab the data from the backend		
		$nodes = $node->getSubNodes("nodes");
		$tracks = $node->getSubNodes("tracks");
				
		// Do we need the left colum
		$show_profile_col = false;
		if (sizeof($nodes) > 0) {
			$show_profile_col = true;
		}
		if ((($node->getMainArt()) <> "" or $node->getDescription() <> "") and $show_album_art <> "false") {
			$show_profile_col = true;
		}
		
		$smarty->assign('show_album_block', false);
		if (sizeof($nodes) > 0) {
			$smarty->assign('show_album_block', true);
		}
		
		$smarty->assign('show_artist_profile', false);
		$art = $node->getMainArt();
		// let's make sure we have a profile to show
		if (($art or $node->getDescription() <> "") and $show_album_art == "true") {	
			$smarty->assign('show_artist_profile', true);
		}
		
		// Now let's see if there are random tracks here
		$smarty->assign('show_tracks', false);
		if (count($tracks) <> 0){
			$smarty->assign('show_tracks', true);
			// Now let's setup our buttons for later
			$playButtons = $display->playLink($node,$img_play,false,false,true). $display->playLink($node,$img_random_play,false,false,true,true);
			// Now let's make sure they can stream
			if (!$jzUSER->getSetting('stream')){
				$playButtons = $img_play_dis. $img_random_play_dis;
			}
			if ($jzUSER->getSetting('download')){
				$playButtons .= $display->downloadButton($node, true, true, true);
			} else {
				$playButtons .= $display->downloadButton($node, true, true, true);
			}
			$playButtons .= $display->podcastLink($node);
			if ($enable_ratings == "true"){
				$playButtons .= $display->rateButton($node, true);		
			}
			$playButtons .= " &nbsp; ";
			
			$smarty->assign('playButtons', $playButtons);
		}		
		
		// Do they want the sampler?
		$smarty->assign('show_sampler', false);
		if ($show_sampler == "true" && sizeof($nodes) > 1){
			$smarty->assign('show_sampler', true);
		}					
		
		// Do they want either of these?
		$smarty->assign('show_sim_col', false);
		if ($show_similar == "true" or $show_radio == "true"){
			$smarty->assign('show_sim_col', true);
		}
					
		$smarty->assign('show_profile_col', $show_profile_col);
		
		// Now let's display the correct template
		if (count($nodes) == 1){
			$smarty->assign('show_sampler', true);
			$_GET['action'] = "viewalltracks";		
		}
		$smarty->display(SMARTY_ROOT. 'templates/slick/artist.tpl');
	}
?>
