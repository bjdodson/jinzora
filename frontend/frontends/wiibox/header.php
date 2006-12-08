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
	
	class jzBlocks extends jzBlockClass {
	
	}
	
	
	class jzFrontend extends jzFrontendClass {
		function jzFrontend() {
			global $jzSERVICES;
			// force the embedded player
			define('JZ_FORCE_EMBEDDED_PLAYER','true');
			$jzSERVICES->loadService('players','xspf');
			
			
			parent::_constructor();
		}		
					
		function standardPage(&$node) {
			global $show_artist_alpha, $truncate_length, $sort_by_year, $jzSERVICES;

			// Let's setup the objects
			$blocks = &new jzBlocks();
			$display = &new jzDisplay();
			
			$smarty = smartySetup();
   
			// Now let's get the sub nodes to where we are
			if (isset($_GET['jz_letter'])) {
				$root = new jzMediaNode();
				$nodes = $root->getAlphabetical($_GET['jz_letter'],"nodes",distanceTo("artist"));
			} else {
			  $nodes = $node->getSubNodes("nodes");
			}
			if ($sort_by_year == "true"){
				sortElements($nodes,"year");
			} else {
				sortElements($nodes,"name");
			}
			
			$itemArray = array();
			// Now let's loop through the nodes
			foreach($nodes as $item){
				$itemArray[] = array("name" => $item->getName(), 
                                     "path" => $item->getPath(),
                                     "link" => $display->link($item,"VIEW",false,false,true),
                                     "playlink" => $display->playLink($item,"PLAY",false,false,true)
                                     );
			}
			$smarty->assign("nodes",$itemArray);
			
			// Now are there any tracks?
			$tracks = $node->getSubNodes("tracks");
			if (count($tracks) <> 0){
				
				$smary->assign("tracks",array());
			}
			
			$smarty->assign("playerURL",urlize(array("frame" => "player")));
			$smarty->assign("bodyURL",urlize(array("frame" => "body")));
			
			// OUTPUT HTML
			
			// Is this our first pageview?
			if (!isset($_GET['frame'])) {
				$display->preheader();
				jzTemplate($smarty,"page");
			} else if ($_GET['frame'] == "player"){
				$display->preheader();
				jzTemplate($smarty,"player");
			} else {
				$display->preheader();
				jzTemplate($smarty,"body");
			}
			
		}
	}
?>