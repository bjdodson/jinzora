<?php 
	if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
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
		  $enable_playlist, $track_play_only, $skin, $bg_c, $text_c, $img_discuss_dis, $hierarchy, $random_albums, $frontend, $include_path,
		  $cols_in_genre,$show_frontpage_items,$show_alphabet,$chart_types;
		
		// Let's setup the display object
		$smarty = smartySetup();
		$blocks = new jzBlocks();
		
		
		
		
		
		
		
		// Let's get the site news
		$site_news = $blocks->siteNews($node);
		$smarty->assign('smarty_include', getcwd());
		$smarty->assign('site_news', $site_news);
		$smarty->assign('word_site_news', word("Site News"));
		
		// Now let's show the feature artist/album
		if ($node->getName() <> ""){
			$smarty->assign('editor_pick_title', word("Editors Pick"). ": ". $node->getName());
			$smarty->assign('jz_bg_color', jz_bg_color);			
		}
	
		// Now let's see if we need the featured block or not
		$smarty->assign('show_featured', false);
		if ($node->getLevel() == 0){
			if ($blocks->showFeaturedBlock($node,true)){
				$smarty->assign('show_featured', true);
				//$smarty->assign('featured_data', $blocks->showFeaturedBlock($node));
			}
		}
				
				
					
		// Now let's display the templates	
		$smarty->display(SMARTY_ROOT. 'templates/slick/genre.tpl');
	}
?>
























