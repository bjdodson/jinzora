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
 * Code Purpose: This page contains all the blocks for the default frontend.
 * Created: 12/22/04
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

class jzBlockClass {
  
	/**
	* Constructor for the class.
	* 
	* @author Ben Dodson
	* @version 12/22/04
	* @since 12/22/04
	*/
	function jzBlocks() {
	
	}


  /*
   * Displays the select box of
   * our current playlist.
   *
   * @author Ben Dodson
   * @since 9/5/05
   * @version 9/5/05
   **/
  function playlistDisplay() {
    global $jzUSER;
    $display = new jzDisplay();
    ?><select name="playlistTracks" size="18" class="jz_select" style="width: 200px">
    <?php
    $pl = $jzUSER->loadPlaylist();
    $list = $pl->getList();
    foreach ($list as $el) {
      echo "<option value=\"" . jz_encode(htmlentities($el->getPath("String"))) . "\">" . htmlentities($display->returnShortName($el->getName(),25)) . "</option>";
    }
    echo '</select>';
  }
	
	/**
	 * Shows the description for a node.
	 *
	 * @author Ross Carlson, Ben Dodson
	 * @since 6/26/05
	 * @version 6/26/05
	 **/
	function description($node, $desc_truncate = false) {
		include(dirname(__FILE__). "/blocks/description.php");
	}
	
	/**
	 * Handles all browsing elements for a page (grid + alphabetical listing(s?))
	 * 
	 * @author Ross Carlson
	 * @since 4/5/05
	 * @version 4/5/05
	 * @param $node Object the node we are viewing
	 *
	 **/
	function showAllArt($node){
		include(dirname(__FILE__). "/blocks/show-all-art.php");
	}

	/**
	 * Handles all browsing elements for a page (grid + alphabetical listing(s?))
	 * 
	 * @author Ben Dodson
	 * @since 3/22/05
	 * @version 3/22/05
	 *
	 **/
	function mediaBrowser($node,$showMainGrid) {
		global $show_artist_alpha, $show_album_alpha;
		// did they want to show all art?
		if (isset($_POST['action'])){
			if ($_POST['action'] == "viewallart"){
				$this->showAllArt($node);
				return;
			}
		}
		if (isset($_GET['action'])){
			if ($_GET['action'] == "viewallart"){
				$this->showAllArt($node);
				return;
			}
		}

		if ($show_artist_alpha == "true" && $node->getLevel() == 0 && distanceTo("artist") !== false) {
			$this->alphabeticalList($node, "artist");
			$this->blockSpacer();
		}
		if ($show_album_alpha == "true" && $node->getLevel() == 0 && distanceTo("album") !== false) {
			$this->alphabeticalList($node, "album");
			$this->blockSpacer();
		}
		if ($node->getLevel() > 0 || $showMainGrid  || isset($_GET['jz_letter'])) {
			$this->nodeGrid($node);
		}
	  
	}


	/**
	 * Displays the grid of nodes for a standard page.
	 *
	 * @author Ben Dodson, Ross Carlson
	 * @version 3/22/05
	 * @since 3/22/05
	 **/	
	function nodeGrid($node,$distance=false) {	 
		global $hierarchy; 
		$smarty = smartySetup();
		
		$display = new jzDisplay();
		$lvl = isset($_GET['jz_letter']) ? ($_GET['jz_level'] + $node->getLevel() - 1): $node->getLevel();
		switch ($hierarchy[$lvl]){
			case "genre":
				$pg_title = word("Genres");
			break;
			case "artist":
				$pg_title = word("Artists");
			break;
			case "album":
				$pg_title = word("Albums");
			break;
			default:
				$pg_title = word("Genres");
			break;
		}
		if (isset($_GET['jz_letter'])) {
			$retArray = $node->getAlphabetical($_GET['jz_letter'],"nodes",$_GET['jz_level']);
			$letter = $_GET['jz_letter'];
		} else {
			$retArray = $node->getSubNodes("nodes",$distance);
		}
		sortElements($retArray,"name");
		if ($node->getName() <> ""){
			$pg_title = $node->getName();
			if ($node->getSubNodeCount("nodes") > 0){
				$pg_title .= " (". $node->getSubNodeCount("nodes");
				if ($node->getSubNodeCount("tracks") > 0) {
					$pg_title .= "+";
				}
				$pg_title .= ")";
			}
		} else {
			if (count($retArray) <> 0){
				$pg_title .= " (". count($retArray). ")";
			}		
		}

		if ($display->startCache("nodeGrid",$node->getName(), $letter)){
			return;
		}
		
		$_SESSION['jz_node_distance'] = $distance;
		$smarty->assign('jz_bg_color', jz_bg_color);
		$smarty->assign('title', $pg_title);
		
		$smarty->display(SMARTY_ROOT. 'templates/slick/nodegrid.tpl');
		
		// Now lets finish out the cache
		$display->endCache();
		
		flushdisplay();
	}

		  

	/**
	* Displays the block that shows the A-Z Listing
	*
	* @author Ben Dodson, Ross Carlson
	* @since 3/23/05
	* @version 3/23/05
	*
	**/
	function alphabeticalList($node, $type, $padding = 3) {
		global $fe;
		
		$smarty = smartySetup();
		
		$urla = array();
		switch($type){
			case "artist":
				$title = word("Alphabetical Listing (Artists)");
				$urla['jz_level'] = distanceTo("artist",$node);
			break;
			case "album":
				$title = word("Alphabetical Listing (Albums)");
				$urla['jz_level'] = distanceTo("album",$node);
			break;
		}

		if ($node->getLevel() > 0) {
		  $urla['jz_path'] = $node->getPath("String");
		}

		$alpha_list = array();
		$urla['jz_letter'] = '#';
		$alpha_list['#'] = urlize($urla);

		for ($let = 'A'; $let < 'Z'; $let++) {
		  $urla['jz_letter'] = $let;
		  $alpha_list[$let] = urlize($urla);
		}
		$urla['jz_letter'] = 'Z';
		$alpha_list['Z'] = urlize($urla);

		$urla['jz_letter'] = "";
		$alpha_list[word('All')] = urlize($urla);


		$smarty->assign('title', $title);
		$smarty->assign('alpha_list', $alpha_list);
		$smarty->assign('jz_bg_color', jz_bg_color);
		
		$smarty->display(SMARTY_ROOT. 'templates/'. $fe->name . '/alphalist.tpl');
	}	
	
	/**
	 * Draws the block header, if the particular class wants one.
	 *
	 * @author Ben Dodson, Ross Carlson
	 * @since 3/1/05
	 * @version 3/1/05
	 *
	 **/
	function blockHeader($title = "", $right = "") {
	  return true;
	}
	
	/**
	 * Opens the block body.
	 *
	 * @author Ben Dodson, Ross Carlson
	 * @since 3/1/05
	 * @version 3/1/05
	 *
	 **/
	function blockBodyOpen($width = "100%") {
	  include(dirname(__FILE__). "/blocks/block-body-open.php");
	}

	/**
	 * Closes the block body.
	 *
	 * @author Ben Dodson, Ross Carlson
	 * @since 3/1/05
	 * @version 3/1/05
	 *
	 **/
	function blockBodyClose() {
	  include(dirname(__FILE__). "/blocks/block-body-close.php");
	}

	/**
	 * Draws the block spacer
	 *
	 * @author Ben Dodson, Ross Carlson
	 * @since 3/1/05
	 * @version 3/1/05
	 *
	 **/
	function blockSpacer() {
	  include(dirname(__FILE__). "/blocks/block-spacer.php");
	}

	/**
	* Check to see if the install directory exsists, and if so returns code to display the error
	* 
	* @author Ross Carlson
	* @version 2/25/05
	* @since 2/25/05
	* @param returns true if insecure, false if secure
	*/
	function checkForSecure(){
		global $include_path;
		
		// Let's see if the install dir is there
		if (is_dir($include_path. "install")){
			// Now let's give developers a break,
			// If CVS is there let's NOT error
			if (is_dir($include_path. "CVS")){
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
	
	/**
	* Displays the now breadcrumb block
	* 
	* @author Ross Carlson
	* @version 2/25/05
	* @since 2/25/05
	*/
	function breadCrumbs(){		
	  if (!defined('NO_AJAX_LINKS')) {
			$val  = '<div id="breadcrumbs"></div>';
			$val .= '<script>';
			$val .= 'function callBreadcrumbs() {';
			$val .= 'x_returnBreadcrumbs(callBreadcrumbs_cb)';
			$val .= '}';
			$val .= 'function callBreadcrumbs_cb(a) {';
			$val .= 'document.getElementById("breadcrumbs").innerHTML = a;';
			$val .= '}';
			$val .= 'callBreadcrumbs();';
			$val .= '</script>';
			
			return $val;
	  } else {
	    $blocks = new jzBlocks();
	    return $blocks->drawBreadcrumbs(true);
	  }
	}

	  /*
	   * Draws the breadcrumbs.
	   *
	   * @author Ross Carlson
	   * @since 8/6/05
	   *
	   **/
	  function drawBreadcrumbs($return = false) {
			global $img_up_arrow, $this_page;
										
			// Now let's create the breadcrumbs
			$bcArray = explode("/",$_SESSION['jz_path']);
			$node = new jzMediaNode($_SESSION['jz_path']);
			$display = new jzDisplay();
	
			$bcrumbs="";
			$bcrumbs = $img_up_arrow. " ";
			$bcrumbs .= '<a href="'. str_replace("ajax_request.php","index.php",$this_page). '">'. word("Home"). '</a>';
			$bcrumbs .= "&nbsp;";
			// Now we need to cut the last item off the list
			$bcArray = array_slice($bcArray,0,count($bcArray)-1);
			
			// Now let's display the crumbs
			$path = "";
			for ($i=0; $i < count($bcArray); $i++){
				$bcrumbs .= $img_up_arrow. "&nbsp;";
				$path .= $bcArray[$i] ."/";
				$curPath = substr($path,0,strlen($path)-1);
				
				$arr = array();
				$arr['jz_path'] = $curPath;
				
				$link = str_replace("ajax_request.php","index.php",urlize($arr));
				$bcrumbs .= '<a href="'. $link. '">'. $bcArray[$i]. '</a>';
				$bcrumbs .= "&nbsp;";
			}
			$mode = "GET";
			$bcrumbs .= '<form action="'. $this_page. '" method="'. $mode. '">';
			$bcrumbs .= '- <select style="width:100px" class="jz_select" name="'. jz_encode('jz_path'). '" onChange="form.submit();">';
			$parent = $node->getParent();
			$nodes = $parent->getSubNodes("nodes");
			foreach ($nodes as $child) {
				$path = $child->getPath("String");
				$bcrumbs .= '<option ';
				// Is this the current one?
				if ($child->getName() == $node->getName()){
					$bcrumbs .= ' selected ';
				}
				$bcrumbs .= 'value="'. htmlentities(jz_encode($path)). '">'. $display->returnShortName($child->getName(),20). '</option>';
			}
			$bcrumbs .= '</select>';
			$bcrumbs .= "</form>&nbsp;";
	
			if ($return) {
				return $bcrumbs;
			}
			echo $bcrumbs;
	  }

	
	/**
	* Displays the random playlist generator
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 2/20/05
	* @since 2/20/05
	*/
	function randomGenerateSelector($node,$header = false, $return = false){
		global $this_page, $random_play_amounts, $quick_list_truncate, 
		  $default_random_type, $default_random_count, $jzUSER;
		
		$root = new jzMediaNode();
		$display = new jzDisplay();

		// Now, can they stream?
		if (!$jzUSER->getSetting('stream')){
			return;
		}

		if ($return) {
		  ob_start();
		}
		
		if ($header) {
		  echo $header;
		} else {
		  echo '<font size="1">' . word("Randomize selected") . '</font><br>'; 
		}
		    ?>
		<form name="randomizer" action="<?php echo $this_page; ?>" method="post">
		<input type="hidden" name="<?php echo jz_encode("action"); ?>" value="<?php echo jz_encode("generateRandom"); ?>">
		<select name="<?php echo jz_encode("random_play_number"); ?>" class="jz_select">
		<?php
			$random_play = explode("|", $random_play_amounts);
			$ctr = 0;
			while (count($random_play) > $ctr){
				echo '<option value="'. jz_encode($random_play[$ctr]).'"';
				if ($random_play[$ctr] == $default_random_count) {
					echo " selected";
				}
				echo '>'. $random_play[$ctr].'</option>'. "\n";
				$ctr = $ctr + 1;
			}
			echo '</select> ';
			echo '<select name="' . jz_encode("random_play_type") . '" class="jz_select" style="width:60px;">';
			$random_play_types = "Songs|Albums|Artists";
			$random_play = explode("|", $random_play_types);
			$ctr = 0;
			while (count($random_play) > $ctr){
				// Let's make sure this isn't blank
				if ($random_play[$ctr] <> ""){
					echo '<option value="'. jz_encode($random_play[$ctr]).'"';
					if ($random_play[$ctr] == $default_random_type) {
						echo " selected";
					}
					echo '>'. $random_play[$ctr]. '</option>'. "\n";
				}
				$ctr++;
			}
			echo '</select>';
			// Now let's let them pick a genre				
			if (distanceTo("genre") !== false){
				$curgenre = getInformation($node,'genre');
				echo ' <select name="' . jz_encode("random_play_genre") .'" class="jz_select" style="width:75px;">';
				if ($curgenre === false) 			
					echo '<option value="' . jz_encode("") . '" selected>All Genres</option>'. "\n";
				else echo '<option value="' . jz_encode("") . '">All Genres</option>'. "\n";
				if (!isset($genreArray)) {
					// todo: use the genre array from the other dropdown
					// so we don't query twice.
					$genreArray = $root->getSubNodes("nodes",distanceTo("genre"));
				}
				for ($ctr=0; $ctr < count($genreArray); $ctr++){
					if ($genreArray[$ctr] <> ""){	
						$title = returnItemShortName($genreArray[$ctr]->getName(),$quick_list_truncate);
						echo '<option ';
						// Now let's see if this is the genre we're looking at
						if (!isset($genre)){$genre="";}
						if (strtolower($title) == strtolower($genre)){ echo 'selected'; }
						echo ' value="'. jz_encode(htmlentities($genreArray[$ctr]->getPath("String"))). '"';
						if ($curgenre == $genreArray[$ctr]->getName())
							echo ' selected';
						echo '>'. $title. '</option>'. "\n";
					}
				}
				echo '</select>';
			}
			echo ' <input class="jz_submit" type="submit" name="submit_random" value="'. word("Go"). '"';
			if (!defined('NO_AJAX_JUKEBOX')) {
			  echo ' onClick="return submitPlaybackForm(this,\'' . htmlentities($this_page)  . '\')"';
			} else if (checkPlayback() == "embedded") {
			  echo ' onClick="' . $display->embeddedFormHandler('randomizer') . '"';
			}
			echo '>';
			echo '</form>';
			echo '</nobr>';

			if ($return) {
			  $var = ob_get_contents();
			  ob_end_clean();
			  return $var;
			}

	}

	/**
	 * Draws the opening of the small rounded inner blocks
	 * 
	 * @author Ross Carlson
	 * @version 01/21/05
	 * @since 01/21/05
	 */
	function openInnerBlock(){
  ?>
 	<table width="100%" cellpadding="3" cellspacing="0" border="0">
    <tr>
    <td width="6" height="6" class="jz_left_iblock_topl"></td>
    <td width="99%" height="6" class="jz_left_iblock_topm"></td>
    <td width="6" height="6" class="jz_left_iblock_topr"></td>
    </tr>
    <tr>
    <td width="6" class="jz_left_iblock_left"></td>
    <td width="99%" class="jz_left_iblock_inner">
    <?php
    }
	
	/**
	 * Draws the closing of the small rounded inner blocks
	 * 
	 * @author Ross Carlson
	 * @version 01/21/05
	 * @since 01/21/05
	 */
	function closeInnerBlock(){
  ?>
 </td>
     <td width="6" class="jz_left_iblock_right"></td>
     </tr>
     <tr>
     <td width="6" height="6" class="jz_left_iblock_botl"></td>
     <td width="99%" height="6" class="jz_left_iblock_botm"></td>
     <td width="6" height="6" class="jz_left_iblock_botr"></td>
     </tr>
     </table>
     <?php
     }


	
	/**
	* Draws the playlist bar
	* 
	* @author Ross Carlson
	* @version 01/11/05
	* @since 01/11/05
	*/
	function playlistBar(){
		global $img_check, $img_check_none, $root_dir, $jzUSER;

		if (!$jzUSER->getSetting('stream')){
			return;
		}
		
		$smarty = smartySetup();		
		$smarty->assign('img_check', $img_check);
		$smarty->assign('img_check_none', $img_check_none);		
		$smarty->display(SMARTY_ROOT. 'templates/general/playlist-bar.tpl');
		
		return;
		
		$display = new jzDisplay();
		$this->openInnerBlock();
		?>
		<table width="95%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="50%" valign="middle">
					<nobr>
					<a style="cursor:pointer" onClick="CheckBoxes('albumForm',true); return false;" href="javascript:;"><?php echo $img_check; ?></a>
					<a style="cursor:pointer" onClick="CheckBoxes('albumForm',false); return false;" href="javascript:;"><?php echo $img_check_none; ?></a>
					<?php
						$display->sendListButton();
						echo "&nbsp;";
						$display->sendListButton(true);
					?>
					</nobr>
				</td>
				<td width="50%" valign="middle" align="right">
					<nobr>
					&nbsp; &nbsp;
					<?php
						$display->addListButton(); 
						echo "&nbsp;";
						$display->playlistSelect(115,false,"all");
					?>
					</nobr>
				</td>
			</tr>
		</table>
		<?php
	  $this->closeInnerBlock();
	}
	
	/**
	* Shows the chart system
	* 
	* @author Ross Carlson
	* @version 01/26/05
	* @since 01/26/05
	* @param $node The node we are viewing so we can filter
	* @param $types The charts to display. Comma seperated list of:
	* topplayalbum, topplayartist, topdownalbum, newalbums, newartists, newtracks
	* recentplayalbum, recentplayartist, recentplaytrack,
	* topratedalbum, topratedartist, topviewartist, topplaytrack
	* @param $numItems The number of items we want to return (defaults to 5)
	* @param $format Should we format this or return raw data (defaults to true)
	* 
	*/
	function showCharts($node,$types=false, $numItems=false, $format=true, $vertAlign = false){
	  global $album_name_truncate, $img_tiny_play, $img_tiny_play_dis, $jzUSER, $img_rss, $root_dir,$advanced_tooltips,$rss_in_charts,$num_items_in_charts,$chart_timeout_days,$chart_types; 
		$be = new jzBackend();
		if ($be->hasFeature('charts') === false) {
		  return;
		}
		// Let's setup our objects
		$blocks = new jzBlocks();
		$display = new jzDisplay();
		
		// Now let's do a loop creating all our blocks
		if ($types === false || $types == "") {
		  if (isset($chart_types) && !isNothing($chart_types)) {
		    $b = $chart_types;
		  } else {
		    $b = "topplayalbum,topplayartist,topviewartist,newalbums";
		  }
		} else {
		  $b = $types;
		}
		$bArray = explode(",",$b);
		for ($e=0;$e<count($bArray);$e++){
			// Now let's create our blocks
			$showPlays = false;
			switch ($bArray[$e]){
				case "topplayalbum":
					$func = "getMostPlayed";
					$arr['action'] = "popup";
					$arr['ptype'] = "topstuff";
					$arr['tptype'] = "played-albums";
					$title = word("Top Played Albums");
					$distance = "album";
					$showPlays = true;
					$showDownload = false;
					$rss = "most-played";
				break;
				case "topplayartist":
					$func = "getMostPlayed";
					$arr['action'] = "popup";
					$arr['ptype'] = "topstuff";
					$arr['tptype'] = "played-artists";
					$title = word("Top Played Artists");
					$distance = "artist";
					$showPlays = true;
					$showDownload = false;
					$rss = "most-played-artist";
				break;
				case "topdownalbum":
					$func = "getMostDownloaded";
					$arr['action'] = "popup";
					$arr['ptype'] = "topstuff";
					$arr['tptype'] = "downloaded-albums";
					$title = word("Top Downloaded Albums");
					$distance = "album";
					$showDownload = true;
					$rss = "most-downloaded";
				break;
				case "newalbums":	
					$func = "getRecentlyAdded";
					$arr['action'] = "popup";
					$arr['ptype'] = "topstuff";
					$arr['tptype'] = "new-albums";
					$title = word("New Albums");
					$distance = "album";
					$showDownload = false;
					$rss = "last-added";
				break;
			        case "newartists":	
					$func = "getRecentlyAdded";
					$arr['action'] = "popup";
					$arr['ptype'] = "topstuff";
					$arr['tptype'] = "new-artists";
					$title = word("New Artists");
					$distance = "artist";
					$showDownload = false;
					$rss = "last-added-artists";
				break;
			        case "newtracks":	
					$func = "getRecentlyAdded";
					$arr['action'] = "popup";
					$arr['ptype'] = "topstuff";
					$arr['tptype'] = "new-tracks";
					$title = word("New Tracks");
					$distance = "track";
					$showDownload = false;
					$rss = "last-added-tracks";
				break;

			case "recentplaytrack":	
			  $func = "getRecentlyPlayed";
			  $arr['action'] = "popup";
			  $arr['ptype'] = "topstuff";
			  $arr['tptype'] = "recentplayed-tracks";
			  $title = word("Recently Played Tracks");
			  $distance = "track";
			  $showDownload = false;
			  $rss = "recentplayed-track";
			  break;
			case "topplaytrack":	
			  $func = "getMostPlayed";
			  $arr['action'] = "popup";
			  $arr['ptype'] = "topstuff";
			  $arr['tptype'] = "played-tracks";
			  $title = word("Top Played Tracks");
			  $distance = "track";
			  $showDownload = false;
			  $rss = "most-played-tracks";
			  break;
			case "recentplayalbum":	
			  $func = "getRecentlyPlayed";
			  $arr['action'] = "popup";
			  $arr['ptype'] = "topstuff";
			  $arr['tptype'] = "recentplayed-albums";
			  $title = word("Recently Played Albums");
			  $distance = "album";
			  $showDownload = false;
			  $rss = "recentplayed-album";
			  break;
			case "recentplayartist":	
			  $func = "getRecentlyPlayed";
			  $arr['action'] = "popup";
			  $arr['ptype'] = "topstuff";
			  $arr['tptype'] = "recentplayed-artists";
			  $title = word("Recently Played Artists");
			  $distance = "artist";
			  $showDownload = false;
			  $rss = "recentplayed-artist";
			  break;
			case "topratedalbum":	
			  $func = "getTopRated";
			  $arr['action'] = "popup";
			  $arr['ptype'] = "topstuff";
			  $arr['tptype'] = "toprated-albums";
			  $title = word("Top Rated Albums");
			  $distance = "album";
			  $showDownload = false;
			  $rss = "toprated-album";
			  break;
			case "topratedartist":	
			  $func = "getTopRated";
			  $arr['action'] = "popup";
			  $arr['ptype'] = "topstuff";
			  $arr['tptype'] = "toprated-artists";
			  $title = word("Top Rated Artists");
			  $distance = "artist";
			  $showDownload = false;
			  $rss = "toprated-artist";
			  break;
			case "topviewartist":	
			  $func = "getMostViewed";
			  $arr['action'] = "popup";
			  $arr['ptype'] = "topstuff";
			  $arr['tptype'] = "topviewed-artists";
			  $title = word("Most Viewed Artists");
			  $distance = "artist";
			  $showDownload = false;
			  $rss = "topviewed-artist";
			  break;
			default:
			  continue;
			}
			
			// Now let's get the data
			if ($distance == "track") {
			  $returnType = "tracks";
			} else {
			  $returnType = "nodes";
			}
			if ($numItems){
				$num_items_in_charts = $numItems;
			}

			if ($chart_timeout_days > 0) {
			  $be = new jzBackend();
			  $data_id .= pathize('chart-'.$node->getPath("String")) . "-${func}-${distance}-${num_items_in_charts}";
			  if (($recent = $be->loadData($data_id,true)) === false) {
			    $recent = $node->$func($returnType,distanceTo($distance,$node),$num_items_in_charts);
			    $be->storeData($data_id,$recent,$chart_timeout_days);
			  }
			} else {
			  $recent = $node->$func($returnType,distanceTo($distance,$node),$num_items_in_charts);
			}
						
			// Now let's see if we got data
			if (count($recent) == 0){continue;}
			?>
			<td width="25%" valign="top">
				<?php 
					if ($format){
						echo '<div id="slickMainBlockBody">';
					}
				?>
				<table width="95%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td width="100%" valign="middle">
							<nobr>
							<?php
								if ($format){
									// Now let's display the link to the FULL top played list
									echo '<a onclick="openPopup(this, 300, 450); return false;" title="'. $title. '" href="'. urlize($arr). '"><strong>'. $title. '</strong></a>';
									// Let's link to the RSS feed
									if ($rss_in_charts == "true"){
										echo ' - <a href="'. $root_dir. '/rss.php?type='. $rss;
										if ($node->getLevel() != 0) {
										  echo '&root='.$node->getPath("String");
										}
										echo '">'. $img_rss. '</a>';
									}
									echo '<br>';
								}											

								// Now let's loop through the results
								for ($i=0;$i<count($recent);$i++){
									// Now let's create our node and get the properties
									$item = $artnode = $recent[$i];
									$album = $item->getName();
									$parent = $item->getAncestor("artist");
									if ($parent !== false) {
									  $artist = $parent->getName();
									}
									$albumDLCount = $item->getDownloadCount();
									$year = $item->getYear();
									$dispYear = "";
									if ($year <> "-"){
										$dispYear = " (". $year. ")";
									}
									
									// Now let's create our links
									if ($distance == "track") {
									  $artnode = $item->getAncestor("album");
									  $albumArr['jz_path'] = $artnode->getPath("String");
									  $gp = $item->getAncestor("artist");
									  $artistArr['jz_path'] = $gp->getPath("String");
									} else {
									  $albumArr['jz_path'] = $item->getPath("String");
									  if ($parent !== false) {
									    $artistArr['jz_path'] = $parent->getPath("String");
									  }
									}
									
									// Now let's create our short names
									$artistTitle = returnItemShortName($artist,$album_name_truncate);
									$albumTitle = returnItemShortName($album,$album_name_truncate);													
									
									// Now let's display it
									echo "<nobr>";
									if (!$jzUSER->getSetting('stream')){
										echo $img_tiny_play_dis;
									} else {
										$display->playLink($item, $img_tiny_play, $album);
									}
									
									// Ok, did they want advanced tooltips?
									if ($advanced_tooltips == "true"){
										// Now let's set the hover code
										$innerOver = "";
										$showTip = false;
										if (($art = $artnode->getMainArt("75x75")) <> false) {
											$innerOver .= $display->returnImage($art,$artnode->getName(),75,75,"limit",false,false,"left","3","3");
											$showTip = true;
											$bTitle = $artist. " - ". $album. $dispYear;
										} else {
											// Ok, no art so let's make this look better
											$innerOver .= "<strong>". $artist. "<br>". $album. $dispYear. "</strong><br>";
											$bTitle = $album. $dispYear;
										}
										$desc_truncate = 200;
										$desc = $item->getDescription();
										if (!isNothing($desc)) {
										  $innerOver .= $display->returnShortName($desc,$desc_truncate);
										  $showTip = true;
										}

										// Now let's fix up
										$innerOver = str_replace("'","",str_replace('"',"",$innerOver));
										$bTitle = str_replace("'","",str_replace('"',"",$bTitle));		
										
										if ($showTip) {
										  $title = $display->returnToolTip($innerOver, $bTitle);
										} else {
										  $title = ' title="'. $artist. ' - '. $album. $dispYear. '"';
										}

									} else {
										// Standard tooltips
										$title = ' title="'. $artist. ' - '. $album. $dispYear. '"';
									}
									echo ' <a '. $title. ' href="'. urlize($albumArr). '">'. $albumTitle;
									if ($showPlays){
									  if ($bArray[$e] == "topplayalbum") {
									    $albumPlayCount = $item->getSubNodeCount('tracks',-1);
									    if ($albumPlayCount > 0) {
									      $albumPlayCount = ceil($item->getPlayCount() / $albumPlayCount);
									    }
									  } else {
									    $albumPlayCount = $item->getPlayCount();
									  }
										echo ' ('. $albumPlayCount. ')';
									} 
									if ($showDownload){
										echo ' ('. $albumDLCount. ')';
									} 
									echo "</a><br>";
									echo "</nobr>";
								}
							?>
							</nobr>
						</td>
					</tr>
				</table>
				<?php 
					if ($format){
						echo '</div>';
					}
				?>
			</td>
			    <?php if ($vertAlign) { echo '</tr><tr>'; } ?>
		<?php	
		}
	}
	
	/**
	* Shows the site news
	* 
	* @author Ross Carlson
	* @version 01/26/05
	* @since 01/26/05
	* @param $node the Node we are viewing
	*/
	function siteNews($node){
		// Let's setup our objects
		$be = new jzBackend();
		
		if (!is_object($node)){return;}
		
		if ($node->getName() == ""){
			$news = "site-news";
		} else {
			$news = $node->getName(). "-news";
		}
		$news = $be->loadData($news);
		if ($news == ""){return;}
					
		// Now let's display the news
		return $news;
	}
	
	/**
	* Draws the Jinzora Radio Block using the data from the current node
	* 
	* @author Ross Carlson
	* @version 01/11/05
	* @since 01/11/05
	* @param object $node The object to create the radio from
	*/
	function radioBlock($node){
		global $img_play, $cms_mode;

		$display = new jzDisplay();

		$el = $node->getAncestor("artist");
		if ($el === false) {
		  return;
		}
		?>
		<nobr>
		<?php
		if ($cms_mode == "false"){
			echo '<span class="jz_artistDesc">';
		}
		 
		$display->randomPlayButton($el, 50, $img_play);
		echo " <strong>". $el->getName(). "</strong><br>";
	  
		
		$value = $display->radioPlayButton($el, 50); 
		if (!$value){return;}
		
		?>
		<strong><?php echo word("Similar Artists"); ?></strong>
		<?php
		if ($cms_mode == "false"){
			echo '</span>';
		}
		?>
		
		</nobr>
		<?php
	}
	
	/**
	* Draws the Jinzora similar artist block
	* 
	* @author Ross Carlson
	* @version 01/11/05
	* @since 01/11/05
	* @param string $artist The artist that we are getting similar artist data from
	* @param string $onlyMatches Should we only display artists that are actually in your collection
	* @param bolean $limit Should we limit how many results we get back
	*/
	function similarArtistBlock($artist, $onlyMatches = false, $limit = false){
		global $jzSERVICES, $album_name_truncate, $img_tiny_play, $jzUSER, $img_tiny_play_dis, $cms_mode;

		$ptype = $artist->getPType();			
		
		$display = new jzDisplay();
		$artist = $artist->getAncestor("artist");

		// Ok, now we need to search Echocloud to get matches to this artist
		$simArray = $jzSERVICES->getSimilar($artist);
		$simArray = seperateSimilar($simArray);
		
		if (($onlyMatches === true && sizeof($simArray['matches']) == 0) ||
		    (sizeof($simArray['matches']) == 0 && sizeof($simArray['nonmatches']) == 0)) {
		      return;
		    }

		// Let's setup Smarty
		$smarty = smartySetup();
		//$smarty->assign('viewAll', $viewAll);
		$smarty->assign('title', word("Similar Artists"));
		$_SESSION['sim_limit'] = $limit;
		$_SESSION['sim_onlyMatches'] = $onlyMatches;
		
		// Now let's include the right template
		if ($ptype == "artist"){
			$smarty->display(SMARTY_ROOT. 'templates/general/similar-artist.tpl');
		} else if ($ptype == "album"){
			$smarty->display(SMARTY_ROOT. 'templates/general/similar-artist-album.tpl');
		}
	}
	
	/**
	* Draws the Jinzora similar albums block
	* 
	* @author Ross Carlson
	* @version 01/11/05
	* @since 01/11/05
	* @param string $artist The artist that we are getting similar artist data from
	* @param bolean $limit Should we limit how many results we get back
	*/
	function similarAlbumBlock($element, $limit = false){
		global $album_name_truncate, $jzSERVICES, $img_tiny_play, $img_tiny_play_dis, $jzUSER, $cms_mode;
		
		$display = new jzDisplay();
		$element = $element->getAncestor("artist");
		
		if ($element === false) { return; }
		// Ok, now we need to search Echocloud to get matches to this artist
		$simArray = $jzSERVICES->getSimilar($element);
		$simArray = seperateSimilar($simArray);
		if (sizeOf($simArray['matches']) == 0) { return; }

		// Let's setup Smarty
		$smarty = smartySetup();

		$smarty->assign('title', word("Similar Albums"));
		$_SESSION['sim_limit'] = $limit;

		$smarty->display(SMARTY_ROOT. 'templates/general/similar-album.tpl');
	}
	
	/**
	* Draws the block that displays all tracks from an artist on the artist page
	* 
	* @author Ross Carlson
	* @version 01/13/05
	* @since 01/13/05
	* @param $node The node of the item we are viewing
	*/
	function displayAllTracks($node){		
		?>
		<table width="100%" cellpadding="2" cellspacing="0" border="0">
			<tr>
				<td width="100%">
					<?php
						$tracks = $node->getSubNodes("tracks",-1,true);
						$this->trackTable($tracks, "sample-all");
					?>
				</td>
			</tr>
		</table>
		<?php
	}
	
	/**
	* Draws the block that displays a random sampling of tracks from an artist
	* 
	* @author Ross Carlson
	* @version 01/13/05
	* @since 01/13/05
	* @param $node The node of the item we are viewing
	*/
	function displaySampler($node){	
		$tracks = $node->getSubNodes("tracks",-1,true,4);
		if (count($tracks) > 0){
			?>
			<table width="100%" cellpadding="2" cellspacing="0" border="0">
				<tr>
					<td width="100%">
						<?php
							$this->trackTable($tracks, "sample");
						?>
					</td>
				</tr>
			</table>
			<?php
		}
	}

	/**
	 * Creates a small version of the jukebox block.
	 *
	 * @author Ben Dodson
	 * @since 4/29/05
	 * @version 4/29/05
	 * @param text: the text to display in the box. 'off' means no text.
         * @buttons: one of: "top|default|off". Top means toggle with the header text when applicable.
	 **/
	function smallJukebox($text = false, $buttons="default", $linebreaks=true) {
	  global $jbArr,$jzUSER,$include_path, $jukebox_display;

	  if ($text == "") {
	    $text = false;
	  }

	  $display = new jzDisplay();
	  include_once($include_path. "jukebox/class.php");

	  $jb = new jzJukebox();
	  if (!$jb->connect()) {
	    echo '<strong>Error connecting to jukebox. Please make sure your jukebox settings are correct. (jukebox/settings.php)</strong>';
	    $jb_playwhere = "";
	  } else if (isset($_SESSION['jb_playwhere'])){
	    $jb_playwhere = $_SESSION['jb_playwhere'];
	  } else {
	    $jb_playwhere = "stream";
	  }
	  
	  $url_array = array();
	  $url_array['action'] = "popup";
	  $url_array['ptype'] = "jukezora";

		?>
<script>
   sm_text = '<?php echo $text; ?>';
   sm_buttons = '<?php echo $buttons; ?>';
   sm_linebreaks = '<?php echo $linebreaks; ?>';
</script>
		<table width="100%" cellpadding="2" cellspacing="0" border="0">
			<tr>
				<td width="100%" valign="top">
                 <?php
  		                $showText = true;

				if ($buttons == "top" && checkPermission($jzUSER,"jukebox_admin") === true && $_SESSION['jb_playwhere'] != "stream"){
				     // Ok, now we need to make sure we can do things
				      $func = $jb->jbAbilities();
					  echo "<nobr>";
				      if ($func['playbutton']){
						$display->displayJukeboxButton("play");
						$showText = false;
				      }
				      if ($func['pausebutton']){
						$display->displayJukeboxButton("pause");
						$showText = false;
				      }
				      if ($func['stopbutton']){
						$display->displayJukeboxButton("stop");
						$showText = false;
				      }
				      if ($func['nextbutton']){
						$display->displayJukeboxButton("previous");
						$showText = false;
				      }
				      if ($func['prevbutton']){
						$display->displayJukeboxButton("next");
						$showText = false;
				      }
				      if ($func['shufflebutton']){
						//$display->displayJukeboxButton("random_play");
						//$showText = false;
				      }
				      if ($func['clearbutton']){
						$display->displayJukeboxButton("clear");
						$showText = false;
				      }
					  echo "</nobr>";
				}
 	                        if ($showText) {
					?>
                    <?php if (isNothing($text)) { ?>	
					<font size="1">
						<strong>
							<?php 
					                if (checkPlayback() == "jukebox") {
 					                  $theJWord = word("Jukebox");
					                } else {
					                  $theJWord = word("Playback");
					                }
 					                        $display->popupLink("jukezora",$theJWord);
								if (checkPlayback() == "jukebox") {
								  $jz_jbstatus = $jb->getPlayerStatus();
								  echo " - ". ucwords($jz_jbstatus);
								}
								
							?>
                                                 </strong>
					</font>
					<?php } else if ($text != "off") { echo $text; } ?>
					<?php				
					} ?>
				</td>
			</tr>
			<tr>
				<td width="100%" valign="top">
					<?php
						$arr = array();
						$arr['action'] = "jukebox";
						$arr['subaction'] = "jukebox-command";
						$arr['command'] = "playwhere";
					?>
					<form action="<?php echo urlize($arr); ?>" method="POST" name="playbackForm">
						<select name="jbplaywhere" id="smallJukeboxSelect" class="jz_select" style="width:132;" onChange="updateSmallJukebox()">
					   <?php if (checkPermission($jzUSER,'stream')) { ?>
							<option value="stream">Stream</option>
							<?php
					   }
								// Now let's get a list of all the jukeboxes that are installed
								for ($i=0; $i < count($jbArr); $i++){
								  echo '<option ';
								  if ($jb_playwhere == $jbArr[$i]['description']){ echo " selected "; }
								  echo 'value="'. $jbArr[$i]['description']. '">'. $jbArr[$i]['description']. '</option>';
								}
							  ?>
						</select>
					</form>
					<?php
						if ($linebreaks){ echo '</td></tr><tr><td width="100%">'; } else { echo " &nbsp "; }
						if ($jb_playwhere <> "stream" && checkPermission($jzUSER,"jukebox_admin") === true && $buttons == "default"){
						
						// Ok, now we need to make sure we can do things
					 	$func = $jb->jbAbilities();
						  
						  echo "<nobr>";
						  if ($func['playbutton']){
							$display->displayJukeboxButton("play");
						  }
						  if ($func['pausebutton']){
							$display->displayJukeboxButton("pause");
						  }
						  if ($func['stopbutton']){
							$display->displayJukeboxButton("stop");
						  }
						  if ($func['nextbutton']){
							$display->displayJukeboxButton("previous");
						  }
						  if ($func['prevbutton']){
							$display->displayJukeboxButton("next");
						  }
						  if ($func['shufflebutton']){
							//$display->displayJukeboxButton("random_play");
						  }
						  if ($func['clearbutton']){
							$display->displayJukeboxButton("clear");
						  }
						  echo "</nobr>";
						}
					  ?>
				</td>
			</tr>
		</table>
		<?php
	}
  
	/**
	* Displays the Jukebox Block
	* 
	* @author Ben Dodson
	* @version 12/22/04
	* @since 12/22/04
	*/
	function jukeboxBlock(){
		global $this_page, $media_dirs, $jbArr, $root_dir,$include_path,$jzUSER;

		$display = new jzDisplay();
		include_once($include_path. "jukebox/class.php");
		
		// let's default to stream
		if (!isset($_SESSION['jb_playwhere'])) {
		  if (checkPermission($jzUSER,"stream")) {
		    $_SESSION['jb_playwhere'] = "stream";
		  } else {
		    $_SESSION['jb_playwhere'] = $jbArr[0]['description'];
		  }
		}
		$jb_playwhere = $_SESSION['jb_playwhere'];
		
		?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jz_block_td" height="100%">
			<tr>
				<td width="5%" valign="top" height="100%">
					<nobr>
					<?php
						// Now let's create our Jukebox class and connect to it to make sure it works
						$jb = new jzJukebox();
						if (!$jb->connect()){
							echo "We had a problem connecting to the player, sorry this is a fatal error!<br><br>";
							echo "Player Settings:<br>";
							for ($i=0; $i < count($jbArr); $i++){
								if ($jbArr[$i]['description'] == $_SESSION['jb_playwhere']){
									foreach ($jbArr[$i] as $setting=>$value) {
										echo $setting. " - ". $value."<br>";
									}
								}
							}
							echo "<br>Please check these with your player's settings";
							echo "<br>";
							?>
							Playback to:<br>
							<?php
								$arr = array();
								$arr['action'] = "jukebox";
								$arr['subaction'] = "jukebox-command";
								$arr['command'] = "playwhere";
							?>
							<form action="<?php echo urlize($arr); ?>" method="post">
								<select name="jbplaywhere" class="jz_select" id="jukeboxSelect" style="width:142;" onChange="updateJukebox(true); return false;">
							   <?php if (checkPermission($jzUSER,'stream')) { ?>
									<option <?php if ($jb_playwhere == "stream"){ echo " selected "; } ?>value="stream">Stream</option>
									<?php
							   }
										// Now let's get a list of all the jukeboxes that are installed
										for ($i=0; $i < count($jbArr); $i++){
											echo '<option ';
											if ($jb_playwhere == $jbArr[$i]['description']){ echo " selected "; }
											echo 'value="'. $jbArr[$i]['description']. '">'. $jbArr[$i]['description']. '</option>';
										}
									?>
								</select>
							</form></nobr></td></tr></table>
							<?php
							return;
						}
						
						// Let's figure out where they are playing
						if (isset($_SESSION['jb_playwhere'])){
							$jb_playwhere = $_SESSION['jb_playwhere'];
						} else {
							$jb_playwhere = "";
						}
					
						$remain = $jb->getCurrentTrackRemaining();
						$jz_jbstatus = $jb->getPlayerStatus();
						if ($jz_jbstatus <> "playing"){
							$remain = 0;
						}
						if ($remain == 1){$remain = 0; }
						if ($remain > 1){$remain = $remain - 1;}
						
						if ($jb_playwhere <> "stream" && checkPermission($jzUSER,"jukebox_admin")){
							// Ok, now we need to make sure we can do things
							$func = $jb->jbAbilities();
							
							if ($func['playbutton']){
								$display->displayJukeboxButton("play");
							}
							if ($func['pausebutton']){
								$display->displayJukeboxButton("pause");
							}
							if ($func['stopbutton']){
								$display->displayJukeboxButton("stop");
							}
							if ($func['nextbutton']){
								$display->displayJukeboxButton("previous");
							}
							if ($func['prevbutton']){
								$display->displayJukeboxButton("next");
							}
							if ($func['shufflebutton']){
								$display->displayJukeboxButton("random_play");
							}
							if ($func['clearbutton']){
								$display->displayJukeboxButton("clear");
							}
							/*
							if ($func['repeatbutton']) {
							  $status = $jb->getPlayerStatus("repeat");
							  if ($status) {
							    $display->displayJukeboxButton("no_repeat");
							  } else {
							    $display->displayJukeboxButton("repeat");
							  }
							}
							*/
							echo '<br><br>';
							if ($func['status']){
								echo 'Status: ';
								echo ucwords($jz_jbstatus);
								echo '<br>';
							}
							if ($func['stats']){
								$jb->returnJBStats();
								echo '<br>';
							}
							$on = false;
							if ($func['progress'] and $on){
								?>
								Progress:
								<span id="timer">&nbsp;<br></span><br>
								<?php
							}

							if ($func['volume']){
								$arr = array();
								$arr['action'] = "jukebox";
								$arr['subaction'] = "jukebox-command";
								$arr['command'] = "volume";
								?>
								<form action="<?php echo urlize($arr); ?>" method="post">
									<input type="hidden" name="action" value="jukebox">
									<input type="hidden" name="subaction" value="jukebox-command">
									<input type="hidden" name="command" value="volume">
									<select name="jbvol" id="jukeboxVolumeSelect" class="jz_select" style="width:142;" onChange="sendJukeboxVol(); return false">
										<?php
											$vol = "";
											if (isset($_SESSION['jz_jbvol-'. $_SESSION['jb_id']])){
												$vol = $_SESSION['jz_jbvol-'. $_SESSION['jb_id']];
											}
											
											$c=100;
											while($c > 0){
												echo '<option ';
												if ($c == $vol){ echo ' selected '; }
												echo 'value="'. $c. '">Volume '. $c. '%</option>';
												$c = $c-10;
											}
										?>
										<option value="0">Mute</option>
									</select>
								</form>
								<br>
								<?php
							}

						// This closes our if to see if we are streaming or not
						}
						echo 'Playback to:<br>';
						$arr = array();
						$arr['action'] = "jukebox";
						$arr['subaction'] = "jukebox-command";
						$arr['command'] = "playwhere";
						?>
						<form action="<?php echo urlize($arr); ?>" method="post">
							<select name="jbplaywhere" class="jz_select" id="jukeboxSelect" style="width:142;" onChange="updateJukebox(true); return false;">
						   <?php if (checkPermission($jzUSER,'stream')) { ?>
								<option <?php if ($jb_playwhere == "stream"){ echo " selected "; } ?>value="stream">Stream</option>
								<?php
						   }
									// Now let's get a list of all the jukeboxes that are installed
									for ($i=0; $i < count($jbArr); $i++){
										echo '<option ';
										if ($jb_playwhere == $jbArr[$i]['description']){ echo " selected "; }
										echo 'value="'. $jbArr[$i]['description']. '">'. $jbArr[$i]['description']. '</option>';
									}
								?>
							</select>
						</form>
						<?php
						if ($jb_playwhere <> "stream" and $func['addtype']){
							echo '<br>';
							echo 'Add type:<br>';
							// Now let's set the add type IF it hasn't been set
							if (!isset($_SESSION['jb-addtype'])){
								$_SESSION['jb-addtype'] = "current";
							}

							$arr = array();
							$arr['action'] = "jukebox";
							$arr['subaction'] = "jukebox-command";
							$arr['command'] = "addwhere";
						?>
						<form action="<?php echo urlize($arr); ?>" method="post">
							<input type="hidden" name="action" value="jukebox">
							<input type="hidden" name="subaction" value="jukebox-command">
							<input type="hidden" name="command" value="addwhere">
							<select name="addplat" class="jz_select" id="jukeboxAddTypeSelect" style="width:142;" onChange="sendJukeboxAddType(); return false;">
								<option <?php if ($_SESSION['jb-addtype'] == "current"){echo " selected ";} ?> value="current">At Current</option>
								<option <?php if ($_SESSION['jb-addtype'] == "end"){echo " selected ";} ?>value="end">At End</option>
								<option <?php if ($_SESSION['jb-addtype'] == "begin"){echo " selected ";} ?>value="begin">At Beginning</option>
								<option <?php if ($_SESSION['jb-addtype'] == "replace"){echo " selected ";} ?>value="replace">Replace</option>
							</select>
					</form>
					</nobr>
					<?php
					}
					?>
				</td>
				<td width="5%" valign="top">
					<?php
						// Let's make sure they aren't streaming
						if ($jb_playwhere == "stream"){ echo '</td></tr></table>'; return; }
					?>
					<?php
						if ($func['nowplaying']){
							$curTrack = $jb->getCurrentTrackName();
							$fullname = $curTrack;
							$curTrack = $display->returnShortName($curTrack,25);
						?>
						<?php echo word("Now Playing:"). ' <a href="javascript:;" title="'. $fullname. '">'. $curTrack. "</a><br>"; ?>
						<!--
						<span ID="CurTicker" STYLE="overflow:hidden; width:275px;"  onmouseover="CurTicker_PAUSED=true" onmouseout="CurTicker_PAUSED=false">
							
						</span>
						-->
						<?php
							if ($func['nexttrack']){
								$fullList = $jb->getCurrentPlaylist();
								if ($fullList != array()) {
									$nextTrack = $fullList[getCurPlayingTrack()+1];
									$fullname = $nextTrack;
									if (stristr($nextTrack,"/")){
										$nArr = explode("/",$nextTrack);
										$nextTrack = $nArr[count($nArr)-1];
									}
									$nextTrack = str_replace(".mp3","",$nextTrack);
									$nextTrack = $display->returnShortName($nextTrack,30);
								}
								?>
								<?php echo word("Next Track:"). ' <a href="javascript:;" title="'. $fullname. '">'. $nextTrack. "</a><br>"; ?>
								<!--
								<DIV ID="NextTicker" STYLE="overflow:hidden; width:275px;"  onmouseover="NextTicker_PAUSED=true" onmouseout="NextTicker_PAUSED=false">
									
								</DIV>
								-->
								<?php
							}
						?>
						<script language="javascript" src="<?php echo $root_dir; ?>/jukebox/ticker.js"></script>
					<?php
						}

						if ($func['fullplaylist']){
						  if (!is_array($fullList)) {
						    $fullList = $jb->getCurrentPlaylist();
						  }
					?>
					
						Complete Playlist
						<?php
							// Did they need any addon tools
							$jb->getAddOnTools();
						?>
						<br>
						<?php
							// Now let's get the full playlist back
							$curTrackNum = $jb->getCurrentPlayingTrack();
						?>
						<?php
							$arr = array();
							$arr['action'] = "jukebox";
							$arr['subaction'] = "jukebox-command";
							$arr['command'] = "jumpto";
						?>
						<form action="<?php echo urlize($arr); ?>" method="post">
						<input type="hidden" name="action" value="jukebox">
						<input type="hidden" name="subaction" value="jukebox-command">
						<input type="hidden" name="command" value="jumpto">
							<select name="jbjumpto" id="jukeboxJumpToSelect" class="jz_select" size="6" style="width:275px;"<?php if ($func['jump']){ echo 'ondblclick="sendJukeboxJumpTo(); return false;"'; }?>>
								<?php
									for ($i=0; $i < count($fullList); $i++){
										echo '<option value="'. $i. '"';
										if ($curTrackNum == $i) { 
											echo " style=\"font-weight:bold;\" ";
											echo '> * '. $fullList[$i]. '</option>'; 		
										} else {
											echo '>'. $fullList[$i]. '</option>';
										}
										
									}
								?>
							</select>
						</form>
					<?php
						}
					?>
				</td>
				<td width="90%" valign="top">					
					<?php
						if ($jz_jbstatus == 'playing'){
							$curTrackLength = $jb->getCurrentTrackLength();
							$curTrackLoc = $jb->getCurrentTrackLocation();	
							?>
							<script> 
								<!--// 
								var seconds = '<?php echo $curTrackLoc; ?>';
								var time = '';
								t = document.getElementById("timer");	
								
								function converTime(sec){
									ctr=0;
									while (sec >= 60){
										sec = sec - 60;
										ctr++;
									}
									if (ctr<0){ctr=0}
									if (sec<0){sec=0}
									if (sec < 10){sec = "0" + sec;}							
									return ctr + ":" + sec;
								}
								
								function displayCountdown(){ 
								  return;
									// Update the counter
									seconds++	
										
									// Now let's not go over
									if (seconds < <?php echo $curTrackLength; ?>){
										t.innerHTML = converTime(seconds) + "/<?php echo convertSecMins($curTrackLength); ?>";
									} else {
										t.innerHTML = "<?php echo convertSecMins($curTrackLength); ?>/<?php echo convertSecMins($curTrackLength); ?>";
										<?php writeLogData("messages","Jukebox block: Refreshing the jukebox display"); ?>
										seconds = 1;
										updateJukebox(true);
									}
									setTimeout("displayCountdown()",1000);
								} 
								displayCountdown();
								--> 
							</script> 
							<?php						
							}
							// Now we need to return the path to the track that is playing so we can get the art and description for it
							$filePath = $jb->getCurrentTrackPath();
							$track = new jzMediaNode($filePath,"filename");
							
							// Now let's make sure we are looking at a track for real
							if (false !== $track && $track->getPath() != ""){		
								
								$node = $track->getAncestor("album");
								
								if ($node) {
									// Now let's set what we'll need
									$album = ucwords($node->getName());
									$parent = $node->getAncestor("artist");
									if ($parent) {
										$artist = ucwords($parent->getName());
									} else {
										$artist = "";
									}
									// Now let's display the art
									if (($art = $node->getMainArt("130x130")) == false) {
										$art = "style/images/default.jpg";
									}
									$display->link($parent, $artist, $artist, false, false, false, false, false, "_top");
									echo " - ";
									$display->link($node, $album, $album, false, false, false, false, false, "_top");
									echo "<br>";
									echo $display->returnImage($art,$node->getName(),"130","130","fit",false,false,"left","5","5");
								
									// Now let's get the review
									$desc = $node->getDescription();
									$desc_truncate = 375;
									echo $display->returnShortName($desc,$desc_truncate);
									if (strlen($desc) > $desc_truncate){
										$url_array = array();
										$url_array['jz_path'] = $node->getPath("String");
										$url_array['action'] = "popup";
										$url_array['ptype'] = "readmore";
										echo ' <a href="'. urlize($url_array). '" onclick="openPopup(this, 450, 450); return false;">...read more</a>';
									}
								}
							}
					?>
				</td>
			</tr>
		</table>
		<SCRIPT><!--\			
			NextTicker_start();
			CurTicker_start();
		//-->
		</script>
		    <script>setTimeout('jukeboxUpdater()',10*1000);</script>
		<?php
	}



	  function trackTable($tracks, $purpose = false){
	    global $media_dir, $jinzora_skin, $hierarchy, $album_name_truncate, $row_colors, 
	      $img_more, $img_email, $img_rate, $img_discuss, $num_other_albums, $enable_ratings, $this_site, $allow_clips,
	      $root_dir, $jzUSER, $hide_id3_comments,$max_song_length, $enable_discussion, $max_song_name_length, $show_lyrics_links, 
				$allow_send_email,$handle_compilations, $video_types, $show_track_numbers;
	
		if (sizeof($tracks) == 0) return;
		// Let's setup the new display object
		$display = &new jzDisplay();
		
		$tracks_only = array();
		foreach ($tracks as $track) {
		  if (is_object($track)) {
		    $tracks_only[] = $track;
		  }
		}

		// Now let's see if this is a Audio, Video, or Photo node
		$video = true;
		foreach($tracks_only as $track){
			if (!preg_match("/\.($video_types)$/i", $track->getDataPath())){
				$video = false;
			}
		}
		if ($video){
			$this->videoTable($tracks_only, $purpose);
			return;
		}

		// Let's figure out our settings:
		// First, set the defaults.
		$showNumbers = false;
		if ($show_track_numbers == "true"){
			$showNumbers = true;

		}
		$showArtist = false;
		$showAlbum = false;
		$showCheck = false;
		$showInfo = false;
		$showEmail = false;
		$showRate = false;
		$showDisc = false;
		$showAlbumNames = false;
		$trackTruncate = false;
		$showPlayCount = false;
		
		if ($enable_discussion == "true"){
			$showDisc = true;
		}
		$trackTruncate = $max_song_name_length;

		// Now adjust as needed:
		switch ($purpose) {
		case "generic":
		  $showNumbers = false;
		  break;
		case "album":
		  $showCheck = true;
		  $showInfo = true;
		  $showPlayCount = true;
		  $showEmail = true;
		  break;
		case "search":
		  $showCheck = true;
		  $showArtist = true;
		  $showInfo = true;
		  $showEmail = true;
			$trackTruncate = 100;
		  break;
		case "sample":
			// We only want to show album thumbs IF this artist has more than 1 album
			$parent = $tracks_only[0]->getParent(); 
			$gParent = $parent->getParent();
			$nodes = $gParent->getSubNodes("nodes");
			$showNumbers = false;
			$showAlbum = true;
		  break;
		case "sample-all":
		  $showNumbers = false;
		  $showCheck = true;
		  $showAlbumNames = true;
		  $trackTruncate = 20;
		  break;
		}
		
		if ($allow_send_email == "false"){
			$showEmail = false;
		}

		// Do we need to start the form
		if ($showCheck){
			$node = $tracks_only[0]->getParent();
			?>
			<form name="albumForm" action="<?php echo urlize(); ?>" method="POST">
			<input type="hidden" name="<?php echo jz_encode("action"); ?>" value="<?php echo jz_encode("mediaAction"); ?>">
				<?php if ($purpose != "search") { ?> 
			  <input type="hidden" name="<?php echo jz_encode("jz_path"); ?>" value="<?php echo htmlentities(jz_encode($node->getPath("String"))); ?>">
				<?php } ?>
			<input type="hidden" name="<?php echo jz_encode("jz_list_type"); ?>" value="<?php echo jz_encode("tracks"); ?>">
			<?php
		}
		
		// Now let's setup the big table to display everything
		$i=0;
		  ?>
		  <table class="jz_track_table" width="100%" cellpadding="3" cellspacing="0" border="0">
		 <?php
		     $artists = array();

		if ($handle_compilations == "true") {
		  foreach ($tracks_only as $child) {
		    $a = $child->getMeta();
		    if (!isNothing($a['artist'])) {
		      $artists[$a['artist']] = true;
		    }
		  }
		} else {
		  foreach ($tracks_only as $child) {
		    $a = $child->getAncestor('artist');
		    if ($a !== false) {
		      $artists[strtoupper($a->getName())] = true;
		    }
		  }
		}
		if (sizeof($artists) > 1) {
		  $multiArtist = true;
		} else {
		  $multiArtist = false;
		}

		$first_label = true;
		 foreach ($tracks as $child) {
		   // is it a header?
		   if (is_string($child)) {
		     if (!$first_label) {
		       echo '<tr><td colspan="100">&nbsp;</td></tr>';
		     }
		     echo '<tr><td colspan="100">' . $child . '</td></tr>';

		     $first_label = false;
		     continue;
		   }
			 
		 	// Let's make sure this isn't a lofi track
			if (substr($child->getPath("String"),-9) == ".lofi.mp3" or substr($child->getPath("String"),-9) == ".clip.mp3"){continue;}
		   // First let's grab all the tracks meta data
		   $metaData = $child->getMeta();
		   $album = $child->getParent();
		   if (findPType($album) == "disk") {
		     $album = $album->getParent();
		   }
		   $gParent = $album->getParent();
		   $artist = getInformation($album,"artist");
		   ?>
		   <tr class="<?php echo $row_colors[$i]; ?>">
		   <?php
		   if ($showCheck and $jzUSER->getSetting('stream')){
		     $value = htmlentities(jz_encode($child->getPath("String")));
		   ?>
		   <td width="1%" valign="top" class="jz_track_table_songs_td">
		   <input type="checkbox" name="jz_list[]" value="<?php echo $value; ?>">
		   </td>
		   <?php } ?>
		   
		   <td width="99%" valign="top" class="jz_track_table_songs_td" nowrap>
		   	<?php 
		   		echo $display->playButton($child); 
			?>
			 <?php
				// Now, is there a lofi version?
				$loFile = substr($child->getDataPath("String"),0,-4).".lofi.mp3";
				if (is_file($loFile) and $jzUSER->getSetting('stream')){
					$lofi = new jzMediaTrack(substr($child->getPath("String"),0,-4).".lofi.mp3");
					//echo '<td width="1%" valign="top" class="jz_track_table_songs_td">';
					echo $display->lofiPlayButton($lofi);
					//echo '</td>';
				}
			   ?>
			   <?php
				// Now, is there a clip version?
				$loFile = substr($child->getDataPath("String"),0,-4).".clip.mp3";
				if (is_file($loFile) || $allow_clips == "true"){
					//echo '<td width="1%" valign="top" class="jz_track_table_songs_td">';
					echo $display->clipPlayButton($child);
					//echo '</td>';
				}
			   ?>
			   
		   <?php echo $display->downloadButton($child); ?>
		   <?php
		   
		   // Do they want ratings?
		   if ($enable_ratings == "true"){
				$display->rateButton($child);
		   }
		   if ($showInfo){		    
		     echo " ";
		     $display->infoButton($child);
		   }
		   if ($showEmail and $jzUSER->getSetting('stream')){
		     $display->emailButton($child);
		   }
		   ?>
		   <?php
		   if ($showDisc){
		   ?>
		   <a class="jz_track_table_songs_href" href=""><?php $display->displayDiscussIcon($child); ?></a>
		   <?php
		   }
		   ?>
		   <?php 
		   if ($showArtist !== false) {
			 $j = 0;
			 while ($j < sizeof($hierarchy) && $hierarchy[$j] != 'artist') {
			   $j++;
			 }
			 if ($j < sizeof($hierarchy)) {
			   $parent = $child;
			   while ($parent->getLevel() > $j+1) {
					 $parent = $parent->getParent();
			   }
			   $display->link($parent,$parent->getName(),$parent->getName(),"jz_track_table_songs_href");
			   echo " / ";
			 }
		   }
		   
		   // This is where we display the name		   
		   
		   if ($multiArtist) {
		     if ($handle_compilations == "true") {
		       $artistName = $metaData['artist'];
		       if (isNothing($artistName)) {
						 $artist = $child->getAncestor("artist");
						 if ($artist !== false) {
							 $artistName = $artist->getName();
						 }
		       }
		     } else {
		       $artist = $child->getAncestor("artist");
		       if ($artist !== false) {
						 $artistName = $artist->getName();
		       }
		     }
		     if (isset($artistName)) {
		       $tName = $child->getName();
		       unset($artistName);
		     } else {
		       $tName = $child->getName();
		     }
		   } else {
		     $tName = $child->getName();
		   }
		   if ($trackTruncate) {
		     $tName = returnItemShortName($tName, $trackTruncate);
		   }
			 if ($purpose == "search"){
				 $album = $child->getAncestor("album");
				 $display->link($album, $album->getName(), $album->getName(), "jz_track_table_songs_href"); 
				 echo " / ";
			 }
			 
			 if ($showNumbers){
			 	echo $metaData['number']. " -&nbsp;";	
			 }
			 
		   if ($jzUSER->getSetting('stream')){
		     if ($showAlbum){
		       $descName = $album->getName(). " - ". $child->getName();
		     } else {
		       $descName = $child->getName();
		     }
		     // $tName = $child->getName();
		     $display->link($child, $tName, $descName, "jz_track_table_songs_href"); 
		   } else {
		     echo $tName;
		   }
		   // Did they want to see lyrics links?
		   if ($show_lyrics_links == "true"){
		   		if ($metaData['lyrics'] <> ""){
					$urlArr = array();
					$urlArr['jz_path'] = $child->getPath("String");
					$urlArr['action'] = "popup";
					$urlArr['ptype'] = "viewlyricsfortrack";
					echo '<a href="'. urlize($urlArr). '" onclick="openPopup(this, 450, 450); return false;"> - '. word("Lyrics"). '</a>';
				}
		   }
		   // Now let's show the description if there is one
		   if ($short_desc = $child->getShortDescription() and $hide_id3_comments == "false"){
		   	echo "<br>". $short_desc;
		   }
		   if ($description = $child->getDescription()){
		   	echo "<br>". $description;
		   }

		   // Do they want ratings?
		   /*if ($enable_ratings == "true"){
		   		//$rating = $display->displayRating($child,true);
				if ($rating){
					//echo "<br>". $rating;
				}
				//unset($rating);
		   }*/
		   ?>
		   </td>
		   
		   <?php
		   $lyricsSearch = false;
		   if (isset($_POST['search_type'])){
			if ($_POST['search_type'] == "lyrics"){
				$lyricsSearch = $_POST['search_query'];
			}
		   }
			if (isset($_GET['search_type'])){
			if ($_GET['search_type'] == "lyrics"){
				$lyricsSearch = $_GET['search_query'];
			}
		   }
			if ($lyricsSearch){
				// Now let's get the lyrics back
				$lyrics = $child->getLyrics();
				// Now let's parse it out
				$lyrics = str_replace("Lyrics Provided by: Leo's Lyrics\nhttp://www.leoslyrics.com","",$lyrics);
				$start = strpos(strtolower($lyrics),strtolower($lyricsSearch))-20;
				if ($start < 0){$start=0;}
				$lyrics = "&nbsp; &nbsp; &nbsp; &nbsp;(...". substr($lyrics,$start,strlen($lyricsSearch)+40). "...)";
				$lyrics = highlight($lyrics,$lyricsSearch);
				echo '<td width="6%" align="center" valign="top" class="jz_track_table_songs_td" nowrap>';
				echo $lyrics;
				echo '</td>';
			}
		   ?>
		   
		   
		   <?php   	
			if ($showAlbumNames){
				echo '<td width="1%" class="jz_track_table_songs_td" nowrap>';
				$display->link($album, returnItemShortName($album->getName(),20),$album->getName(),"jz_track_table_songs_href");
				echo '</td>';
			}
		   ?>
		   
			<?php
				if ($showPlayCount){
					echo '<td width="1%" align="center" valign="top" class="jz_track_table_songs_td" nowrap>';
					if ($child->getPlayCount() <> 0){
						echo $child->getPlayCount(). " ". word("Plays");
					} else {
						echo " - ";
					}
					echo '</td>';
				}
			?>
		   
		   
		   <td width="6%" align="center" valign="top" class="jz_track_table_songs_td" nowrap>
		   &nbsp; <?php echo convertSecMins($metaData['length']); ?> &nbsp;
		   </td>
		   </tr>
		   <?php		
		   $i = 1 - $i;
		   unset($gParent);unset($album);
		 }
		
		// Now let's set a field with the number of checkboxes that were here
		echo "</table>";
		
		// Now let's show the playlist bar if we should
		if ($showCheck){
			$this->blockSpacer();
			$this->playlistBar();
			echo "</form>";
		}
	  }

  
  /**
   * Displays a table of the given nodes.
   *
   * @author Ross Carlson
   * @version 11/30/04
   * @since 11/30/04
   */
  function nodeTable($nodes){
    global $media_dir, $skin, $hierarchy, $album_name_truncate, $row_colors, 
      $img_more, $img_email, $img_rate, $img_discuss, $num_other_albums, $img_download_dis;					
    
    if (sizeof($nodes) == 0) return;
    // Let's setup the new display object
    $display = &new jzDisplay();
    
    // Now let's setup the big table to display everything
    $i=0;
      ?>
      <table class="jz_track_table" width="100%" cellpadding="5" cellspacing="0" border="0">
	 <!--a-->
	 <?php
	 $c = -1;
	 foreach ($nodes as $child) {
	 	$path = $child->getPath("String");
	 	$c++;
	   ?>
	   <tr class="<?php echo $row_colors[$i]; ?>">
	   <!--
	   <td width="1%" valign="top" class="jz_track_table_songs_td">
	   <input class="jz_checkbox" type="checkbox" name="track-<?php echo $c; ?>" value="<?php echo $path; ?>">
	   </td>
	   -->
	   <td width="1%" valign="top" class="jz_track_table_songs_td">
	   <?php 
	   	echo $display->downloadButton($child); 
	   ?>
	   </td>
	   <td width="1%" valign="top" class="jz_track_table_songs_td">
	   <?php 
	   	
	   	echo $display->playButton($child); 
	   ?>
	   </td>
	   <td width="1%" valign="top" class="jz_track_table_songs_td">
	   <a class="jz_track_table_songs_href" href=""><?php echo $img_email; ?></a>
	   </td>
	   <td width="1%" valign="top" class="jz_track_table_songs_td">
	   <a class="jz_track_table_songs_href" href=""><?php echo $img_rate; ?></a>
	   </td>
	   <td width="1%" valign="top" class="jz_track_table_songs_td">
	   <a class="jz_track_table_songs_href" href=""><?php echo $img_discuss; ?></a>
	   </td>
	   <td width="1%" align="center" valign="top" class="jz_track_table_songs_td">&nbsp;
	   
	   </td>
	   <td width="100%" valign="top" class="jz_track_table_songs_td">
	   
	   <?php 
	   $parent = $child->getNaturalParent();
	   if ($parent->getLevel() > 0) {
	     $display->link($parent, $parent->getName("String"), $parent->getName(), "jz_track_table_songs_href"); 
	     echo " / ";
	   }
	   echo "<!--m-->";
	   $display->link($child, $child->getName("String"), $child->getName(), "jz_track_table_songs_href"); 
	   ?><!--n--></a>
	   </td>
	   <td width="12%" class="jz_track_table_songs_td" nowrap>&nbsp;
	   
	   </td>
	   <td width="10%" class="jz_track_table_songs_td" nowrap>
	   &nbsp; &nbsp;
	   </td>
	   <td width="10%" class="jz_track_table_songs_td" nowrap>
	   </td>
	   <td width="10%" class="jz_track_table_songs_td" nowrap>
	   &nbsp;  &nbsp;
	   </td>
	   <td width="10%" class="jz_track_table_songs_td" nowrap>&nbsp;
	    
	   </td>
	   <td width="10%" class="jz_track_table_songs_td" nowrap>
	   &nbsp;  &nbsp;
	   </td>
	   <td width="10%" class="jz_track_table_songs_td" nowrap>
	   &nbsp;  &nbsp;
	   </td>
	   </tr>
	   <?php		
	   $i = 1 - $i; // cool trick ;)
	 }
    
    // Now let's set a field with the number of checkboxes that were here
    echo "<!--z--></table><br>";
  }


  
	/**
	* Displays the random albums block
	* @author Ross Carlson
	* @version 12/22/04
	* @since 12/22/04
	* @param object $node the node that we are looking at
	* @param string $level The level we are looking at, like a subartist
	*/
	function randomAlbums(&$node, $level = "", $valArray = false){
		global $random_albums, $random_per_slot, $random_rate, $row_colors, $root_dir, $jzUSER, $show_album_art, $random_art_size;

		// Should we be here????
		if ($random_albums == "0" or $show_album_art == "false"){ return; }
		
		// Let's setup the new display object
		$display = &new jzDisplay();
		
		// Let's make sure they didn't pass the data already
		if ($valArray){
			$artArray = $valArray;
		} else {
			// Now let's get a random amount of albums with album art
		  $artArray = $node->getSubNodes("nodes",distanceTo("album",$node),true,$random_albums*$random_per_slot,true);
		}

		// Now let's see how much we got back and make sure we just shouldn't return
		if (count($artArray) == 0){ return; }

		// Now let's display the images			
		?>
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<tr>
				<?php
					// Now let's figure out how wide to make the colums
					if (($random_albums * $random_per_slot) > count($artArray)){
						// Now we've got to figure out how many we've got
						$numArt = count($artArray);
						if ($numArt > $random_albums){
							$random_per_slot = round(count($artArray) / $random_albums - .49,0);
						} else {
							$random_albums = count($artArray);
							$random_per_slot = 1;
						}
					}
					$colWidth = 100 / $random_albums;
					$c=1;
					while ($c < ($random_albums+1)){
						echo '<td align="center" valign="middle" width="'. $colWidth. '">';
						echo '<div id="div'. $c. '"></div>';
						echo '</td>';
						$c++;
					}
				?>
			</tr>
		</table>
		<?php		

		// Now let's add the Javascript for the rotations
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT"><!--\
			
			//you may add your image file or text below
			$c=1;
			// Now let's create the variables
			<?php
				$c=1;
				while ($c < ($random_albums + 1)){
					echo "var imgItem". $c. "=new Array()". "\n";
					$c++;
				}
			
				// Now let's build the first array with ALL the data so we can break it up later
				$c=0;
				for ($i=0; $i < count($artArray); $i++){					
					$albumName_long = $artArray[$i]->getName();
					$albumName = returnItemShortName($artArray[$i]->getName(),12);	 					
					$albumLink = str_replace('"',"\\\"",$display->link($artArray[$i],$albumName, word("Browse"). ": ". $albumName_long, "jz_random_art_block", true));
					
					$artist = $artArray[$i]->getNaturalParent();
					$artistName_long = $artist->getName();	 
					$artistName = returnItemShortName($artist->getName(),12);	 
					$artistLink = str_replace('"',"\\\"",$display->link($artist,$artistName, word("Browse"). ": ". $artistName_long, "jz_random_art_block", true));
					$artsize = explode("x",$random_art_size);
					$imgSrc = str_replace('"',"'",$display->returnImage($artArray[$i]->getMainArt($random_art_size),$artArray[$i]->getName(),$artsize[0],$artsize[1],"fixed"));
					$item_link = str_replace('"',"'",$display->link($artArray[$i],$imgSrc, $albumName_long, "jz_random_art_block", true));
					
					// Now, can they stream?
					if ($jzUSER->getSetting('stream')){
						$playLink = str_replace('"',"\\\"",$display->playLink($artArray[$i],word("Play"), word("Play"). ": ". $albumName_long, "jz_random_art_block", true));
						$randLink = str_replace('"',"\\\"",$display->playLink($artArray[$i],word("Play Random"), word("Play Random"). ": ". $albumName_long, "jz_random_art_block", true, true));
						$dispLink = $playLink. " - ". $randLink;
					} else {
						$dispLink = "";
					}
					
					// Let's make sure they aren'te view only				
					$arrayVar = "<center>". $artistLink. "<br>". $albumLink. "<br>". $item_link;
					if ($jzUSER->getSetting('stream')){
						$arrayVar .= "<br>". $dispLink. "</center>";
					}
					$fullArray[] = $arrayVar;					
				}
				
				// Now we need to get the different arrays
				$c=1; $start=0;
				while ($c < ($random_albums + 1)){
					$dataArray = array_slice($fullArray,$start,$random_per_slot);
					for ($ctr=0; $ctr < count($dataArray); $ctr++){
						echo "imgItem". $c. "[". $ctr. "]=\"". $dataArray[$ctr]. '"'. "\n";
					}
	
					// Now let's move on
					$start = $start+$random_per_slot;
					$c++;
				}
				
				// Now let's create the functions
				$c=1;
				while ($c < ($random_albums + 1)){
					?>					
					var current<?php echo $c; ?>=0
					<?php
					$c++;
				}
				$c=1;
				while ($c < ($random_albums + 1)){
					?>
					var ns6=document.getElementById&&!document.all
					function changeItem<?php echo $c; ?>(){
						if(document.layers){
							document.layer1.document.write(imgItem<?php echo $c; ?>[current<?php echo $c; ?>])
							document.layer1.document.close()
						}
						if(ns6)document.getElementById("div<?php echo $c; ?>").innerHTML=imgItem<?php echo $c; ?>[current<?php echo $c; ?>]
						{
							if(document.all){
								div<?php echo $c; ?>.innerHTML=imgItem<?php echo $c; ?>[current<?php echo $c; ?>]
							}
						}
						if (current<?php echo $c; ?>==<?php echo ($random_per_slot -1); ?>) current<?php echo $c; ?>=0
						else current<?php echo $c; ?>++
						<?php 
							if ($random_per_slot <> 1){
								?>
								setTimeout("changeItem<?php echo $c; ?>()",<?php echo $random_rate; ?>)
								<?php
							}
						?>
					}
					<?php
					$c++;
				}
				$c=1;
				while ($c < ($random_albums + 1)){
					?>					
					changeItem<?php echo $c; ?>();
					<?php
					$c++;
				}
			?>
			
			//-->
		</script>
		<?php
	}
}
?>
