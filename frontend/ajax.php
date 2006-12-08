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
	* Code Purpose: This page contains all AJAX display functions
	* Created: 8.20.05 Ben Dodson
	*
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	
	
	if (!isset($ajax_list) || !is_array($ajax_list)) {
		$ajax_list = array();
	}
	
	// BUILD FUNCTIONS LIKE THIS:
	$ajax_list[] = "returnNowStreaming";
	$ajax_list[] = "returnWhoisWhere";
	$ajax_list[] = "returnBreadcrumbs";	
	$ajax_list[] = "returnAlbumAlbumBlock";
	$ajax_list[] = "returnCurrentInfo";
	$ajax_list[] = "setResample";
	$ajax_list[] = "setLyrics";
	/** 
	* Returns the AJAX code for currently playing info
	*
	* @author Ross Carlson
	* @since 8.21.05
	*
	**/
	function returnCurrentInfo($mysid, $update){
	  global $jzUSER, $jzSERVICES;

	  $be = new jzBackend();
	  $display = new jzDisplay();
	  $tracks = $be->getPlaying();
	  foreach($tracks as $sid => $song){
	    if ($mysid == $sid) {
	      $track = new jzMediaTrack($song['path']);
	      $album = $track->getAncestor("album");
	      $artist = $track->getAncestor("artist");
	      if ($update != "false") {
					if (isset($_SESSION['currentinfo']) && $_SESSION['currentinfo'] == $track->getID()) {
						return;
					}
	      }

	      $_SESSION['currentinfo'] = $track->getID();
	      
				echo '<table width="100%" cellpadding="2" cellspacing="0"><tr><td valign="top">';
				//echo $artist->getName(). " - ". $album->getName();
				
				$dispYear="";
				if (!isNothing($track->getYear())){
					$dispYear = " (". $track->getYear(). ")";
				}		
						
				$arr = array();
				$arr['jz_path'] = $artist->getPath("String");
				echo '<a href="javascript:;" onclick="window.opener.location.href = '. "'". str_replace("ajax_request","index",urlize($arr)). "'". '; return false;">'. $artist->getName(). '</a>';
				echo " - ";
				echo $track->getName();
				
				echo "<br>";
				$arr = array();
				$arr['jz_path'] = $album->getPath("String");
				echo '<em><a href="javascript:;" onclick="window.opener.location.href = '. "'". str_replace("ajax_request","index",urlize($arr)). "'". '; return false;">'. $album->getName(). $dispYear. '</a></em>';
				
				$art = $track->getMainArt('100x100');
	      if (!$art) {
					$art = $album->getMainArt('100x100');
	      }
	      if ($art) {
					echo str_replace('"','',$display->returnImage($art,"",150,150,"limit",false,false,"left","3","3"));				
				}
				
				$lyrics_cols = 48;
		
	      $meta = $track->getMeta();
				$desc = $album->getDescription();
				if ($desc <> ""){
					echo "<br>". $desc;
					$lyrics_cols = 22;
				}
				
				// Safe to do a lyrics search since the popup is not refreshed unnecessarily.
				if (isNothing($meta['lyrics'])) {
					$lyrics = $jzSERVICES->getLyrics($track);
					if (!isNothing($lyrics)) {
						$meta2 = array();
						$meta2['lyrics'] = $lyrics;
						$track->setMeta($meta2);
						$meta['lyrics'] = $lyrics;
					}
				}
				echo '</td><td valign="top">';

				$admin = checkPermission($jzUSER,'admin',$track->getPath("String"));
				if (!isNothing($meta['lyrics']) || $admin) {
					
					$urlArr = array();
					$urlArr['jz_path'] = $track->getPath("String");
					$urlArr['action'] = "popup";
					$urlArr['ptype'] = "viewlyricsfortrack";
					//echo '<a href="'. str_replace("ajax_request","index",urlize($urlArr)). '" onclick="openPopup(this, 450, 450); return false;"><textarea class="jz_input" rows="14" style="border: 0px;" cols="22">'. $meta['lyrics']. '</textarea></a>';
					
					echo '<textarea id="lyricsbox" name="lyrics" class="jz_input" rows="14" style="border: 0px;" cols="'.$lyrics_cols.'">'. $meta['lyrics']. '</textarea>';
					
					if ($admin) {
					  echo '<br><input type="button" class="jz_submit" value="'.word('Update'). '" onClick="x_setLyrics(document.getElementById('."'lyricsbox'".').value,'."'".str_replace("'","\\'",$track->getPath("String"))."'".', alert_cb)">';
					}
	      }
				echo '</td></tr></table>';
	      return;
	    }
	  }
	  
	  echo word("No track information found.");
	}

	
	function returnAlbumAlbumBlock(){
		
	}
	
	/** 
	* Returns the AJAX code for the Whow is where block
	*
	* @author Ross Carlson
	* @since 8.21.05
	*
	**/
	function returnBreadcrumbs(){
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
			$bcrumbs .= 'value="'. jz_encode($path). '">'. $display->returnShortName($child->getName(),20). '</option>';
		}
		$bcrumbs .= '</select>';
		$bcrumbs .= "</form>&nbsp;";
		
		echo $bcrumbs;

		exit();
	}
	
	/** 
	* Returns the AJAX code for the Whow is where block
	*
	* @author Ross Carlson
	* @since 8.21.05
	*
	**/
	function returnWhoisWhere(){
		global $jzUSER, $img_tiny_play, $img_tiny_play_dis, $user_tracking_age, $css, $include_path, $root_dir, $who_is_where_height,$jzSERVICES; 

		$define_only=true;
		
		writeLogData("messages","WIWB: starting up");
		
		$display = new jzDisplay();
		$be = new jzBackend();
				
		// let's get the history
		$oldHist = $be->loadData('history');
		$dArr = explode("\n",$oldHist);

		$retVal = "";
		$count=1;
		$home=0;
		$ipList="";
		for ($i=0; $i < count($dArr); $i++){
			$vArr = explode("|",$dArr[$i]);
			// Now let's make sure this isn't the current user and that it's not too old
			if (isset($vArr[3])){
				$ago = ($vArr[3] + ($user_tracking_age * 60));
				if (($vArr[4] <> $jzUSER->getName() or $vArr[4] == "Anonymous") and $vArr[6] <> $_SESSION['sid'] and ($ago - time() > 0) and !stristr($ipList,"|". $vArr[7])){				
					$time = round((time() - $vArr[3]) / 60);
					if ($time < 1){$time=1;}
					
					$ipList .= "|". $vArr[7];

					// Now let's count
					$count++;
					
					writeLogData("messages","WIWB: Ago: ". $time);
					
					// Let's setup the object from the path
					$item = new jzMediaNode($vArr[2]);
					
					// Let's setup our links
					$arr = array();
					$arr['jz_path'] = $vArr[2];			
					
					if ($vArr[1] == ""){
						$vArr[1] = "Home";
						$home++;
					}

					$item = new jzMediaNode($arr['jz_path']);
					$art = $item->getMainArt("75x75");
					if ($art){
						$albumImage = str_replace('"','',$display->returnImage($art,"",75,75,"limit",false,false,"left","3","3"));				
					} else {
						$albumImage  = "";
					}
					$desc_truncate = 200;
					$desc = htmlentities(str_replace("'","",str_replace("'","",$item->getDescription())));
					$userName = $vArr[4];
					if ($userName == ""){
						$userName = "anonymous";
					}
					if ($vArr[5] <> ""){
						$userName = $vArr[5];
					}
					$body = "<strong>". word("Viewing: "). $userName. "</strong><br>". word("Last seen"). ": ". $time. " ". word("minutes ago"). "<br>". $albumImage. $display->returnShortName($desc,$desc_truncate);
					$title = str_replace("'","",$vArr[1]);
											
					// Let's display
					if ($vArr[1] <> "Home"){
						if ($jzUSER->getSetting('stream')){
							$retVal .= ' <a href="'. urlize($arr). '"';
							if (checkPlayback() == "embedded") {
								//$jzSERVICES = new jzServices();
								$jzSERVICES->loadService("players",$jzUSER->getSetting("player"));
								$retVal .= ' ' . $jzSERVICES->returnPlayerHref();
							}
							$retVal .= '>'. $img_tiny_play. '</a> <a target="_parent" '. $display->returnToolTip($body, $title). ' href="'. str_replace("ajax_request","index",urlize($arr)). '">'. $display->returnShortName($vArr[1],15). '</a><br>';
						} else {
							$retVal .= ' '. $img_tiny_play_dis. ' <a target="_parent" '. $display->returnToolTip($body, $title). ' href="'. str_replace("ajax_request","index",urlize($arr)). '">'. $display->returnShortName($vArr[1],15). '</a><br>';
						}
					}
				}
			}
		}
		if ($count == 1 or $count == 0){
			$tCtr = "";
		} else {
			$tCtr = " (". ($count - 1). ")";
		}
		$return = "<strong>". word("Who is Where"). $tCtr. "</strong><br />";
		if ($home > 0){
			$arr = array();
			$arr['jz_path'] = "";
			$return .= '<a href="'. str_replace("ajax_request","index",urlize($arr)). '">Home ('. $home. ")</a><br>";
		}

		$maxHeight = ($who_is_where_height*13)+26;
		$style = "";
		if ($maxHeight < (($count*13)+26)){
			$style = "<style>#whoiswhere{height: ". $maxHeight. "px;overflow:auto;}</style>";
		}
		$return .= $style. $retVal;
		
		writeLogData("messages","WIWB: displaying data");
		echo $return;
		exit();
	}
	 
	/** 
	* Returns the AJAX code for the NSB
	*
	* @author Ross Carlson
	* @since 8.21.05
	*
	**/
	function returnNowStreaming(){
		global $jzUSER, $img_tiny_play, $im_tiny_play_dis, $css, $img_tiny_info, $skin, $root_dir, $include_path,$jzSERVICES; 
		
		$define_only = true;
		//include_once($include_path. $css);

		writeLogData("messages","NSB: starting up");

		// Now let's figure out the height
		$be = new jzBackend();
		$display = new jzDisplay();
		$tracks = $be->getPlaying();	
		$retVal = "";
		$count=0;
		foreach($tracks as $sid => $song){
			// Let's make sure we got data
			if (count($song) <> 0){	
				// Now let's setup for our links
				$url_array = array();
				$url_array['jz_path'] = $song['path'];
				$url_array['action'] = "playlist";
				$url_array['type'] = "track";
				

				$urlArr = array();
				$urlArr['session'] = $sid;
				$urlArr['action'] = "popup";
				$urlArr['ptype'] = "viewcurrentinfo";
				$infoLink = '<a href="'. str_replace("ajax_request","index",urlize($urlArr)). '" onclick="openPopup(this, 450, 300); return false;">'. $img_tiny_info. '</a>';
				
				$arr = array();
				$pArr = explode("/",$song['path']);
				unset($pArr[count($pArr)-1]);
				$arr['jz_path'] = implode("/",$pArr);
				
				$songTrack = $display->returnShortName($song['track'],15);
				/*
				if ($lyricsLink == ""){
					$songTrack = $display->returnShortName($song['track'],15);
				} else {
					$songTrack = $display->returnShortName($song['track'],13);
				}
				*/
				
				$track = new jzMediaNode($song['path']);
				$item = $track->getParent();
				if ($item->getPType() == "disk") {
					$item = $item->getParent();
				}
				$album = $item->getName();
				$artParent = $item->getParent();
				$artist = $artParent->getName();
				
				$art = $item->getMainArt("75x75");
				if ($art){
					$albumImage = str_replace("'","\\'",str_replace('"','',$display->returnImage($art,$album,75,75,"limit",false,false,"left","3","3")));				
				} else {
					$albumImage  = "";
				}
				$desc_truncate = 200;
				$desc = htmlentities(str_replace("'","\\'",str_replace('"','',$item->getDescription())));
				
				// Now let's set the title and body
				$title = htmlentities(str_replace("'","\\'",str_replace('"','',$artist. " - ". $song['track'])));
				$userName = $song['name'];
				if ($userName == ""){
					$userName = word("Anonymous");
				}
				if ($song['fullname'] <> ""){
					$userName = $song['fullname'];
				}
				$body = "<strong>". word("Streaming to: "). $userName. "</strong><br>". $albumImage. $display->returnShortName($desc,$desc_truncate); //$albumImage;
				$count++;
				if ($jzUSER->getSetting('stream')){
					$retVal .= ' <a href="'. str_replace("ajax_request.php","index.php",urlize($url_array)). '"';
					if (checkPlayback() == "embedded") {
						//$jzSERVICES = new jzServices();
						$jzSERVICES->loadUserServices();
						$retVal .= ' ' . $jzSERVICES->returnPlayerHref();
					}
					$retVal .= '>'. $img_tiny_play. '</a>'. $infoLink. '<a '. $display->returnToolTip($body, $title). ' target="_parent" href="'. str_replace("ajax_request","index",urlize($arr)). '">'. $songTrack. '</a><br>';
				} else {
					$retVal .= ''. $img_tiny_play_dis. ''. $infoLink. '<a '. $display->returnToolTip($body, $title). ' target="_parent" '. $title. ' href="'. str_replace("ajax_request","index",urlize($arr)). '">'. $songTrack. '</a><br>';
				}
			}
		}
		if ($count == 1 or $count == 0){
			$tCtr = "";
		} else {
			$tCtr = " (". $count. ")";
		}
		
		$retVal = "<strong>". word("Now Streaming"). $tCtr. "</strong><br />". $retVal;
		$maxHeight = ($who_is_where_height*13)+26;
		$style = "";
		if ($maxHeight < (($count*13)+26)){
			$style = "<style>#whoiswhere{height: ". $maxHeight. "px;overflow:auto;}</style>";
		}
		$return .= $style. $retVal;
		
		
		
		writeLogData("messages","NSB: displaying data");
		echo $retVal;
		exit();
	}

/**
 * AJAX code to set the resample rate
 *
 * @author Ben Dodson
 * @version 2/16/06
 * @since 2/16/06
 * 
 * Note that $v is jz_encoded.
 **/
function setResample($v) {
  $v = jz_decode($v);

  $_SESSION['jz_resample_rate'] = $v;
  if ($_SESSION['jz_resample_rate'] == ""){
    unset($_SESSION['jz_resample_rate']);
  }
}

function setLyrics($lyrics, $path) {
  global $jzUSER;
  
  if (!checkPermission($jzUSER,'admin',$path)) {
    echo word('Insufficient permissions.');
  } else {
    $track = new jzMediaTrack($path);
    $meta = array();
    $meta['lyrics'] = $lyrics;
    $track->setMeta($meta);
    echo word('Lyrics updated.');
  }
}


?>
