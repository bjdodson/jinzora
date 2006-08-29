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
	
		// The TrackTable block displays a small table of our tracks
		function trackTable($tracks) {
			global $show_artist_album, $show_track_num, $this_page;
			
			// Let's setup the objects
			$display = &new jzDisplay();
			
			// Let's get all the tracks
			$node = $tracks[0]->getAncestor('album');
			
			// Now let's loop through the track nodes
			foreach($tracks as $track){
				$meta = $track->getMeta();
				// Did they want track numbers?
				if ($show_track_num == "true"){
					// Now let's link to this track
					$number = $meta['number'];
					if ($number <> ""){
						echo $number. " - ";
					}
				}			
				// Let's display the name of the track with a link to it using our display object
				$display->playLink($track, $meta['title']);
				echo " (". convertSecMins($meta['length']). ")<br>"; 
			}
		}
	}
	class jzFrontend extends jzFrontendClass {
		function jzFrontend() {
			parent::_constructor();
		}			
		function pageTop($node) {
			global $this_page, $include_path, $jinzora_url;			
			
			// Let's setup our objects
			$display = new jzDisplay();
			
			// Let's include the settings file
			include_once($include_path. 'frontend/frontends/simple/settings.php');
					
			echo '<a href="'. $this_page. '">Home</a> - ';
			$display->loginLink();
			echo "<br>";
			// Now let's see if we need the breadcrumbs
			if ($_GET['jz_path'] <> ""){
				if (isset($_POST['jz_path'])){
					$bcArray = explode("/",$_POST['jz_path']);
				} else {
					$bcArray = explode("/",$_GET['jz_path']);
				}
				$path="";$br=false;
				foreach($bcArray as $item){
					if ($item <> ""){
						$path .= "/". $item;
						$arr['jz_path'] = $path;
						$data = new jzMediaNode($path);
						echo ' - (';
						$display->playLink($data, "P", word("Play"), false, false, false);
						echo "-";
						$display->playLink($data, "R", word("Play Random"), false, false, true);
						echo ') <a href="'. urlize($arr). '">'. $item. '</a>';
					}
					unset($arr);
					$br=true;
				}
			}
			if ($br){echo "<br>";}
		}

		function footer($node=false) {
			global $jinzora_url, $allow_interface_change, $show_full_footer, $version;

			$display = new jzDisplay();			
			if ($allow_interface_change == "true"){
				$display->interfaceDropdown();
			}					
			if ($show_full_footer == "true"){
				$diff = round(microtime_diff($_SESSION['jz_load_time'],microtime()),3);
				echo word("generated in"). ": ". $diff. " ". word("seconds"). "<br>";
				echo 'powered by <a href="'. $jinzora_url. '">Jinzora</a> version '. $version;
			}
		}
		
		function standardPage(&$node) {
			global $show_artist_alpha, $truncate_length, $sort_by_year;

			// Let's setup the objects
			$blocks = &new jzBlocks();
			$display = &new jzDisplay();
						
			// Let's display the header
			$this->pageTop($node);
			// ARTIST ALPHA: in header or only for root? Put the following in pageTop for the first...
			if ($node->getLevel() == 0 && $show_artist_alpha == "true") {
				$blocks->alphabeticalList($node,"artist",0);
			}
                        
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
			// Now let's loop through the nodes
			foreach($nodes as $item){
				// Now let's link to this item
				$name = $item->getName();
				if (strlen($name) > $truncate_length){
					$name = substr($name,0,$truncate_length). "..";
				}
				if (!isNothing($item->getYear()) and $item->getPType() == "album"){
					$name .= " (". $item->getYear(). ")";
				}				
				// Let's show a play button
				echo " (";
				$display->playLink($item, "P", word("Play"), false, false, false);
				echo "-";
				$display->playLink($item, "R", word("Play Random"), false, false, true);
				echo ") ";
				$display->link($item,$name); 
				echo "<br>";
			}
			// Now are there any tracks?
			$tracks = $node->getSubNodes("tracks");
			if (count($tracks) <> 0){
				$blocks->trackTable($tracks);
			}
			// Now let's close out
			$this->footer($node);
		}
	}
?>