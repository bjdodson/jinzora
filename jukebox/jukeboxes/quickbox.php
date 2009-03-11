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
	* Contains the Winamp httpQ plugin functions
	*
	* @since 12/04/08
	* @author Ben Dodson <bjdodson@gmail.com>
	*/
	
	/*
	
	NOTES FOR THIS JUKEBOX
	
	This is a zero-configuration jukebox.
	Simply point a browser to [yoursite]/jukebox to have it work.
	*/	
	
	
	
	

	/**
	* The installer function for this jukebox
	* 
	* @author Ben Dodson
	* @version 12/04/08
	* @since 12/04/08
	* @param $step int The step of the install process we are on
	*/
	function jbInstall($step){
	}
	
	/**
	* Returns the stats of the jukebox
	* 
	* @author Ben Dodson
	* @version 12/04/08
	* @since 12/04/08
	* @param return Returns a keyed array of the jukeboxe's abilities
	*/
	function retJBStats(){
		global $jbArr;
		
		return;
	}	
	
	/**
	* Returns a keyed array showing all the functions that this jukebox supports
	* 
	* @author Ben Dodson
	* @version 12/04/08
	* @since 12/04/08
	* @param return Returns a keyed array of the jukeboxe's abilities
	*/
	function returnJBAbilities(){
		
		$retArray['playbutton'] = true;
		$retArray['pausebutton'] = true;
		$retArray['stopbutton'] = true;
		$retArray['nextbutton'] = true;
		$retArray['prevbutton'] = true;
		$retArray['shufflebutton'] = true;
		$retArray['clearbutton'] = true;
		$retArray['repeatbutton'] = false;
		$retArray['delonebutton'] = true;
		$retArray['status'] = false;
		$retArray['progress'] = false;
		$retArray['volume'] = false;
		$retArray['addtype'] = true;
		$retArray['nowplaying'] = true;
		$retArray['nexttrack'] = true;
		$retArray['fullplaylist'] = true;
		$retArray['refreshtime'] = true;
		$retArray['jump'] = true;
		$retArray['stats'] = false;
		$retArray['move'] = true;

		return $retArray;
	}
	
	/**
	* Returns the connection status of the player true or false
	* 
	* @author Ben Dodson
	* @version 12/04/08
	* @since 12/04/08
	* @param return Returns true or false
	*/
	function playerConnect(){
	  return true;
	}
	
	
	/**
	* Returns Addon tools for quickbox
	* 
	* @author Ben Dodson
	* @version 12/04/08
	* @since 12/04/08
	* @param return Returns a link to join the playlist
	*/
	function getAllAddOnTools(){
	  $url = urlize();
	  $url = substr($url,0,strpos($url,'index.php'));
	  $url .= 'jukebox/?id='.urlencode(Quickbox::getID());
	  return ' - <a href="'.htmlentities($url).'" target="_BLANK"> [Click to join]</a>';
	}
	
	
	/**
	* Returns the currently playing tracks path so we can get the node
	* 
	* @author Ben Dodson
	* @version 12/04/08
	* @since 12/04/08
	* @param return Returns the currently playling track's path
	*/
	function getCurTrackPath(){
	  $box = Quickbox::load();
	  if (sizeof($box['playlist']) > 0 && isset($box['pos'])) {
	    return $box['playlist'][$box['pos']];
	  }
	}
	
	/**
	* Returns the currently playing track number
	* 
	* @author Ben Dodson
	* @version 12/04/08
	* @since 12/04/08
	* @param return Returns the currently playling track number
	*/
	function getCurPlayingTrack(){
	  $box = Quickbox::load();
	  if (sizeof($box['playlist']) > 0 && isset($box['pos'])) {
	    return $box['pos'];
	  }
	}
		
	/**
	* Returns the currently playing playlist
	* 
	* @author Ben Dodson
	* @version 12/04/08
	* @since 12/04/08
	* @param return Returns the currently playling playlist
	* @param bolean Return FULL path only?
	*/
	function getCurPlaylist($path = false){
		global $jbArr;
		$box = Quickbox::load();
		$playlist = $box['playlist'];
		$retArray = array();
		if (!is_null($playlist)){
			foreach ($playlist as $i => $entry) {
				if ($path){
					$retArray[] = $entry;
				} else {
				    if (false !== ($id = getTrackIdFromURL($entry))) {
				      $track = new jzMediaTrack($id,'id');
				      $meta = $track->getMeta();
				      $retArray[] = $meta['artist'] . ' - ' . $meta['title'];
				    } else {
				      $retArray[] = word('Unknown');
				    }
				}
			}
		}		
		return $retArray;
	}

	/**
	* Passes a playlist to the jukebox player
	* 
	* @author Ben Dodson
	* @version 12/04/08
	* @since 12/04/08
	* @param $playlist The playlist that we are passing
	*/
	function playlist($playlist){
		global $include_path, $jbArr, $media_dirs,$jzSERVICES;
		
		$addtype = $_SESSION['jb-addtype'];
		$box = Quickbox::load();
		// todo: current, begin
		if ($addtype == 'end') {
		  $list = $box['playlist'];
		} else {
		  $list = array();
		}

		if ($addtype == 'replace') {
		  $box['pos'] = 0;
		}

		foreach ($playlist->getList() as $track) {
		  $list[] = $track->getFileName("user")."\n";
		}

		
		$box['command']='playlist';
		$box['command_time']=time();
		$box['playlist']=$list;
		$box['addtype']=$addtype;
		
		Quickbox::store($box);
		
		exit();
	}
		
	/**
	* Passes a command to the jukebox player
	* 
	* @author Ben Dodson
	* @version 12/04/08
	* @since 12/04/08
	* @param $command The command that we passed to the player
	*/
	function control($command){
		global $jbArr;

		// Now let's execute the command

		$box = Quickbox::load();
		$box['command'] = $command;
		$box['command_time'] = time();
		switch ($command){
			case "play":
			case "stop":
			case "pause":
			case "previous":
		        case "next":
			  break; // Quickbox::store() done below switch
			case "volume":
				$_SESSION['jz_jbvol-'. $_SESSION['jb_id']] = $_POST['jbvol'];
			break;
			case "jumpto":
			  $box['pos'] = $_POST['jbjumpto'];
			  $_SESSION['jbSelectedItems'] = array($_POST['jbjumpto']);
			break;
			case "clear":
			  $box['command'] = 'playlist';
			  $box['playlist'] = array();
			break;
			case "delone":
			  $box['command'] = 'playlist';
			  $box['addtype'] = 'end';
			  for ( $i = sizeof($_POST['jbSelectedItems']) - 1; $i  >= 0; $i--) {
			    array_splice($box['playlist'],$_POST['jbSelectedItems'][$i],1);
			  }
			  $_SESSION['jbSelectedItems'] = array();
			  break;
		        case "repeat":
		                $myMpd->setRepeat(1);
		        break;
		        case "no_repeat":
		                $myMpd->setRepeat(0);
		        break;
			case "random_play":
			  shuffle($box['playlist']);
			  $box['addtype']='replace';
			break;
			case "refreshdb":
			break;
			case "addwhere":
				$_SESSION['jb-addtype'] = $_POST['addplat'];
			break;
			case "moveup":
			  $box['command'] = 'playlist';
			  $box['addtype'] = 'end';
			  $items = $_POST['jbSelectedItems'];
			  sort($items);
			  $i = 0;
			  // find first moveable.
			  while ($i < sizeof($items) && $items[$i] == $i) {
			    $i++;
			  }
			  for ($i; $i < sizeof($items); $i++) {
			    $tmp = $box['playlist'][$items[$i]-1];
			    $box['playlist'][$items[$i]-1]=$box['playlist'][$items[$i]];
			    $box['playlist'][$items[$i]]=$tmp;

			    // does not currently correct the 
			    // value of $box['pos'].

			    // update for displaying the list:
			    $items[$i] = $items[$i]-1;
			  }
			  $_SESSION['jbSelectedItems'] = $items;
			break;
			case "movedown":
			  $box['command'] = 'playlist';
			  $box['addtype'] = 'end';
			  $items = $_POST['jbSelectedItems'];
			  sort($items);
			  $i = sizeof($items) - 1;
			  $j = sizeof($box['playlist']) - 1;
			  // find first moveable.
			  while ($i >= 0 && $items[$i] == $j) {
			    $i--; $j--;
			  }
			  for ($i; $i >= 0; $i--) {
			    $tmp = $box['playlist'][$items[$i]];
			    $box['playlist'][$items[$i]]=$box['playlist'][$items[$i]+1];
			    $box['playlist'][$items[$i]+1]=$tmp;
			    // update for displaying the list:
			    $items[$i] = $items[$i]+1;
			  }
			  $_SESSION['jbSelectedItems'] = $items;
			  break;
		}

		Quickbox::store($box);

		if (defined('NO_AJAX_JUKEBOX')) {
		?>
		<script>
			history.back();
		</script>
		<?php
		    }
	}
	
	/**
	* Returns the players current status
	* Can get the following statuses: playback|repeat 
	*
	* @author Ben Dodson
	* @version 12/04/08
	* @since 12/04/08
	*/
	function getStatus($type = "playback"){
		global $jbArr;
		
		return "";
	}
	
	/**
	* Returns the current playing track
	* 
	* @author Ben Dodson
	* @version 12/04/08
	* @since 12/04/08
	* @return Returns the name of the current playing track
	*/
	function getCurTrackName(){
	  $box = Quickbox::load();
	  if (sizeof($box['playlist']) > 0 && isset($box['pos'])) {
	    $entry = $box['playlist'][$box['pos']];
	  }


	  if (false !== ($id = getTrackIdFromURL($entry))) {
	    $track = new jzMediaTrack($id,'id');
	    $meta = $track->getMeta();
	    return $meta['artist'] . ' - ' . $meta['title'];
	  } else {
	    return word('Unknown');
	  }

	}
	
	/**
	* Returns how long is left in the current track (in seconds)
	* 
	* @author Ben Dodson
	* @version 2/9/05
	* @since 2/9/05
	* @return Returns the name of the current playing track
	*/
	function getCurTrackRemaining(){
	}
	
	/**
	* Gets the length of the current track
	* 
	* @author Ben Dodson
	* @version 2/9/05
	* @since 2/9/05
	* @param return returns the amount of time remaining in seconds
	*/
	function getCurTrackLength(){
	}
	
	/**
	* Returns how long is left in the current track (in seconds)
	* 
	* @author Ben Dodson
	* @version 2/9/05
	* @since 2/9/05
	* @return Returns the name of the current playing track
	*/
	function getCurTrackLocation(){
	}
	
   /**
	* Updates the database
	* 
	* @author Ben Dodson
	* @version 12/08/06
	* @since 12/08/06
	*/
	function updateJukeboxDB($node, $recursive, $root_path){
	}

class Quickbox {
  static $backend = null;
  static $jbid = null;
  static $boxes = bull;
  
  static function init() {
    global $jbArr;
    if (null != Quickbox::$backend) return;

    Quickbox::$backend = new jzBackend();
    Quickbox::$jbid=$jbArr[$_SESSION['jb_id']]['description'];
    Quickbox::$boxes = Quickbox::$backend->loadData('quickboxes');
  }
  static function load($id = false) {
    Quickbox::init();
    if ($id === false) $id = Quickbox::$jbid;
    $box = Quickbox::$boxes[$id];
    return $box;
  }
  
  static function store($box,$id=false) {
    Quickbox::init();
    if ($id === false) $id = Quickbox::$jbid;
    Quickbox::$boxes[$id] = $box;
    Quickbox::$backend->storeData('quickboxes',Quickbox::$boxes);
  }

  static function getID() {
    Quickbox::init();
    return Quickbox::$jbid;
  }
}

?>
