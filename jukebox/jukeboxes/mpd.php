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
	$jbArr[1]['server'] = "localhost";
	$jbArr[1]['port'] = "6600";
	$jbArr[1]['description'] = "MPD Player";
	$jbArr[1]['password'] = "PASS";
	$jbArr[1]['type'] = "mpd";
	
	*/	
	
	
	
	

	/**
	* The installer function for this jukebox
	* 
	* @author Ross Carlson
	* @version 11/20/05
	* @since 11/20/05
	* @param $step int The step of the install process we are on
	*/
	function jbInstall($step){
		global $jbArr;

		echo "<strong>MPD Jukebox Installer</strong><br><br>";
		
		// Now which step are we on?
		switch($step){
			case "2":
				// Now let's create the step 2 page
				?>
				Please complete the following to setup MPD with Jinzora.<br><br>
				<strong>NOTE:</strong> MPD must already be running and have imported the same media folder(s)<br>that Jinzora has in order for MPD to function properly with Jinzora.<br><br>
				<form method="post">
					<table>
						<tr>
							<td>
								Server:
							</td>
							<td>
								<input type="text" value="localhost" name="edit_server" class="jz_input">
							</td>
						</tr>
						<tr>
							<td>
								Port:
							</td>
							<td>
								<input type="text" value="6600" name="edit_port" class="jz_input">
							</td>
						</tr>
						<tr>
							<td>
								Password:
							</td>
							<td>
								<input type="password" value="" name="edit_password" class="jz_input"> (optional)
							</td>
						</tr>
						<tr>
							<td>
								Name:
							</td>
							<td>
								<input type="text" value="MPD Player" name="edit_description" class="jz_input">
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<br>
								<input type="submit" value="Test Connection and Write Settings" name="edit_finish" class="jz_submit">
								<br><br>
							</td>
						</tr>
					</table>
					<input type="hidden" name="edit_step" value="3">
					<input type="hidden" name="edit_jukebox_type" value="mpd">					
				</form>
				<?php
				exit();
			break;
			case "3":
				// Let's test the connection to the player
				// First let's set all the variables for it
				$jbArr[0]['server'] = $_POST['edit_server'];
				$jbArr[0]['port'] = $_POST['edit_port'];
				$jbArr[0]['description'] = $_POST['edit_description'];
				$jbArr[0]['password'] = $_POST['edit_password'];
				$jbArr[0]['type'] = "mpd";
				$_SESSION['jb_id'] = 0;
				
				echo "Testing connection to MPD...<br>";
				if (playerConnect()){
					echo word("Success! Please wait while we write the settings...")."<br><br>";
					flushdisplay();
					sleep(1);
				} else {
					echo word("Failed!  Jinzora had an issue communicating with MPD, ensure that it's running and that you've specified the proper settings");
					exit();
				}
				
				// Ok, let's create the settings file
				$content  = "<?". "php\n";
				$content .= "$". "jbArr[0]['server'] = '". $_POST['edit_server']. "';\n";
				$content .= "$". "jbArr[0]['port'] = '". $_POST['edit_port']. "';\n";
				$content .= "$". "jbArr[0]['description'] = '". $_POST['edit_description']. "';\n";
				$content .= "$". "jbArr[0]['password'] = '". $_POST['edit_password']. "';\n";
				$content .= "$". "jbArr[0]['type'] = 'mpd';\n";
				$content .= "?>";
				
				// Now let's write it out IF we can
				$filename = getcwd(). "/jukebox/settings.php";
				if (is_writable($filename)){
					$handle = fopen($filename, "w");
					fwrite($handle,$content);
					fclose ($handle);
					?>
					<form method="post">
						<input type="submit" name="continue" value="Continue to the Jukebox interface" class="jz_submit"><br><br>
					</form>
					<?php
					exit();
				} else {
					echo 'It looks like your jukebox settings file at "'. $filename. '" is not writeable.<br>You must make it writeable to proceed!';
					echo '<br><br>If you are on Linux you can execute "chmod 666 '. $filename. '" at a shell to make it writeable.<br><br>';
					echo "Or copy and paste the below information into the file at: ". getcwd(). "/jukebox/settings.php<br><br>";
					echo str_replace("    ","&nbsp;&nbsp;&nbsp;&nbsp;",nl2br(str_replace("<?php", "&lt;php",$content)));
					echo "<br><br>";
					exit();
				}				
			break;
		}
	}

	/**
	* Returns a connection to the player
	* 
	* @author Martijn Pieters
	* @version 4/10/05
	* @since 4/10/05
	* @param return Returns mpd instance
	*/
	function & _mpdConnection(){
		global $jbArr;

		if (isset($jbArr[$_SESSION['jb_id']]['password']) && $jbArr[$_SESSION['jb_id']]['password'] != "") {
			$r = &new mpd($jbArr[$_SESSION['jb_id']]['server'],$jbArr[$_SESSION['jb_id']]['port'],$jbArr[$_SESSION['jb_id']]['password']);
			return $r;
		} else {
			$r = &new mpd($jbArr[$_SESSION['jb_id']]['server'],$jbArr[$_SESSION['jb_id']]['port']);
			return $r;
		}
	}
	
	/**
	* Returns the stats of the jukebox
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
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
	* @version 2/9/05
	* @since 2/9/05
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
		$retArray['repeatbutton'] = true;
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
	* @version 2/9/05
	* @since 2/9/05
	* @param return Returns true or false
	*/
	function playerConnect(){
		global $jbArr;

		$myMpd = _mpdConnection();
		if ($myMpd->state == ""){
			return false;
		} else {
			return true;
		}
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
	
		$arr = array();
		$arr['action'] = "jukebox";
		$arr['subaction'] = "jukebox-command";
		$arr['command'] = "refreshdb";
		$arr['ptype'] = "jukebox";
						
		echo ' - <a href="#" onClick="sendJukeboxRequest(\'refreshdb\')">refresh MPD</a>';
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
		
		$myMpd = _mpdConnection();
		
		$num = getCurPlayingTrack();
		$pArray = $myMpd->GetPlaylist();
		return $pArray[$num]['file'];
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
		
		$myMpd = _mpdConnection();
		return $myMpd->current_track_id;
	}
		
	/**
	* Returns the currently playing playlist
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param return Returns the currently playling playlist
	* @param bolean Return FULL path only?
	*/
	function getCurPlaylist($path = false){
		global $jbArr;
		
		$myMpd = _mpdConnection();
		$retArray = array();
		if (!is_null($myMpd->playlist)){
			foreach ($myMpd->playlist as $id => $entry) {
				if ($path){
					$retArray[] = $entry['file'];
				} else {
					$retArray[] = $entry['Artist']." - ".$entry['Title'];				
				}
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
		global $include_path, $jbArr, $media_dirs,$jzSERVICES;
		
		$playlist = $jzSERVICES->createPlaylist($playlist,"jukebox");
		$myMpd = _mpdConnection();
		
		// Now let's get our current playlist and current position
		$curList = getCurPlaylist(true);
		$curTrack = getCurPlayingTrack();

		switch ($_SESSION['jb-addtype']) {
		case "end":
		case "current":
		  $restart_playback = false;
		  break;
		default:
		  $restart_playback = true;
		}

		// Ok, now we need to figure out where to add the stuff
		if ($_SESSION['jb-addtype'] == "current"){
			// Ok, let's split our first playlist in 2 so we can add in the middle
			//$begArr = array_slice($curList,0,$curTrack+1);
		  if (is_array($curList)) {
		    $begArr = array();
		    $endArr = array_slice($curList,$curTrack+1);
		  } else {
		    $begArr = array();
		    $endArr = array();
		  }
		} else if ($_SESSION['jb-addtype'] == "begin"){
			$begArr = array();
			$endArr = $curList;
		} else if ($_SESSION['jb-addtype'] == "end"){
			$begArr = array();
			$endArr = array();
		} else if ($_SESSION['jb-addtype'] == "replace") {
		  $begArr = array();
		  $endArr = array();
		}
		
		
		if ($restart_playback === true){
		  $myMpd->Stop();
		  $myMpd->PLClear();
		} else if ($_SESSION['jb-addtype'] == "current") {
		  // Remove everything at the end of the playlist, since we are going to readd it all.
		  for ($i = sizeof($curList); $i > $curTrack; $i--) {
		    $myMpd->PLRemove($i);
		  }
		}

		// Now let's send the new playlist to the player
		for ($i=0; $i < count($begArr); $i++){
			// Now let's add this
			if ($begArr[$i] <> ''){
				$myMpd->PLAdd($begArr[$i]);
			}
		}		
		// Ok, Now let's add the new stuff
		$pArray = explode("\n",$playlist);
		for ($i=0; $i < count($pArray); $i++){
			if ($pArray[$i] <> ""){
				// Now let's clean up the paths so we can add the media
				$mArr = explode("|",$media_dirs);
				$track = trim($pArray[$i]);
				for ($e=0; $e < count($mArr); $e++){
					$track = trim(str_replace($mArr[$e]. "/","",$track));
				}
				$myMpd->PLAdd($track);
			}
		}
		// Now let's finish this out
		for ($i=0; $i < count($endArr); $i++){
			// Now let's add this
			if ($endArr[$i] <> ''){
				$myMpd->PLAdd($endArr[$i]);
			}
		}

		// Now let's jump to where we need to play
		switch ($_SESSION['jb-addtype']){
			case "current":
				if ($curTrack == 0){$curTrack = -1;}
				$_POST['jbjumpto'] = $curTrack + 1;
			break;
			case "end":
				$_POST['jbjumpto'] = $curTrack;
			break;
		case "replace":
		case "begin":
				$_POST['jbjumpto'] = 0;
			break;
		}
		if ($restart_playback) {
		  control("jumpto");
		}
		control("play");

		if (defined('NO_AJAX_JUKEBOX')) {
		?>
		<script>
			history.back();
		</script>
		<?php 
		    }
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
		$myMpd = _mpdConnection();
		// Now let's execute the command
		switch ($command){
			case "play":
				$myMpd->Play();
			break;
			case "stop":
				$myMpd->Stop();
			break;
			case "pause":
				$myMpd->Pause();
			break;
			case "previous":
				$myMpd->Previous();
			break;
			case "next":
				$myMpd->Next();
			break;
			case "volume":
				$myMpd->SetVolume($_POST['jbvol']);
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
				$myMpd->SkipTo($_POST['jbjumpto']);
			break;
			case "clear":
				$myMpd->Stop();
				$myMpd->PLClear();
			break;
		        case "repeat":
		                $myMpd->setRepeat(1);
		        break;
		        case "no_repeat":
		                $myMpd->setRepeat(0);
		        break;
			case "random_play":
				$myMpd->Stop();
				$myMpd->PLShuffle();
				$myMpd->Play();
			break;
			case "refreshdb":
				$myMpd->DBRefresh();
			break;
			case "addwhere":
				$_SESSION['jb-addtype'] = $_POST['addplat'];
			break;
		}
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
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	*/
	function getStatus($type = "playback"){
		global $jbArr;
		
		$myMpd = _mpdConnection();
		switch ($type) {
		case "repeat":
		  return ($myMpd->repeat) ? true : false;
		  break;
		case "playback":
		  switch ($myMpd->state) {
		  case MPD_STATE_PLAYING: 
		    return "playing";
		    break;
		  case MPD_STATE_PAUSED:
		    return "paused";
		    break;
		  case MPD_STATE_STOPPED:
		    return "stopped";
		    break;
		  }
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
		
		$myMpd = _mpdConnection();
		if ($myMpd->current_track_id == -1) return false;
		return $myMpd->playlist[$myMpd->current_track_id]['Artist']." - ".$myMpd->playlist[$myMpd->current_track_id]['Title'];
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
		
		// Let's create our object
		$myMpd = _mpdConnection();
		
		// Let's return what's left
		return ($myMpd->current_track_length - getCurTrackLocation());
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
		
		// Let's create our object
		$myMpd = _mpdConnection();
		
		return $myMpd->current_track_length;
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
		
		// Let's create our object
		$myMpd = _mpdConnection();

		return $myMpd->current_track_position;
	}
	
	
	
	
	//
	//
	// The following code was obtained from http://mpd.24oz.com/download.html
	//
	//
	
	// Create common command definitions for MPD to use
define("MPD_CMD_STATUS",      "status");
define("MPD_CMD_STATISTICS",  "stats");
define("MPD_CMD_VOLUME",      "volume");
define("MPD_CMD_SETVOL",      "setvol");
define("MPD_CMD_PLAY",        "play");
define("MPD_CMD_STOP",        "stop");
define("MPD_CMD_PAUSE",       "pause");
define("MPD_CMD_NEXT",        "next");
define("MPD_CMD_PREV",        "previous");
define("MPD_CMD_PLLIST",      "playlistinfo");
define("MPD_CMD_PLADD",       "add");
define("MPD_CMD_PLREMOVE",    "delete");
define("MPD_CMD_PLCLEAR",     "clear");
define("MPD_CMD_PLSHUFFLE",   "shuffle");
define("MPD_CMD_PLLOAD",      "load");
define("MPD_CMD_PLSAVE",      "save");
define("MPD_CMD_KILL",        "kill");
define("MPD_CMD_REFRESH",     "update");
define("MPD_CMD_REPEAT",      "repeat");
define("MPD_CMD_LSDIR",       "lsinfo");
define("MPD_CMD_SEARCH",      "search");
define("MPD_CMD_START_BULK",  "command_list_begin");
define("MPD_CMD_END_BULK",    "command_list_end");
define("MPD_CMD_FIND",        "find");
define("MPD_CMD_RANDOM",      "random");
define("MPD_CMD_SEEK",        "seek");
define("MPD_CMD_PLSWAPTRACK", "swap");
define("MPD_CMD_PLMOVETRACK", "move");
define("MPD_CMD_PLMOVE",      "move"); // Don't know if this is right..
define("MPD_CMD_PASSWORD",    "password");
define("MPD_CMD_TABLE",       "list");

// Predefined MPD Response messages
define("MPD_RESPONSE_ERR", "ACK");
define("MPD_RESPONSE_OK",  "OK");

// MPD State Constants
define("MPD_STATE_PLAYING", "play");
define("MPD_STATE_STOPPED", "stop");
define("MPD_STATE_PAUSED",  "pause");

// MPD Searching Constants
define("MPD_SEARCH_ARTIST", "artist");
define("MPD_SEARCH_TITLE",  "title");
define("MPD_SEARCH_ALBUM",  "album");

// MPD Cache Tables
define("MPD_TBL_ARTIST","artist");
define("MPD_TBL_ALBUM","album");

class mpd {
	// TCP/Connection variables
	var $host;
	var $port;
    var $password;

	var $mpd_sock   = NULL;
	var $connected  = FALSE;

	// MPD Status variables
	var $mpd_version    = "(unknown)";

	var $state;
	var $current_track_position;
	var $current_track_length;
	var $current_track_id;
	var $volume;
	var $repeat;
	var $random;

	var $uptime;
	var $playtime;
	var $db_last_refreshed;
	var $num_songs_played;
	var $playlist_count;
	
	var $num_artists;
	var $num_albums;
	var $num_songs;
	
	var $playlist		= array();

	// Misc Other Vars	
	var $mpd_class_version = "1.2";

	var $debugging   = FALSE;    // Set to TRUE to turn extended debugging on.
	var $errStr      = "";       // Used for maintaining information about the last error message

	var $command_queue;          // The list of commands for bulk command sending

    // =================== BEGIN OBJECT METHODS ================

	/* mpd() : Constructor
	 * 
	 * Builds the MPD object, connects to the server, and refreshes all local object properties.
	 */
	function mpd($srv,$port,$pwd = NULL) {
		$this->host = $srv;
		$this->port = $port;
        $this->password = $pwd;

		$resp = $this->Connect();
		if ( is_null($resp) ) {
            $this->errStr = "Could not connect";
			return;
		} else {
			list ( $this->mpd_version ) = sscanf($resp, MPD_RESPONSE_OK . " MPD %s\n");
            if ( ! is_null($pwd) ) {
                if ( is_null($this->SendCommand(MPD_CMD_PASSWORD,$pwd)) ) {
                    $this->connected = FALSE;
                    return;  // bad password or command
                }
    			if ( is_null($this->RefreshInfo()) ) { // no read access -- might as well be disconnected!
                    $this->connected = FALSE;
                    $this->errStr = "Password supplied does not have read access";
                    return;
                }
            } else {
    			if ( is_null($this->RefreshInfo()) ) { // no read access -- might as well be disconnected!
                    $this->connected = FALSE;
                    $this->errStr = "Password required to access server";
                    return; 
                }
            }
		}
	}

	/* Connect()
	 * 
	 * Connects to the MPD server. 
     * 
	 * NOTE: This is called automatically upon object instantiation; you should not need to call this directly.
	 */
	function Connect() {
		if ( $this->debugging ) echo "mpd->Connect() / host: ".$this->host.", port: ".$this->port."\n";
		$this->mpd_sock = fsockopen($this->host,$this->port,$errNo,$errStr,10);
		if (!$this->mpd_sock) {
			$this->errStr = "Socket Error: $errStr ($errNo)";
			return NULL;
		} else {
			while(!feof($this->mpd_sock)) {
				$response =  fgets($this->mpd_sock,1024);
				if (strncmp(MPD_RESPONSE_OK,$response,strlen(MPD_RESPONSE_OK)) == 0) {
					$this->connected = TRUE;
					return $response;
					break;
				}
				if (strncmp(MPD_RESPONSE_ERR,$response,strlen(MPD_RESPONSE_ERR)) == 0) {
					$this->errStr = "Server responded with: $response";
					return NULL;
				}
			}
			// Generic response
			$this->errStr = "Connection not available";
			return NULL;
		}
	}

	/* SendCommand()
	 * 
	 * Sends a generic command to the MPD server. Several command constants are pre-defined for 
	 * use (see MPD_CMD_* constant definitions above). 
	 */
	function SendCommand($cmdStr,$arg1 = "",$arg2 = "") {
		if ( $this->debugging ) echo "mpd->SendCommand() / cmd: ".$cmdStr.", args: ".$arg1." ".$arg2."\n";
		if ( ! $this->connected ) {
			echo "mpd->SendCommand() / Error: Not connected\n";
		} else {
			// Clear out the error String
			$this->errStr = "";
			$respStr = "";

			// Check the command compatibility:
			if ( ! $this->_checkCompatibility($cmdStr) ) {
				return NULL;
			}

			if (strlen($arg1) > 0) $cmdStr .= " \"".utf8_encode($arg1)."\"";
			if (strlen($arg2) > 0) $cmdStr .= " \"".utf8_encode($arg2)."\"";
			fputs($this->mpd_sock,"$cmdStr\n");
			while(!feof($this->mpd_sock)) {
				$response = fgets($this->mpd_sock,1024);

				// An OK signals the end of transmission -- we'll ignore it
				if (strncmp(MPD_RESPONSE_OK,$response,strlen(MPD_RESPONSE_OK)) == 0) {
					break;
				}

				// An ERR signals the end of transmission with an error! Let's grab the single-line message.
				if (strncmp(MPD_RESPONSE_ERR,$response,strlen(MPD_RESPONSE_ERR)) == 0) {
					list ( $junk, $errTmp ) = split(MPD_RESPONSE_ERR . " ",$response );
					$this->errStr = strtok($errTmp,"\n");
				}

				if ( strlen($this->errStr) > 0 ) {
					return NULL;
				}

				// Build the response string
				$respStr .= $response;
			}
			if ( $this->debugging ) echo "mpd->SendCommand() / response: '".$respStr."'\n";
		}
		return $respStr;
	}

	/* QueueCommand() 
	 *
	 * Queues a generic command for later sending to the MPD server. The CommandQueue can hold 
	 * as many commands as needed, and are sent all at once, in the order they are queued, using 
	 * the SendCommandQueue() method. The syntax for queueing commands is identical to SendCommand(). 
     */
	function QueueCommand($cmdStr,$arg1 = "",$arg2 = "") {
		if ( $this->debugging ) echo "mpd->QueueCommand() / cmd: ".$cmdStr.", args: ".$arg1." ".$arg2."\n";
		if ( ! $this->connected ) {
			echo "mpd->QueueCommand() / Error: Not connected\n";
			return NULL;
		} else {
			if ( strlen($this->command_queue) == 0 ) {
				$this->command_queue = MPD_CMD_START_BULK . "\n";
			}
			if (strlen($arg1) > 0) $cmdStr .= " \"" . utf8_encode($arg1) . "\"";
			if (strlen($arg2) > 0) $cmdStr .= " \"" . utf8_encode($arg2) . "\"";

			$this->command_queue .= $cmdStr ."\n";

			if ( $this->debugging ) echo "mpd->QueueCommand() / return\n";
		}
		return TRUE;
	}

	/* SendCommandQueue() 
	 *
	 * Sends all commands in the Command Queue to the MPD server. See also QueueCommand().
     */
	function SendCommandQueue() {
		if ( $this->debugging ) echo "mpd->SendCommandQueue()\n";
		if ( ! $this->connected ) {
			echo "mpd->SendCommandQueue() / Error: Not connected\n";
			return NULL;
		} else {
			$this->command_queue .= MPD_CMD_END_BULK . "\n";
			if ( is_null($respStr = $this->SendCommand($this->command_queue)) ) {
				return NULL;
			} else {
				$this->command_queue = NULL;
				if ( $this->debugging ) echo "mpd->SendCommandQueue() / response: '".$respStr."'\n";
			}
		}
		return $respStr;
	}

	/* AdjustVolume() 
	 *
	 * Adjusts the mixer volume on the MPD by <modifier>, which can be a positive (volume increase),
	 * or negative (volume decrease) value. 
     */
	function AdjustVolume($modifier) {
		if ( $this->debugging ) echo "mpd->AdjustVolume()\n";
		if ( ! is_numeric($modifier) ) {
			$this->errStr = "AdjustVolume() : argument 1 must be a numeric value";
			return NULL;
		}

        $this->RefreshInfo();
        $newVol = $this->volume + $modifier;
        $ret = $this->SetVolume($newVol);

		if ( $this->debugging ) echo "mpd->AdjustVolume() / return\n";
		return $ret;
	}

	/* SetVolume() 
	 *
	 * Sets the mixer volume to <newVol>, which should be between 1 - 100.
     */
	function SetVolume($newVol) {
		if ( $this->debugging ) echo "mpd->SetVolume()\n";
		if ( ! is_numeric($newVol) ) {
			$this->errStr = "SetVolume() : argument 1 must be a numeric value";
			return NULL;
		}

        // Forcibly prevent out of range errors
		if ( $newVol < 0 )   $newVol = 0;
		if ( $newVol > 100 ) $newVol = 100;

        // If we're not compatible with SETVOL, we'll try adjusting using VOLUME
        if ( $this->_checkCompatibility(MPD_CMD_SETVOL) ) {
            if ( ! is_null($ret = $this->SendCommand(MPD_CMD_SETVOL,$newVol))) $this->volume = $newVol;
        } else {
    		$this->RefreshInfo();     // Get the latest volume
    		if ( is_null($this->volume) ) {
    			return NULL;
    		} else {
    			$modifier = ( $newVol - $this->volume );
                if ( ! is_null($ret = $this->SendCommand(MPD_CMD_VOLUME,$modifier))) $this->volume = $newVol;
    		}
        }

		if ( $this->debugging ) echo "mpd->SetVolume() / return\n";
		return $ret;
	}

	/* GetDir() 
	 * 
     * Retrieves a database directory listing of the <dir> directory and places the results into
	 * a multidimensional array. If no directory is specified, the directory listing is at the 
	 * base of the MPD music path. 
	 */
	function GetDir($dir = "") {
		if ( $this->debugging ) echo "mpd->GetDir()\n";
		$resp = $this->SendCommand(MPD_CMD_LSDIR,$dir);
		$dirlist = $this->_parseFileListResponse($resp);
		if ( $this->debugging ) echo "mpd->GetDir() / return ".print_r($dirlist)."\n";
		return $dirlist;
	}

	/* PLAdd() 
	 * 
     * Adds each track listed in a single-dimensional <trackArray>, which contains filenames 
	 * of tracks to add, to the end of the playlist. This is used to add many, many tracks to 
	 * the playlist in one swoop.
	 */
	function PLAddBulk($trackArray) {
		if ( $this->debugging ) echo "mpd->PLAddBulk()\n";
		$num_files = count($trackArray);
		for ( $i = 0; $i < $num_files; $i++ ) {
			$this->QueueCommand(MPD_CMD_PLADD,$trackArray[$i]);
		}
		$resp = $this->SendCommandQueue();
		$this->RefreshInfo();
		if ( $this->debugging ) echo "mpd->PLAddBulk() / return\n";
		return $resp;
	}

	/* PLAdd() 
	 * 
	 * Adds the file <file> to the end of the playlist. <file> must be a track in the MPD database. 
	 */
	function PLAdd($fileName) {
		if ( $this->debugging ) echo "mpd->PLAdd()\n";
		if ( ! is_null($resp = $this->SendCommand(MPD_CMD_PLADD,$fileName))) $this->RefreshInfo();
		if ( $this->debugging ) echo "mpd->PLAdd() / return\n";
		return $resp;
	}

	/* PLMoveTrack() 
	 * 
	 * Moves track number <origPos> to position <newPos> in the playlist. This is used to reorder 
	 * the songs in the playlist.
	 */
	function PLMoveTrack($origPos, $newPos) {
		if ( $this->debugging ) echo "mpd->PLMoveTrack()\n";
		if ( ! is_numeric($origPos) ) {
			$this->errStr = "PLMoveTrack(): argument 1 must be numeric";
			return NULL;
		} 
		if ( $origPos < 0 or $origPos > $this->playlist_count ) {
			$this->errStr = "PLMoveTrack(): argument 1 out of range";
			return NULL;
		}
		if ( $newPos < 0 ) $newPos = 0;
		if ( $newPos > $this->playlist_count ) $newPos = $this->playlist_count;
		
		if ( ! is_null($resp = $this->SendCommand(MPD_CMD_PLMOVETRACK,$origPos,$newPos))) $this->RefreshInfo();
		if ( $this->debugging ) echo "mpd->PLMoveTrack() / return\n";
		return $resp;
	}

	/* PLShuffle() 
	 * 
	 * Randomly reorders the songs in the playlist.
	 */
	function PLShuffle() {
		if ( $this->debugging ) echo "mpd->PLShuffle()\n";
		if ( ! is_null($resp = $this->SendCommand(MPD_CMD_PLSHUFFLE))) $this->RefreshInfo();
		if ( $this->debugging ) echo "mpd->PLShuffle() / return\n";
		return $resp;
	}

	/* PLLoad() 
	 * 
	 * Retrieves the playlist from <file>.m3u and loads it into the current playlist. 
	 */
	function PLLoad($file) {
		if ( $this->debugging ) echo "mpd->PLLoad()\n";
		if ( ! is_null($resp = $this->SendCommand(MPD_CMD_PLLOAD,$file))) $this->RefreshInfo();
		if ( $this->debugging ) echo "mpd->PLLoad() / return\n";
		return $resp;
	}

	/* PLSave() 
	 * 
	 * Saves the playlist to <file>.m3u for later retrieval. The file is saved in the MPD playlist
	 * directory.
	 */
	function PLSave($file) {
		if ( $this->debugging ) echo "mpd->PLSave()\n";
		$resp = $this->SendCommand(MPD_CMD_PLSAVE,$file);
		if ( $this->debugging ) echo "mpd->PLSave() / return\n";
		return $resp;
	}

	/* PLClear() 
	 * 
	 * Empties the playlist.
	 */
	function PLClear() {
		if ( $this->debugging ) echo "mpd->PLClear()\n";
		if ( ! is_null($resp = $this->SendCommand(MPD_CMD_PLCLEAR))) $this->RefreshInfo();
		if ( $this->debugging ) echo "mpd->PLClear() / return\n";
		return $resp;
	}

	/* PLRemove() 
	 * 
	 * Removes track <id> from the playlist.
	 */
	function PLRemove($id) {
		if ( $this->debugging ) echo "mpd->PLRemove()\n";
		if ( ! is_numeric($id) ) {
			$this->errStr = "PLRemove() : argument 1 must be a numeric value";
			return NULL;
		}
		if ( ! is_null($resp = $this->SendCommand(MPD_CMD_PLREMOVE,$id))) $this->RefreshInfo();
		if ( $this->debugging ) echo "mpd->PLRemove() / return\n";
		return $resp;
	}

	/* SetRepeat() 
	 * 
	 * Enables 'loop' mode -- tells MPD continually loop the playlist. The <repVal> parameter 
	 * is either 1 (on) or 0 (off).
	 */
	function SetRepeat($repVal) {
		if ( $this->debugging ) echo "mpd->SetRepeat()\n";
		$rpt = $this->SendCommand(MPD_CMD_REPEAT,$repVal);
		$this->repeat = $repVal;
		if ( $this->debugging ) echo "mpd->SetRepeat() / return\n";
		return $rpt;
	}

	/* SetRandom() 
	 * 
	 * Enables 'randomize' mode -- tells MPD to play songs in the playlist in random order. The
	 * <rndVal> parameter is either 1 (on) or 0 (off).
	 */
	function SetRandom($rndVal) {
		if ( $this->debugging ) echo "mpd->SetRandom()\n";
		$resp = $this->SendCommand(MPD_CMD_RANDOM,$rndVal);
		$this->random = $rndVal;
		if ( $this->debugging ) echo "mpd->SetRandom() / return\n";
		return $resp;
	}

	/* Shutdown() 
	 * 
	 * Shuts down the MPD server (aka sends the KILL command). This closes the current connection, 
	 * and prevents future communication with the server. 
	 */
	function Shutdown() {
		if ( $this->debugging ) echo "mpd->Shutdown()\n";
		$resp = $this->SendCommand(MPD_CMD_SHUTDOWN);

		$this->connected = FALSE;
		unset($this->mpd_version);
		unset($this->errStr);
		unset($this->mpd_sock);

		if ( $this->debugging ) echo "mpd->Shutdown() / return\n";
		return $resp;
	}

	/* DBRefresh() 
	 * 
	 * Tells MPD to rescan the music directory for new tracks, and to refresh the Database. Tracks 
	 * cannot be played unless they are in the MPD database.
	 */
	function DBRefresh() {
		if ( $this->debugging ) echo "mpd->DBRefresh()\n";
		$resp = $this->SendCommand(MPD_CMD_REFRESH);
		
		// Update local variables
		$this->RefreshInfo();

		if ( $this->debugging ) echo "mpd->DBRefresh() / return\n";
		return $resp;
	}

	/* Play() 
	 * 
	 * Begins playing the songs in the MPD playlist. 
	 */
	function Play() {
		if ( $this->debugging ) echo "mpd->Play()\n";
		if ( ! is_null($rpt = $this->SendCommand(MPD_CMD_PLAY) )) $this->RefreshInfo();
		if ( $this->debugging ) echo "mpd->Play() / return\n";
		return $rpt;
	}

	/* Stop() 
	 * 
	 * Stops playing the MPD. 
	 */
	function Stop() {
		if ( $this->debugging ) echo "mpd->Stop()\n";
		if ( ! is_null($rpt = $this->SendCommand(MPD_CMD_STOP) )) $this->RefreshInfo();
		if ( $this->debugging ) echo "mpd->Stop() / return\n";
		return $rpt;
	}

	/* Pause() 
	 * 
	 * Toggles pausing on the MPD. Calling it once will pause the player, calling it again
	 * will unpause. 
	 */
	function Pause() {
		if ( $this->debugging ) echo "mpd->Pause()\n";
		if ( ! is_null($rpt = $this->SendCommand(MPD_CMD_PAUSE) )) $this->RefreshInfo();
		if ( $this->debugging ) echo "mpd->Pause() / return\n";
		return $rpt;
	}
	
	/* SeekTo() 
	 * 
	 * Skips directly to the <idx> song in the MPD playlist. 
	 */
	function SkipTo($idx) { 
		if ( $this->debugging ) echo "mpd->SkipTo()\n";
		if ( ! is_numeric($idx) ) {
			$this->errStr = "SkipTo() : argument 1 must be a numeric value";
			return NULL;
		}
		if ( ! is_null($rpt = $this->SendCommand(MPD_CMD_PLAY,$idx))) $this->RefreshInfo();
		if ( $this->debugging ) echo "mpd->SkipTo() / return\n";
		return $idx;
	}

	/* SeekTo() 
	 * 
	 * Skips directly to a given position within a track in the MPD playlist. The <pos> argument,
	 * given in seconds, is the track position to locate. The <track> argument, if supplied is
	 * the track number in the playlist. If <track> is not specified, the current track is assumed.
	 */
	function SeekTo($pos, $track = -1) { 
		if ( $this->debugging ) echo "mpd->SeekTo()\n";
		if ( ! is_numeric($pos) ) {
			$this->errStr = "SeekTo() : argument 1 must be a numeric value";
			return NULL;
		}
		if ( ! is_numeric($track) ) {
			$this->errStr = "SeekTo() : argument 2 must be a numeric value";
			return NULL;
		}
		if ( $track == -1 ) { 
			$track = $this->current_track_id;
		} 
		
		if ( ! is_null($rpt = $this->SendCommand(MPD_CMD_SEEK,$track,$pos))) $this->RefreshInfo();
		if ( $this->debugging ) echo "mpd->SeekTo() / return\n";
		return $pos;
	}

	/* Next() 
	 * 
	 * Skips to the next song in the MPD playlist. If not playing, returns an error. 
	 */
	function Next() {
		if ( $this->debugging ) echo "mpd->Next()\n";
		if ( ! is_null($rpt = $this->SendCommand(MPD_CMD_NEXT))) $this->RefreshInfo();
		if ( $this->debugging ) echo "mpd->Next() / return\n";
		return $rpt;
	}

	/* Previous() 
	 * 
	 * Skips to the previous song in the MPD playlist. If not playing, returns an error. 
	 */
	function Previous() {
		if ( $this->debugging ) echo "mpd->Previous()\n";
		if ( ! is_null($rpt = $this->SendCommand(MPD_CMD_PREV))) $this->RefreshInfo();
		if ( $this->debugging ) echo "mpd->Previous() / return\n";
		return $rpt;
	}
	
	/* Search() 
	 * 
     * Searches the MPD database. The search <type> should be one of the following: 
     *        MPD_SEARCH_ARTIST, MPD_SEARCH_TITLE, MPD_SEARCH_ALBUM
     * The search <string> is a case-insensitive locator string. Anything that contains 
	 * <string> will be returned in the results. 
	 */
	function Search($type,$string) {
		if ( $this->debugging ) echo "mpd->Search()\n";
		if ( $type != MPD_SEARCH_ARTIST and
	         $type != MPD_SEARCH_ALBUM and
			 $type != MPD_SEARCH_TITLE ) {
			$this->errStr = "mpd->Search(): invalid search type";
			return NULL;
		} else {
			if ( is_null($resp = $this->SendCommand(MPD_CMD_SEARCH,$type,$string)))	return NULL;
			$searchlist = $this->_parseFileListResponse($resp);
		}
		if ( $this->debugging ) echo "mpd->Search() / return ".print_r($searchlist)."\n";
		return $searchlist;
	}

	/* Find() 
	 * 
	 * Find() looks for exact matches in the MPD database. The find <type> should be one of 
	 * the following: 
     *         MPD_SEARCH_ARTIST, MPD_SEARCH_TITLE, MPD_SEARCH_ALBUM
     * The find <string> is a case-insensitive locator string. Anything that exactly matches 
	 * <string> will be returned in the results. 
	 */
	function Find($type,$string) {
		if ( $this->debugging ) echo "mpd->Find()\n";
		if ( $type != MPD_SEARCH_ARTIST and
	         $type != MPD_SEARCH_ALBUM and
			 $type != MPD_SEARCH_TITLE ) {
			$this->errStr = "mpd->Find(): invalid find type";
			return NULL;
		} else {
			if ( is_null($resp = $this->SendCommand(MPD_CMD_FIND,$type,$string)))	return NULL;
			$searchlist = $this->_parseFileListResponse($resp);
		}
		if ( $this->debugging ) echo "mpd->Find() / return ".print_r($searchlist)."\n";
		return $searchlist;
	}

	/* Disconnect() 
	 * 
	 * Closes the connection to the MPD server.
	 */
	function Disconnect() {
		if ( $this->debugging ) echo "mpd->Disconnect()\n";
		fclose($this->mpd_sock);

		$this->connected = FALSE;
		unset($this->mpd_version);
		unset($this->errStr);
		unset($this->mpd_sock);
	}

	/* GetArtists() 
	 * 
	 * Returns the list of artists in the database in an associative array.
	*/
	function GetArtists() {
		if ( $this->debugging ) echo "mpd->GetArtists()\n";
		if ( is_null($resp = $this->SendCommand(MPD_CMD_TABLE, MPD_TBL_ARTIST)))	return NULL;
        $arArray = array();
        
        $arLine = strtok($resp,"\n");
        $arName = "";
        $arCounter = -1;
        while ( $arLine ) {
            list ( $element, $value ) = split(": ",$arLine);
            if ( $element == "Artist" ) {
            	$arCounter++;
            	$arName = $value;
            	$arArray[$arCounter] = $arName;
            }

            $arLine = strtok("\n");
        }
		if ( $this->debugging ) echo "mpd->GetArtists()\n";
        return $arArray;
    }

    /* GetAlbums() 
	 * 
	 * Returns the list of albums in the database in an associative array. Optional parameter
     * is an artist Name which will list all albums by a particular artist.
	*/
	function GetAlbums( $ar = NULL) {
		if ( $this->debugging ) echo "mpd->GetAlbums()\n";
		if ( is_null($resp = $this->SendCommand(MPD_CMD_TABLE, MPD_TBL_ALBUM, $ar )))	return NULL;
        $alArray = array();

        $alLine = strtok($resp,"\n");
        $alName = "";
        $alCounter = -1;
        while ( $alLine ) {
            list ( $element, $value ) = split(": ",$alLine);
            if ( $element == "Album" ) {
            	$alCounter++;
            	$alName = $value;
            	$alArray[$alCounter] = $alName;
            }

            $alLine = strtok("\n");
        }
		if ( $this->debugging ) echo "mpd->GetAlbums()\n";
        return $alArray;
    }

	//*******************************************************************************//
	//***************************** INTERNAL FUNCTIONS ******************************//
	//*******************************************************************************//

    /* _computeVersionValue()
     *
     * Computes a compatibility value from a version string
     *
     */
    function _computeVersionValue($verStr) {
		list ($ver_maj, $ver_min, $ver_rel ) = split("\.",$verStr);
		return ( 100 * $ver_maj ) + ( 10 * $ver_min ) + ( $ver_rel );
    }

	/* _checkCompatibility() 
	 * 
	 * Check MPD command compatibility against our internal table. If there is no version 
	 * listed in the table, allow it by default.
	*/
	function _checkCompatibility($cmd) {
        // Check minimum compatibility
	  if (isset($this->COMPATIBILITY_MIN_TBL[$cmd])) {
	    $req_ver_low = $this->COMPATIBILITY_MIN_TBL[$cmd];
	    $req_ver_hi = $this->COMPATIBILITY_MAX_TBL[$cmd];
	  } else {
	    $req_ver_low = false;
	    $req_ver_hi = false;
	  }

		$mpd_ver = $this->_computeVersionValue($this->mpd_version);

		if ( $req_ver_low ) {
			$req_ver = $this->_computeVersionValue($req_ver_low);

			if ( $mpd_ver < $req_ver ) {
				$this->errStr = "Command '$cmd' is not compatible with this version of MPD, version ".$req_ver_low." required";
				return FALSE;
			}
		}

        // Check maxmum compatibility -- this will check for deprecations
		if ( $req_ver_hi ) {
            $req_ver = $this->_computeVersionValue($req_ver_hi);

			if ( $mpd_ver > $req_ver ) {
				$this->errStr = "Command '$cmd' has been deprecated in this version of MPD.";
				return FALSE;
			}
		}

		return TRUE;
	}

	/* _parseFileListResponse() 
	 * 
	 * Builds a multidimensional array with MPD response lists.
     *
	 * NOTE: This function is used internally within the class. It should not be used.
	 */
	function _parseFileListResponse($resp) {
		if ( is_null($resp) ) {
			return NULL;
		} else {
			$plistArray = array();
			$plistLine = strtok($resp,"\n");
			$plistFile = "";
			$plCounter = -1;
			while ( $plistLine ) {
				list ( $element, $value ) = split(": ",$plistLine);
				if ( $element == "file" ) {
					$plCounter++;
					$plistFile = $value;
					$plistArray[$plCounter]["file"] = $plistFile;
				} else {
					$plistArray[$plCounter][$element] = $value;
				}

				$plistLine = strtok("\n");
			} 
		}
		return $plistArray;
	}

	/* RefreshInfo() 
	 * 
	 * Updates all class properties with the values from the MPD server.
     *
	 * NOTE: This function is automatically called upon Connect() as of v1.1.
	 */
	function RefreshInfo() {
        // Get the Server Statistics
		$statStr = $this->SendCommand(MPD_CMD_STATISTICS);
		if ( !$statStr ) {
			return NULL;
		} else {
			$stats = array();
			$statLine = strtok($statStr,"\n");
			while ( $statLine ) {
				list ( $element, $value ) = split(": ",$statLine);
				$stats[$element] = $value;
				$statLine = strtok("\n");
			} 
		}

        // Get the Server Status
		$statusStr = $this->SendCommand(MPD_CMD_STATUS);
		if ( ! $statusStr ) {
			return NULL;
		} else {
			$status = array();
			$statusLine = strtok($statusStr,"\n");
			while ( $statusLine ) {
				list ( $element, $value ) = split(": ",$statusLine);
				$status[$element] = $value;
				$statusLine = strtok("\n");
			}
		}

        // Get the Playlist
		$plStr = $this->SendCommand(MPD_CMD_PLLIST);
   		$this->playlist = $this->_parseFileListResponse($plStr);
    	$this->playlist_count = count($this->playlist);

        // Set Misc Other Variables
		$this->state = $status['state'];
		if ( ($this->state == MPD_STATE_PLAYING) || ($this->state == MPD_STATE_PAUSED) ) {
			$this->current_track_id = $status['song'];
			list ($this->current_track_position, $this->current_track_length ) = split(":",$status['time']);
		} else {
			$this->current_track_id = -1;
			$this->current_track_position = -1;
			$this->current_track_length = -1;
		}

		$this->repeat = $status['repeat'];
		$this->random = $status['random'];

		$this->db_last_refreshed = $stats['db_update'];

		$this->volume = $status['volume'];
		$this->uptime = $stats['uptime'];
		$this->playtime = $stats['playtime'];
		if (isset($stats['songs_played'])) {
		  $this->num_songs_played = $stats['songs_played'];
		} else {
		  $this->num_songs_played = false;
		}
		if (isset($stats['num_artists'])) {
		  $this->num_artists = $stats['num_artists'];
		} else {
		  $this->num_artists = false;
		}
		if (isset($stats['num_songs'])) {
		$this->num_songs = $stats['num_songs'];
		} else {
		  $this->num_songs = false;
		}
		if (isset($stats['num_albums'])) { 
		  $this->num_albums = $stats['num_albums'];
		} else {
		  $this->num_albums = false;
		}
		return TRUE;
	}

    /* ------------------ DEPRECATED METHODS -------------------*/
	/* GetStatistics() 
	 * 
	 * Retrieves the 'statistics' variables from the server and tosses them into an array.
     *
	 * NOTE: This function really should not be used. Instead, use $this->[variable]. The function
	 *   will most likely be deprecated in future releases.
	 */
	function GetStatistics() {
		if ( $this->debugging ) echo "mpd->GetStatistics()\n";
		$stats = $this->SendCommand(MPD_CMD_STATISTICS);
		if ( !$stats ) {
			return NULL;
		} else {
			$statsArray = array();
			$statsLine = strtok($stats,"\n");
			while ( $statsLine ) {
				list ( $element, $value ) = split(": ",$statsLine);
				$statsArray[$element] = $value;
				$statsLine = strtok("\n");
			} 
		}
		if ( $this->debugging ) echo "mpd->GetStatistics() / return: " . print_r($statsArray) ."\n";
		return $statsArray;
	}

	/* GetStatus() 
	 * 
	 * Retrieves the 'status' variables from the server and tosses them into an array.
     *
	 * NOTE: This function really should not be used. Instead, use $this->[variable]. The function
	 *   will most likely be deprecated in future releases.
	 */
	function GetStatus() {
		if ( $this->debugging ) echo "mpd->GetStatus()\n";
		$status = $this->SendCommand(MPD_CMD_STATUS);
		if ( ! $status ) {
			return NULL;
		} else {
			$statusArray = array();
			$statusLine = strtok($status,"\n");
			while ( $statusLine ) {
				list ( $element, $value ) = split(": ",$statusLine);
				$statusArray[$element] = $value;
				$statusLine = strtok("\n");
			}
		}
		if ( $this->debugging ) echo "mpd->GetStatus() / return: " . print_r($statusArray) ."\n";
		return $statusArray;
	}

	/* GetVolume() 
	 * 
	 * Retrieves the mixer volume from the server.
     *
	 * NOTE: This function really should not be used. Instead, use $this->volume. The function
	 *   will most likely be deprecated in future releases.
	 */
	function GetVolume() {
		if ( $this->debugging ) echo "mpd->GetVolume()\n";
		$volLine = $this->SendCommand(MPD_CMD_STATUS);
		if ( ! $volLine ) {
			return NULL;
		} else {
			list ($vol) = sscanf($volLine,"volume: %d");
		}
		if ( $this->debugging ) echo "mpd->GetVolume() / return: $vol\n";
		return $vol;
	}

	/* GetPlaylist() 
	 * 
	 * Retrieves the playlist from the server and tosses it into a multidimensional array.
     *
	 * NOTE: This function really should not be used. Instead, use $this->playlist. The function
	 *   will most likely be deprecated in future releases.
	 */
	function GetPlaylist() {
		if ( $this->debugging ) echo "mpd->GetPlaylist()\n";
		$resp = $this->SendCommand(MPD_CMD_PLLIST);
		$playlist = $this->_parseFileListResponse($resp);
		if ( $this->debugging ) echo "mpd->GetPlaylist() / return ".print_r($playlist)."\n";
		return $playlist;
	}

    /* ----------------- Command compatibility tables --------------------- */
	var $COMPATIBILITY_MIN_TBL = array(
		MPD_CMD_SEEK 		=> "0.9.1"	,
		MPD_CMD_PLMOVE  	=> "0.9.1"	,
		MPD_CMD_RANDOM  	=> "0.9.1"	,
		MPD_CMD_PLSWAPTRACK	=> "0.9.1"	,
		MPD_CMD_PLMOVETRACK	=> "0.9.1"  ,
		MPD_CMD_PASSWORD	=> "0.10.0" ,
        MPD_CMD_SETVOL      => "0.10.0"
	);

    var $COMPATIBILITY_MAX_TBL = array(
        MPD_CMD_VOLUME      => "0.10.0"
    );

}   // ---------------------------- end of class ------------------------------
	
	
	
	
	
?>
