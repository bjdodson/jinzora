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
	
	if (!is_array($ajax_list)) {
		$ajax_list = array();
	}

	$ajax_list[] = "ajaxSmallJukebox";
	$ajax_list[] = "ajaxJukeboxRequest";
	$ajax_list[] = "ajaxJukebox";
	$ajax_list[] = "ajaxJukeboxNowPlaying";
	$ajax_list[] = "ajaxJukeboxNextTrack";

	/** 
	* Gets the HTML for the 'now playing' of the jukebox.
	*
	* @author Ben Dodson
	* @since 8/22/05
	**/
	function ajaxJukeboxNowPlaying(){
	  global $include_path;

	  writeLogData("messages","Jukebox: Creating Now Playing element");
		
	  include_once($include_path. "jukebox/class.php");
	  $jb = new jzJukebox();

	  $curTrack = $jb->getCurrentTrackName();
	  $fullname = $curTrack;
	  if (strlen($curTrack)>23){
	    $curTrack = substr($curTrack,0,20). "...";
	  }
	  echo '<a href="javascript:void();" title="'. $fullname. '">'. $curTrack. "</a>";
	}

	/** 
	* Gets the HTML for the 'next track' of the jukebox.
	*
	* @author Ben Dodson
	* @since 8/22/05
	**/
	function ajaxJukeboxNextTrack(){
	  global $include_path;
		
		writeLogData("messages","Jukebox: Creating next track element");

	  include_once($include_path. "jukebox/class.php");
	  $jb = new jzJukebox();

	  $fullList = $jb->getCurrentPlaylist();
	  if ($fullList != array()) {
	    $nextTrack = $fullList[getCurPlayingTrack()+1];
	    $fullname = $nextTrack;
	    if (stristr($nextTrack,"/")){
	      $nArr = explode("/",$nextTrack);
	      $nextTrack = $nArr[count($nArr)-1];
	    }
	    $nextTrack = str_replace(".mp3","",$nextTrack);
	    if (strlen($nextTrack)>20){
	      $nextTrack = substr($nextTrack,0,20). "...";
	    }
	  }
	    echo '<a href="javascript:void();" title="'. $fullname. '">'. $nextTrack. "</a>";							  
	}

	/** 
	* Handles a jukebox action (play/stop/forward/etc.)
	*
	* @author Ben Dodson
	* @since 8/21/05
	**/
	function ajaxJukeboxRequest($command, $arg = false){
	  global $include_path;

		writeLogData("messages","Jukebox: Passing command '". $command. "' to the jukebox");

	  include_once($include_path. "jukebox/class.php");
	  $jb = new jzJukebox();

	  if ($command == "volume") {
	    $_POST['jbvol'] = $arg;
	  } else if ($command == "addwhere") {
	    $_POST['addplat'] = $arg;
	  } else if ($command == "jumpto"){
	  	$arg = explode(',',$arg);
	  	$_POST['jbjumpto'] = $arg[0];
	  } else {
	  	$_POST['jbSelectedItems'] = explode(',',$arg);
	  }
	 
	  $jb->passCommand($command); 
	}

	/** 
	* Returns the AJAX code for the default jukebox
	*
	* @author Ben Dodson
	* @since 8/22/05
	* @param new_jb: the jukebox to change to.
	**/
        function ajaxJukebox($new_jb = false, $direct_call = false) {
	  global $include_path,$jbArr;
	  
	  writeLogData("messages","Jukebox: Displaying the primary jukebox interface");
		
	  $blocks = new jzBlocks();

	  if ($new_jb !== false) {
	    // Change the jukebox
	    include_once($include_path."jukebox/class.php");
	    for ($i=0; $i < count($jbArr); $i++){
	      if ($jbArr[$i]['description'] == $new_jb){
		$_SESSION['jb_id'] = $i;
	      }
	    }
	    // Hack our POST vars:
	    $_POST['jbplaywhere'] = $new_jb;

	    $jb = new jzJukebox();
	    $jb->passCommand("playwhere");
	  }

	  $jb = new jzJukebox();

	  $value = $jb->getCurrentTrackName();
	  $label = "jb-" . $_SESSION['jb_id'] . "curtrack";
	  if ($direct_call == "false") { $direct_call = false; }
	  if (!$direct_call && isset($_SESSION[$label]) && $_SESSION[$label] == $value) {
	    return;
	  } else {
	    $_SESSION[$label] = $value;
	  }	
	  
	  $blocks->jukeboxBlock();

	}

	/** 
	* Returns the AJAX code for the small jukebox
	*
	* @author Ben Dodson
	* @since 8/21/05
	* @param new_jb: the jukebox to change to.
	**/
	function ajaxSmallJukebox($new_jb = false, $text = false, $buttons = false, $linebreaks = false){
	  global $include_path,$jbArr;
		
		writeLogData("messages","Jukebox: Displaying the small jukebox interface");
		
	  $blocks = new jzBlocks();

	  if ($new_jb !== false) {
	    // Change the jukebox
	    include_once($include_path."jukebox/class.php");
	    for ($i=0; $i < count($jbArr); $i++){
	      if ($jbArr[$i]['description'] == $new_jb){
		$_SESSION['jb_id'] = $i;
	      }
	    }
	    // Hack our POST vars:
	    $_POST['jbplaywhere'] = $new_jb;

	    $jb = new jzJukebox();
	    $jb->passCommand("playwhere");
	  }
	  $blocks->smallJukebox($text, $buttons, $linebreaks);
	}
	
?>