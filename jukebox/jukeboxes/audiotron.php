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
	* Contains the Audiontron Jukebox Interface code
	*
	* @since 2/9/05
	* @author Ross Carlson <ross@jinzora.org>
	*/
	
	/*
	
	NOTES FOR THIS JUKEBOX
	
	This Jukebox requires the following settings:
	
	server
	port
	password
	description
	type
	
	An example would be:
	$jbArr[0]['server'] = "localhost";
	$jbArr[0]['port'] = "4800";
	$jbArr[0]['password'] = "jinzora";
	$jbArr[0]['description'] = "Audiotron";
	$jbArr[0]['type'] = "winamp"; // VERY IMPORTANT
	$jbArr[0]['mediaserver'] = "SERVERNAME";
	$jbArr[0]['mediashare'] = "SHARENAME";

	*/	
	
	// Right off the bat we need to clear the status session var before the functions run
	$_SESSION['jz_audiotron_status'] = "";
	
	/**
	* Returns the connection status of the player true or false
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param return Returns true or false
	*/
	function playerConnect(){
		$status = getATStatus(true);
		if ($status == ""){
			// Let's try again
			usleep(5000);
			$status = getATStatus(true);
			if ($status == ""){
				return false;
			}
		}
		return true;
	}
	
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
		
		// First let's get the full status from the player
		$status = getATStatus();
		
		// Now let's get the location
		$cur = substr($status,strpos($status,"SourceID=")+strlen("SourceID="));
		$cur = substr($cur,0,strpos($cur,"\n"));
		$cur = str_replace("\\\\". $jbArr[$_SESSION['jb_id']]['mediaserver']. "\\". $jbArr[$_SESSION['jb_id']]['mediashare']. "\\","",$cur);
		$cur = str_replace("\\","/",$cur);

		return $cur;
	}
	
	/**
	* Returns the currently playing track number
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param return Returns the currently playling track number
	*/
	function getCurPlayingTrack(){
		global $jbArr;
		
		// First let's get the full status from the player
		$status = getATStatus();
		
		// Now let's get the location
		$cur = substr($status,strpos($status,"CurrIndex=")+strlen("CurrIndex="));
		$cur = substr($cur,0,strpos($cur,"\n"));
		
		return $cur;
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
		global $jbArr;
		
		// Let's get the full playing list - we'll create a session variable for this since it can be HUGE and take FOREVER
		if (!isset($_SESSION['jb_at_playlist'])){
			$_SESSION['jb_at_playlist'] = file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. "/apigetinfo.asp?type=playq");
		}
		$list = $_SESSION['jb_at_playlist'];
		
		// Now let's break that into an array so we can process it
		$valArray = explode("[Song",$list);
		for ($i=0; $i < count($valArray); $i++){
			if (!stristr($valArray[$i], "[Play Queue]")){
				// Now let's get just the path out of this
				$item = substr($valArray[$i],strpos($valArray[$i],"ID=")+strlen("ID="));
				$item = substr($item,0,strpos($item,"\n"));
				$item = str_replace("\\\\". $jbArr[$_SESSION['jb_id']]['mediaserver']. "\\". $jbArr[$_SESSION['jb_id']]['mediashare']. "\\","",$item);
				$item = str_replace("\\","/",$item);
				$retArray[] = $item;
			}
		}
		return $retArray;
	}

	/**
	* Passes a playlist to the jukebox player
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param $playlist The playlist that we are passing
	*/
	function playlist($playlist){
		global $include_path, $jbArr,$jzSERVICES;
		
		$playlist = $jzSERVICES->createPlaylist($playlist,"jukebox");
		// First we need to get the current playlist so we can figure out where to add
		$curList = getCurPlaylist();
		
		// Let's get where we are in the current list
		$curTrack = getCurPlayingTrack();
		
		// Ok, now we need to figure out where to add the stuff
		if ($_SESSION['jb-addtype'] == "current"){
			// Ok, let's split our first playlist in 2 so we can add in the middle
			$begArr = array_slice($curList,0,$curTrack+1);
			$endArr = array_slice($curList,$curTrack+1);
		} else if ($_SESSION['jb-addtype'] == "begin"){
			$begArr = "";
			$endArr = array();
		} else if ($_SESSION['jb-addtype'] == "end"){
			$begArr = $curList;
			$endArr = array();
		} else if ($_SESSION['jb-addtype'] == "replace") {
		  $begArr = array();
		  $endArr = array();
		}
		
		// Now let's send the new playlist to the player
		$f=false;$data="";
		for ($i=0; $i < count($begArr); $i++){
			// Now let's add this
			if ($begArr[$i] <> ""){
				$val = "\\\\". $jbArr[$_SESSION['jb_id']]['mediaserver']. "\\". $jbArr[$_SESSION['jb_id']]['mediashare']. "\\". str_replace($jbArr[$_SESSION['jb_id']]['localpath'],"",str_replace("/","\\",$begArr[$i]));
				//echo $val. "<br>";
				if ($f){$val = "\r\n". trim($val);}
				$f=true;
				$data .= $val;
			}
		}		
		// Ok, Now let's add the new stuff
		$pArray = explode("\n",$playlist);
		for ($i=0; $i < count($pArray); $i++){
			if ($pArray[$i] <> ""){
				// Now let's add this
				$val = "\\\\". $jbArr[$_SESSION['jb_id']]['mediaserver']. "\\". $jbArr[$_SESSION['jb_id']]['mediashare']. str_replace($jbArr[$_SESSION['jb_id']]['localpath'],"",str_replace("/","\\",$pArray[$i]));
				//echo $val. "<br>";
				if ($f){$val = "\r\n". trim($val);}
				$f=true;
				$data .= $val;
			}
		}
		// Now let's finish this out
		for ($i=0; $i < count($endArr); $i++){
			if ($endArr[$i] <> ""){
				// Now let's add this
				$val = "\\\\". $jbArr[$_SESSION['jb_id']]['mediaserver']. "\\". $jbArr[$_SESSION['jb_id']]['mediashare']. "\\". str_replace($jbArr[$_SESSION['jb_id']]['localpath'],"",str_replace("/","\\",$endArr[$i]));
				//echo $val. "<br>";
				if ($f){$val = "\r\n". trim($val);}
				$f=true;
				$data .= $val;
			}
		}

		// Now let's clear the current list
		control("clear", false);
		usleep(500);
		
		$fileName = $jbArr[$_SESSION['jb_id']]['localpath']. "/". $jbArr[$_SESSION['jb_id']]['playlistname'];
		$handle = fopen($fileName, "w");
		fwrite($handle,$data);	
		fclose ($handle);
		
		// Ok, now we need to tell the audiotron to play the M3U file
		$plName = "\\\\". $jbArr[0]['mediaserver']. "\\". $jbArr[0]['mediashare']. "\\". $jbArr[0]['playlistname'];
		
		// Now let's play then load
		file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. "/apicmd.asp?cmd=stop");
		file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. "/apicmd.asp?cmd=clear");
		unset($_SESSION['jb_at_playlist']);
		file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. "/apiqfile.asp?type=file&file=". $plName);
		usleep(500);
		file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. "/apicmd.asp?cmd=play");
		usleep(500);
		// Ok, first we need to know what track number we are on
		$c=0;
		while ($c<$curTrack+1){
			file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. "/apicmd.asp?cmd=next");
			usleep(500);
			$c++;
		}	
		?>
		<script>
			history.back();
		</script>
		<?php
		return;
	}
		
	/**
	* Passes a command to the jukebox player
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param $command The command that we passed to the player
	*/
	function control($command, $goBack = true){
		global $jbArr;

		// Now let's execute the command
		switch ($command){
			case "play":
				file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. "/apicmd.asp?cmd=play");
			break;
			case "stop":
				unset($_SESSION['jb_at_playlist']);
				file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. "/apicmd.asp?cmd=stop");
			break;
			case "pause":
				file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. "/apicmd.asp?cmd=pause");
			break;
			case "previous":
				file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. "/apicmd.asp?cmd=prev");
			break;
			case "next":
				file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. "/apicmd.asp?cmd=next");
			break;
			case "volume":
				// Now we have to set the value based on 0-255
				$vol = $_POST['jbvol'];
				file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. "/apicmd.asp?cmd=volume&arg=". $vol);
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
				// Ok, first we need to know what track number we are on
				$cur = getCurPlayingTrack();
				// Now where to jump to
				$pos = $_POST['jbjumpto'];
				// Now how many is that?
				$num = $pos - $cur;
				// Now let's jump that many
				if ($num > 0){
					$c=0;
					while ($c<$num){
						file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. "/apicmd.asp?cmd=next");
						usleep(500);
						$c++;
					}
				} else {
					$c=0;
					while ($c>$num-1){
						file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. "/apicmd.asp?cmd=prev");
						usleep(500);
						$c=$c-1;
					}
				}
			break;
			case "clear":
				file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. "/apicmd.asp?cmd=stop");
				file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. "/apicmd.asp?cmd=clear");
				unset($_SESSION['jb_at_playlist']);
			break;
			case "random_play":
				file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. "/apicmd.asp?cmd=random");
			break;
		}
		usleep(50);
		if ($goBack){
			?>
			<script>
				history.back();
			</script>
			<?php
		}
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
		
		// First let's get the full status from the player
		$status = getATStatus();		
		$status = substr($status,strpos($status,"State=")+strlen("State="));
		$status = trim(substr($status,0,strpos($status,"\n")));

		switch ($status){
			case "Playing":
				return "playing";
			break;
			case "Inactive":
				return "stopped";
			break;
			case "Paused":
				return "paused";
			break;
		}
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
		
		// First let's get the full status from the player
		$status = getATStatus();
		if (stristr($status,"State=Inactive")){
			$cur = "";
		} else {
			// Now let's get the location
			$cur = substr($status,strpos($status,"Title=")+strlen("Title="));
			$cur = substr($cur,0,strpos($cur,"\n"));
		}
		
		return $cur;
	}
	
	/**
	* Returns how long is left in the current track (in seconds)
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @return Returns the name of the current playing track
	*/
	function getCurTrackLocation(){
		global $jbArr;
		
		// First let's get the full status from the player
		$status = getATStatus();
		
		// Now let's get the location
		$cur = substr($status,strpos($status,"CurrPlayTime=")+strlen("CurrPlayTime="));
		$cur = substr($cur,0,strpos($cur,"\n"));
		
		// Now let's return
		return $cur;
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
		
		// First let's get the full status from the player
		$status = getATStatus();
		
		// Now let's get the location
		$cur = getCurTrackLocation();
		
		// Now let's get the length
		$length = getCurTrackLength();
		$remain = ($length - $cur);
		
		// Now let's return
		return $remain;
	}
	
	/**
	* Gets the length of the current track
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param return returns the amount of time remaining in seconds
	*/
	function getCurTrackLength(){
		global $jbArr;
		
		// First let's get the full status from the player
		$status = getATStatus();
		
		// Now let's get the length
		$length = substr($status,strpos($status,"TotalTime=")+strlen("TotalTime="));
		$length = substr($length,0,strpos($length,"\n"));
		
		return $length;
		
		// Now let's get the location
		$cur = substr($status,strpos($status,"CurrPlayTime=")+strlen("CurrPlayTime="));
		$cur = substr($cur,0,strpos($cur,"\n"));
		
		return $cur;
	}
	
	/**
	* Gets the current status from the Audiotron
	* We use this so that we would only make 1 request to the audiotron per pass
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param $force Should we FORCE an update (defaults to false)
	* @return Returns the name of the current playing track
	*/
	function getATStatus($force = false){
		global $jbArr;
		
		usleep(500);
		if ($_SESSION['jz_audiotron_status'] == "" or $force){
			$_SESSION['jz_audiotron_status'] = @file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['username']. ":". $jbArr[$_SESSION['jb_id']]['password']. "@". $jbArr[$_SESSION['jb_id']]['server']. "/apigetstatus.asp");
		}
		return $_SESSION['jz_audiotron_status'];
	}
?>