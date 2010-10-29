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
	  $url .= 'jukebox/jukeboxes/junctionbox/?id=';
	  return ' - <a href="'.htmlentities($url).'" target="_BLANK"> [Click to open]</a>';
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
		
		/**
		 * The Junction jukebox works in Javascript on the client.
		 * The Jinzora client sends the Jukebox a command to download
		 * a playlist. That command gets routed here by Jinzora's
		 * internal jukebox logic. We simply re-route the message as
		 * if it were a simple request to download a playlist.
		 */
		$playlist->stream();
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
		
		switch ($command){
		  case "addwhere":
		    $_SESSION['jb-addtype'] = $_POST['addplat'];
		    break;
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
?>
