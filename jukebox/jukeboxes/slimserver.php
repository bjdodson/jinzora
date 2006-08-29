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
	* Contains the Slimserver Jukebox Functions
	*
	* @since 4/10/05
	* @author Ross Carlson <ross@jinzora.org>
	*/
	
	/*
	
	NOTES FOR THIS JUKEBOX
	
	This Jukebox requires the following settings:
	
	server
	port
	description
	type
	
	An example would be:
	$jbArr[0]['server'] = "localhost";
	$jbArr[0]['port'] = "9090";
	$jbArr[0]['description'] = "SlimServer";
	$jbArr[0]['type'] = "slimserver"; // VERY IMPORTANT
	
	*/	
	
	
	/**
	* Returns the stats of the jukebox
	* 
	* @author Ross Carlson
	* @version 4/9/05
	* @since 4/9/05
	* @param return Returns a keyed array of the jukeboxe's abilities
	*/
	function retJBStats(){
		global $jbArr;
		
		return;
	}	
	
	/**
	* Returns a keyed array showing all the functions that this jukebox supports
	* 
	* @author Ross Carlson
	* @version 4/9/05
	* @since 4/9/05
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
		$retArray['status'] = true;
		$retArray['progress'] = true;
		$retArray['volume'] = true;
		$retArray['addtype'] = true;
		$retArray['nowplaying'] = true;
		$retArray['nexttrack'] = true;
		$retArray['fullplaylist'] = true;
		$retArray['refreshtime'] = true;
		$retArray['jump'] = true;
		$retArray['stats'] = false;
		
		return $retArray;
	}
	
	/**
	* Returns the connection status of the player true or false
	* 
	* @author Ross Carlson
	* @version 4/9/05
	* @since 4/9/05
	* @param return Returns true or false
	*/
	function playerConnect(){
		global $jbArr;
		
		// Let's connect to the jukebox so we can return status
		$jukebox = new slim($jbArr[$_SESSION['jb_id']]['server'],$jbArr[$_SESSION['jb_id']]['port']);
		// Now let's get the player status
		if ($jukebox){
			return true;
		} else {
			return false;
		}
	}
	
	/**
	* Returns Addon tools for MPD - namely refresh jukebox database
	* 
	* @author Ross Carlson
	* @version 4/9/05
	* @since 4/9/05
	* @param return Returns a link to refresh the MPD database
	*/
	function getAllAddOnTools(){
		return;
	}
	
	/**
	* Returns the currently playing tracks path so we can get the node
	* 
	* @author Ross Carlson
	* @version 4/9/05
	* @since 4/9/05
	* @param return Returns the currently playling track's path
	*/
	function getCurTrackPath(){
		global $jbArr;
		
		// let's connect to the player
		$jukebox = new slim($jbArr[$_SESSION['jb_id']]['server'],$jbArr[$_SESSION['jb_id']]['port']);
		
		// Now let's make sure it's playing
		if (trim(str_replace("playlist tracks ","",$jukebox->execute("playlist tracks ?"))) == 0){
			return;
		}
		
		return $jukebox->get("curtrackpath");
	}
	
	/**
	* Returns the currently playing track number
	* 
	* @author Ross Carlson
	* @version 4/9/05
	* @since 4/9/05
	* @param return Returns the currently playling track number
	*/
	function getCurPlayingTrack(){
		global $jbArr;

		// let's connect to the player
		$jukebox = new slim($jbArr[$_SESSION['jb_id']]['server'],$jbArr[$_SESSION['jb_id']]['port']);
		
		// Now let's make sure it's playing
		if (trim(str_replace("playlist tracks ","",$jukebox->execute("playlist tracks ?"))) == 0){
			return;
		}
		
		return $jukebox->get("curtracknum");
	}
	
	/**
	* Returns the currently playing playlist
	* 
	* @author Ross Carlson
	* @version 4/9/05
	* @since 4/9/05
	* @param return Returns the currently playling playlist
	*/
	function getCurPlaylist($fullPath = false){
		global $jbArr;

		// let's connect to the player
		$jukebox = new slim($jbArr[$_SESSION['jb_id']]['server'],$jbArr[$_SESSION['jb_id']]['port']);

		// Now let's make sure it's playing
		if (trim(str_replace("playlist tracks ","",$jukebox->execute("playlist tracks ?"))) == 0){
			return;
		}

		return $jukebox->get("playlist", $fullPath);
	}

	/**
	* Passes a playlist to the jukebox player
	* 
	* @author Ross Carlson
	* @version 4/9/05
	* @since 4/9/05
	* @param $playlist The playlist that we are passing
	*/
	function playlist($playlist){
		global $include_path, $jbArr, $media_dirs,$jzSERVICES;
		
		$playlist = $jzSERVICES->createPlaylist($playlist,"jukebox");
		// let's connect to the player
		$jukebox = new slim($jbArr[$_SESSION['jb_id']]['server'],$jbArr[$_SESSION['jb_id']]['port']);
		
		// Let's get where we are in the current list
		$curTrack = getCurPlayingTrack();
		
		// Now let's get the full current playlist
		$curList = getCurPlaylist(true);
		
		// Ok, now we need to figure out where to add the stuff
		if ($_SESSION['jb-addtype'] == "current"){
			// Ok, let's split our first playlist in 2 so we can add in the middle
			$begArr = @array_slice($curList,0,$curTrack+1);
			$endArr = @array_slice($curList,$curTrack+1);
		} else if ($_SESSION['jb-addtype'] == "begin"){
			$begArr = "";
			$endArr = array();
		} else if ($_SESSION['jb-addtype'] == "end"){
			$begArr = $curList;
			$endArr = array();
		}  else if ($_SESSION['jb-addtype'] == "replace") {
		    $begArr = array();
		    $endArr = array();
		} else if ($_SESSION['jb-addtype'] == "replace") {
		    $begArr = array();
		    $endArr = array();
		}
		
		// Now let's build the new playlist
		$prev=false;
		$curPlaylist = explode("\n",$playlist);
		if (is_array($begArr) and is_array($endArr)){
			$newList = array_merge($begArr,$curPlaylist,$endArr);
		} else {
			$prev=true;
			$newList = explode("\n",$playlist);
		}
		
		$playlist="";
		foreach($newList as $item){
			if ($item <> ""){
				$playlist .= str_replace("/","\\",$item). "\n";
			}
		}
		
		// Let's stop the jukebox
		control("stop",false);

		// let's figure out the filename
		$fname = "__". str_replace(":","_",$jukebox->info['mac']. ".m3u");
		
		$pl=false;
		// now let's find where it is
		$mDirs = explode("|",$media_dirs);
		foreach ($mDirs as $dir){
			if (is_file($dir. "/". $fname)){
				$pl = $dir. "/". $fname;
			}
		}

		$handle = fopen($pl, "w");
		fwrite($handle,$playlist);				
		fclose($handle);	

		// Now let's play it
		$jukebox->execute("playlist insert ". $fname);
		
		control("play");
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
	* @version 4/9/05
	* @since 4/9/05
	* @param $command The command that we passed to the player
	* @param $goBack Should we go back after executing (default is true)
	*/
	function control($command, $goBack = true){
		global $jbArr;
		
		// let's connect to the player
		$jukebox = new slim($jbArr[$_SESSION['jb_id']]['server'],$jbArr[$_SESSION['jb_id']]['port']);

		// Now let's execute the command
		switch ($command){
			case "play":
				$jukebox->execute("button play");
			break;
			case "stop":
				$jukebox->execute("button stop");
			break;
			case "pause":
				$jukebox->execute("button pause");
			break;
			case "previous":
				$jukebox->execute("button rew");
			break;
			case "next":
				$jukebox->execute("button fwd");
			break;
			case "volume":
				$jukebox->execute("mixer volume ". $_POST['jbvol']);
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
				$jukebox->execute("playlist index ". $_POST['jbjumpto']);
			break;
			case "clear":
				$jukebox->execute("playlist clear");
			break;
			case "random_play":
				return; // TODO HERE
			break;
			case "addwhere":
				$_SESSION['jb-addtype'] = $_POST['addplat'];
			break;
		}
		if ($goBack){
			?>
			<script>
				history.back();
			</script>
			<?php
			exit();
		}
	}
	
	/**
	* Returns the players current status
	* 
	* @author Ross Carlson
	* @version 4/9/05
	* @since 4/9/05
	*/
	function getStatus(){
		global $jbArr;
		
		// let's connect to the player
		$jukebox = new slim($jbArr[$_SESSION['jb_id']]['server'],$jbArr[$_SESSION['jb_id']]['port']);
		
		$status = substr($jukebox->info['status'],strpos($jukebox->info['status'],"mode:")+5);
		$status = substr($status,0,strpos($status," "));

		switch ($status){
			case "play":
				return "playing";
			break;
			case "stop":
				return "stopped";
			break;
			case "pause":
				return "paused";
			break;
		}
	}
	
	/**
	* Returns the current playing track
	* 
	* @author Ross Carlson
	* @version 4/9/05
	* @since 4/9/05
	* @return Returns the name of the current playing track
	*/
	function getCurTrackName(){
		global $jbArr;
		
		// let's connect to the player
		$jukebox = new slim($jbArr[$_SESSION['jb_id']]['server'],$jbArr[$_SESSION['jb_id']]['port']);
		
		// Now let's make sure it's playing
		if (trim(str_replace("playlist tracks ","",$jukebox->execute("playlist tracks ?"))) == 0){
			return;
		}
		
		// Ok, let's return the track
		return $jukebox->get("curtrack");
	}
	
	/**
	* Returns how long is left in the current track (in seconds)
	* 
	* @author Ross Carlson
	* @version 4/9/05
	* @since 4/9/05
	* @return Returns the name of the current playing track
	*/
	function getCurTrackRemaining(){
		global $jbArr;

		$length = getCurTrackLength();
		$cur = getCurTrackLocation();

		return (($length - $cur) + 2);
	}
	
	/**
	* Gets the length of the current track
	* 
	* @author Ross Carlson
	* @version 4/9/05
	* @since 4/9/05
	* @param return returns the amount of time remaining in seconds
	*/
	function getCurTrackLength(){
		global $jbArr;
		
		// let's connect to the player
		$jukebox = new slim($jbArr[$_SESSION['jb_id']]['server'],$jbArr[$_SESSION['jb_id']]['port']);
		
		// Now let's make sure it's playing
		if (trim(str_replace("playlist tracks ","",$jukebox->execute("playlist tracks ?"))) == 0){
			return;
		}
		
		return $jukebox->get("curlength");
	}
	
	/**
	* Returns how long is left in the current track (in seconds)
	* 
	* @author Ross Carlson
	* @version 4/9/05
	* @since 4/9/05
	* @return Returns the name of the current playing track
	*/
	function getCurTrackLocation(){
		global $jbArr;
		
		// let's connect to the player
		$jukebox = new slim($jbArr[$_SESSION['jb_id']]['server'],$jbArr[$_SESSION['jb_id']]['port']);
		
		// Now let's make sure it's playing
		if (trim(str_replace("playlist tracks ","",$jukebox->execute("playlist tracks ?"))) == 0){
			return;
		}

		return (getCurTrackLength() - $jukebox->get("remaining"));
	}
	
	class slim {
		
		/**
		 * Object contructor
		 *
		 * @since 4.7.2005
		 * @author Ross Carlson <ross@jinzora.com>
		 */
		function slim($host, $port) {
		
			// Let's set the play info
			$this->info['host'] = $host;
			$this->info['port'] = $port;
			 
			if ($this->info['socket'] = @fsockopen($this->info['host'], $this->info['port'], $errno, $errstr, .5)){
				// Lets set the query to get the MAC address
				$query = 'player id 0 ?';
				$data = $this->execute($query);
				$mac = trim(str_replace("player id 0 ","",$data));
				$this->info['mac'] = $mac;
				
				// now let's get the players current status
				$this->info['status'] = $this->get("status");
				
				// now let's return
				return true;
			} else {
				// Ok, we couldn't connect, return false
				return false;
			}
		}
		
		
		/**
		* Gets a specific piece of data from the player
		*
		* @since 4.10.2005
		* @author Ross Carlson <ross@jinzora.com>
		* @param $item string The item that we need info for
		*/
		function get($item, $option = false){
			global $media_dirs;
			
			switch ($item){
				case "status":
					return $this->execute("status");
				break;
				case "curlength":
					// Ok, let's get the current track
					$duration = trim(substr($this->info['status'],strpos($this->info['status'],"duration:")+9));
					return round(substr($duration,0,strpos($duration," ")));
				break;
				case "remaining":
					// TODO fix this...
					// Ok, let's get the current track
					$time = trim(substr($this->info['status'],strpos($this->info['status'],"time:")+5));
					$time = round(substr($time,0,strpos($time," ")));
					$duration = $this->get("curlength");
					if ($time <> 0){
						return $duration - $time;
					} else {
						return 0;
					}	
				break;
				case "curtracknum":
					return trim(str_replace("playlist index ","",$this->execute("playlist index ?")));
				break;
				case "curtrack":
					$curnumber = $this->get("curtracknum");
					return trim(str_replace("playlist title ". $curnumber,"",$this->execute("playlist title ". $curnumber. " ?")));
				break;
				case "nexttrack":
					$nextNum = $this->get("curtracknum") + 1;
					return trim(str_replace("playlist title ". $nextNum,"",$this->execute("playlist title ". $nextNum. " ?")));
				break;
				case "curtrackpath":
					$curnumber = $this->get("curtracknum");
					$path = trim(str_replace("playlist path ". $curnumber,"",$this->execute("playlist path ". $curnumber. " ?")));
					$path = strtolower(str_replace("file:///","",$path));
					// Now we need to strip all the media dirs
					$mDirs = explode("|",$media_dirs);
					foreach ($mDirs as $dir){
						$path = str_replace(strtolower($dir),"",$path);
					}
					return $path;
				break;
				case "playlist":
					// what should the name be?
					$playlist = "__". str_replace(":","_",$this->info['mac']. ".m3u");
					$pl=false;
					// now let's find where it is
					$mDirs = explode("|",$media_dirs);
					foreach ($mDirs as $dir){
						if (is_file($dir. "/". $playlist)){
							$pl = $dir. "/". $playlist;
						}
					}
					// Did we get a list?
					if ($pl){
						$data = str_replace("﻿","",file($pl));
						if (!$option){
							// Ok, now let's fix up the paths to the tracks
							$mDirs = explode("|",$media_dirs);
							foreach($data as $track){
								$track = strtolower(str_replace("\\","/",$track));
								foreach ($mDirs as $dir){
									$track = str_replace(strtolower($dir). "/","",$track);
								}
								$trArr = explode("/",$track);
								$tArr = explode(".",ucwords($trArr[count($trArr)-1]));
								$t[] = $tArr[0];
							}
							return $t;
						} else {
							return $data;
						}
					} else {
						return false;
					}
				break;
			}
		}
		
		/**
		* Executes a given command and retruns the results
		*
		* @since 4.10.2005
		* @author Ross Carlson <ross@jinzora.com>
		* @param $command string The command to be executed
		*/
		function execute($command){
			// let's connect and send the command
			fputs($this->info['socket'], $command."\n");
			
			// let's get the results back
			$buff = fgets($this->info['socket']);
			
			// Let's clean them up
			$buff = urldecode(substr($buff, 0, strlen($buff)-1));
			$buff = str_replace('%20',' ',$buff);
			
			// Now let's return
			return $buff;
		}
	}
?>