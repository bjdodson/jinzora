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
	* Contains the dummy jukebox player functions
	*
	* @since 2/9/05
	* @author Ross Carlson <ross@jinzora.org>
	*/
	
	/*
	
	NOTES FOR THIS JUKEBOX
	
	This Jukebox is for demo and testing purposes only
	This Jukebox requires the following settings:

	description
	type
	
	An example would be:
	$jbArr[0]['description'] = "Dummy Player";
	$jbArr[0]['type'] = "dummy";
	
	*/	
	
	/**
	* Returns Addon tools for MPD - namely refresh jukebox database
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param return Returns a link to refresh the MPD database
	*/
	function getAllAddOnTools(){
		return;
	}
	
	/**
	* Returns the currently playing tracks path so we can get the node
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param return Returns the currently playling track's path
	*/
	function getCurTrackPath(){
		global $jbArr;
		
		return;
	}
	
	/**
	* Returns the currently playing playlist
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param return Returns the currently playling playlist
	*/
	function getCurPlayingTrack(){
		global $jbArr;
		
		return;
	}
	
	/**
	* Returns the currently playing playlist
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param return Returns the currently playling playlist
	*/
	function getCurPlaylist(){
		global $include_path, $jbArr, $media_dirs;
		
		$list = explode("\n",@file_get_contents($include_path. "temp/dummy-pl.txt"));
		$mArray = explode("|",$media_dirs);
		for ($i=0; $i < count($mArray); $i++){
			$list = str_replace($mArray[$i]. "/","",$list);
		}
		return $list;
	}

	/**
	* Passes a playlist to the jukebox player
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param $playlist The playlist that we are passing
	*/
	function playlist($playlist, $action = false){
		global $include_path, $jbArr,$jzSERVICES;

		$playlist = $jzSERVICES->createPlaylist($playlist,"jukebox");
		// Let's write this out to the playlist file
		$fileName = $include_path. "temp/dummy-pl.txt";
		$handle = fopen($fileName, "w");
		fwrite($handle,$playlist);	
		fclose ($handle);
		
		// Now that we've written the file we need to send it to the player
		if ($action){
			$_SESSION['jb_dum_status'] = $action;
		} else {
			$_SESSION['jb_dum_status'] = "playing";
		}
		?>
		<script>
			history.back();
		</script>
		<?php
		exit();
	}
		
	/**
	* Passes a command to the jukebox player
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param $command The command that we passed to the player
	*/
	function control($command){
		global $jbArr;

		// Now let's execute the command
		switch ($command){
			case "play":
				$_SESSION['jb_dum_status'] = "playing";
			break;
			case "stop":
				$_SESSION['jb_dum_status'] = "stopped";
			break;
			case "pause":
				$_SESSION['jb_dum_status'] = "paused";
			break;
			case "previous":
				
			break;
			case "next":
				
			break;
			case "volume":
				// Now we have to set the value based on 0-255
				$_SESSION['jz_jbvol-'. $_SESSION['jb_id']] = $_POST['jbvol'];
			break;
			case "playwhere":
				// Ok, let's set where they are playing
				$_SESSION['jb_playwhere'] = $_POST['jbplaywhere'];
				// Now let's figure out it's ID
				for ($i=0; $i < count($jbArr); $i++){
					if ($jbArr[$i]['description'] == $_SESSION['jb_playwhere']){
						$_SESSION['jb_id'] = $i;
					}
				}
			break;
			case "jumpto":
				// We need to add 1 so we don't start at 0
				$pos = $_POST['jbjumpto'];
				$_SESSION['jb_dum_status'] = "playing";
			break;
			case "clear":
				$_SESSION['jb_dum_status'] = "stopped";
				playlist("","stopped");
			break;
			case "random_play":
				$_SESSION['jb_dum_status'] = "playing";
			break;
		}
		?>
		<script>
			history.back();
		</script>
		<?php
	}
	
	/**
	* Returns the players current status
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	*/
	function getStatus(){
		global $jbArr;
		
		return $_SESSION['jb_dum_status'];
	}
	
	/**
	* Returns the current playing track
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @return Returns the name of the current playing track
	*/
	function getCurTrackName(){
		global $jbArr;

		return;
	}
	
	/**
	* Returns how long is left in the current track (in seconds)
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @return Returns the name of the current playing track
	*/
	function getCurTrackRemaining(){
		global $jbArr;
		
		return 0;
	}
?>