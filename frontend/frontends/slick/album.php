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
	* Code Purpose: This page contains all the album related related functions
	* Created: 9.24.03 by Ross Carlson
	*
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

 	// This function displays all the Genres or Artists 
	function drawPage(&$node){
		global  $album_name_truncate, $row_colors, $num_other_albums, $jzUSER, $show_similar, $show_radio, $jzSERVICES;

		// Let's setup the new display object
		$display = &new jzDisplay();
		$blocks = &new jzBlocks();
		$fe = &new jzFrontend();
		$smarty = smartySetup();
		
		// Now should we show this colum
		$parent = $node->getAncestor("artist");
		if ($parent !== false) {
			$parentArt = $parent->getMainArt();
			$parentDesc = $parent->getDescription();
			$desc = $node->getDescription();
			$art = $node->getMainArt();	
		}
		$smarty->assign('show_profile_col', false);
		if ($parentArt <> false or $parentDesc <> "" or $desc <> "" or $art <> false){
			$smarty->assign('show_profile_col', true);
		}
		
		$smarty->assign('show_other_albums', false);
		if ($num_other_albums > 0) {
			$smarty->assign('show_other_albums', true);
		}

		$smarty->assign('show_right_col', false);
		// Do they want either of these?
		if ($show_similar == "true" or $show_radio == "true"){
			$smarty->assign('show_right_col', true);
			
			$parent = $node->getAncestor('artist');
			$simArray = $jzSERVICES->getSimilar($parent);
			$simArray = seperateSimilar($simArray);
			$smarty->assign('show_similar', false);
			if (sizeof($simArray['matches']) <> 0) {
				$smarty->assign('show_similar', true);
			}
		}
		
		$smarty->assign('show_radio', $show_radio);
		$smarty->assign('show_similar', $show_similar);
		
		// Now let's display the template
		//$smarty->display(SMARTY_ROOT. 'templates/slick/header.tpl');
		$smarty->display(SMARTY_ROOT. 'templates/slick/album.tpl');
	}
?>