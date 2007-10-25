<?php 
	if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
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
	
	class jzBlocks extends jzBlockClass {
	
		// The TrackTable block displays a small table of our tracks
		function trackTable($tracks, $purpose = false) {
			include(dirname(__FILE__). "/blocks/track-table.php");
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
				   $show_artist_alpha, $show_artist_list, $allow_resample, $img_login, $img_prefs, $help_access;

			// Let's setup our objects
			$display = new jzDisplay();
			$blocks = new jzBlocks();
			$smarty = smartySetup();
			
			jzBlock('page-header');
			jzBlock('jukebox');
			jzBlock('site-news');
			jzBlock('album-info-block');
			jzBlock('browse-bar');
			jzBlock('breadcrumbs');

			if ($show_artist_alpha == "true") {
				$blocks->alphabeticalList($node,"artist",0);
			}
		}

		function footer($node=false) {
			global $root_dir, $jinzora_skin, $img_check, $img_check_none, $jzUSER, $version, $jinzora_url, $show_page_load_time,
				   $allow_lang_choice, $allow_style_choice, $allow_interface_change, $image_dir, $jzSERVICES, $jzUSER, $cms_mode, $allow_theme_change;

			if ($node === false){$node = new jzMediaNode();}						
			$smarty = smartySetup();
			$display = new jzDisplay();
			
			jzBlock('footer');
		}
		
		function standardPage(&$node) {
			global $jinzora_skin, $root_dir, $row_colors, $image_size, $desc_truncate, $image_dir, $jzSERVICES, $show_frontpage_items, $show_artist_alpha, $sort_by_year;

			// Let's setup the objects
			$blocks = &new jzBlocks();
			$display = &new jzDisplay();
			$fe = &new jzFrontend();
			$smarty = smartySetup();
						
			// Let's display the header
			$this->pageTop($node);
                  
			jzBlock('standard-page');


			// Now are there any tracks?
			if (isset($_GET['jz_letter'])) {
				$root = new jzMediaNode();
				$tracks = array();
			} else {
			  $tracks = $node->getSubNodes("tracks");
			}
			if (count($tracks) <> 0){
				$blocks->trackTable($tracks);
			}
			
			jzBlock('playlist-bar');
			
			// Now let's close out
			$this->footer($node);
		}
	}
?>