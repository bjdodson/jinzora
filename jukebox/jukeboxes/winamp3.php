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
	$jbArr[0]['server'] = "localhost";
	$jbArr[0]['port'] = "4800";
	$jbArr[0]['password'] = "jinzora";
	$jbArr[0]['description'] = "Living Room";
	$jbArr[0]['type'] = "winamp"; // VERY IMPORTANT
	
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
		global $jbArr, $root_dir;

		echo "<strong>Winamp Jukebox Installer</strong><br><br>";
		
		// Now which step are we on?
		switch($step){
			case "2":
				// Now let's create the step 2 page
				?>
				Please complete the following to setup Winamp/httpQ Plugin with Jinzora.<br><br>
				<strong>NOTE:</strong> Winamp must already be configured with the httpQ plugin and running for this wizard to complete.<br><br>
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
								<input type="text" value="4800" name="edit_port" class="jz_input">
							</td>
						</tr>
						<tr>
							<td>
								Password:
							</td>
							<td>
								<input type="password" value="" name="edit_password" class="jz_input">
							</td>
						</tr>
						<tr>
							<td>
								Name:
							</td>
							<td>
								<input type="text" value="Winamp Player" name="edit_description" class="jz_input">
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
					<input type="hidden" name="edit_jukebox_type" value="winamp3">					
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
				$jbArr[0]['type'] = "winamp3";
				$_SESSION['jb_id'] = 0;
				
				echo "Testing connection to Winamp...<br>";
				if (playerConnect()){
					echo "Success! Plesae wait while we write the settings...<br><br>";
					flushdisplay();
					sleep(1);
				} else {
					echo "Failed!  Jinzora had an issue communicating with Winamp, ensure that it's running and that you've specified the proper settings";
					exit();
				}
				
				// Ok, let's create the settings file
				$content  = "<?". "php\n";
				$content .= "    $". "jbArr[0]['server'] = '". $_POST['edit_server']. "';\n";
				$content .= "    $". "jbArr[0]['port'] = '". $_POST['edit_port']. "';\n";
				$content .= "    $". "jbArr[0]['description'] = '". $_POST['edit_description']. "';\n";
				$content .= "    $". "jbArr[0]['password'] = '". $_POST['edit_password']. "';\n";
				$content .= "    $". "jbArr[0]['type'] = 'winamp3';\n";
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
		$retArray['repeatbutton'] = false;
		$retArray['delonebutton'] = true;
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
		
		writeLogData("messages","Winamp3: Testing connection to the player");
		
		$status = @@file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/isplaying?p=". $jbArr[$_SESSION['jb_id']]['password']);		

		if ($status == ""){
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
		
		$val = @file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/getplaylistfile?p=". $jbArr[$_SESSION['jb_id']]['password']. "&index=". getCurPlayingTrack());

		writeLogData("messages","Winamp3: Returning the current playing track path: ". $val);
		
		return $val;
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
		
		$val = @file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/getlistpos?p=". $jbArr[$_SESSION['jb_id']]['password']);
		
		writeLogData("messages","Winamp3: Returning the current playing track number: ". $val);
		
		return $val;
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
		
		$val = explode(";;;",@file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/getplaylisttitle?p=". $jbArr[$_SESSION['jb_id']]['password']. "&delim=;;;"));
		
		writeLogData("messages","Winamp3: Returning the current playlist");
		
		return $val;
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
		
		if (isset($jbArr[$_SESSION['jb_id']]['prefix']) && $jbArr[$_SESSION['jb_id']]['prefix'] == "http") {
		  $content = "";
		  foreach ($playlist->getList() as $track) {
		    $content .= $track->getFileName("user")."\n";
		  }
		  $playlist = $content;
		} else {
		  $playlist = $jzSERVICES->createPlaylist($playlist,"jukebox");
		  $arr = explode("\n",$playlist);
		  $arr2 = array();
		  foreach ($arr as $a) {
		    if (false === stristr($a, "://")) {
		      $arr2[] = str_replace("/","\\",$a);
		    } else {
		      $arr2[] = $a;
		    }
		  }
		  $playlist = implode("\n",$arr2);
		}
		writeLogData("messages","Winamp3: Creating a playlist");

		// First we need to get the current playlist so we can figure out where to add
		$clist = explode(";;;",@file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/getplaylisttitle?p=". $jbArr[$_SESSION['jb_id']]['password']. "&delim=;;;"));
		foreach($clist as $item){
			if ($item <> ""){
				$curList[] = $item;
			}
		}
		
		// Let's get where we are in the current list
		writeLogData("messages","Winamp3: Getting the current playing track number");
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
		  writeLogData("messages","Winamp3: Clearing the current playlist");
		  control("clear",false);
		} else if ($_SESSION['jb-addtype'] == "current") {
		  // Remove everything at the end of the playlist, since we are going to readd it all.
		  for ($i = sizeof($curList); $i > $curTrack; $i--) {
		    $arr = array();
		    $arr['index'] = $i;
		    httpqRequest("deletepos",$arr);
		  }
		}
		
		writeLogData("messages","Winamp3: Sending the new playlist to the player");





		// Now let's send the new playlist to the player
		for ($i=0; $i < count($begArr); $i++){
			// Now let's add this
			if ($begArr[$i] <> ""){
			  $arr = array();
			  $arr['file'] = $begArr[$i];
			  httpqRequest("playfile", $arr);
			}
		}		
		// Ok, Now let's add the new stuff
		$pArray = explode("\n",$playlist);
		for ($i=0; $i < count($pArray); $i++){
			if ($pArray[$i] <> ""){
			  $arr = array();
			  $arr['file'] = $pArray[$i];
			  httpqRequest("playfile", $arr);
			}
		}
		// Now let's finish this out
		for ($i=0; $i < count($endArr); $i++){
			// Now let's add this
			if ($endArr[$i] <> ""){
			  $arr = array();
			  $arr['file'] = $endArr[$i];
			  httpqRequest("playfile", $arr);
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
	* @param $goBack Should we go back after executing (default is true)
	*/
	function control($command, $goBack = true){
		global $jbArr;

		writeLogData("messages","Winamp3: Sending command to jukebox: ". $command);
		// Now let's execute the command
		switch ($command){
			case "play":
				$handle = fopen("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/play?p=". $jbArr[$_SESSION['jb_id']]['password']. "", "r");
			break;
			case "stop":
				$handle = fopen("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/stop?p=". $jbArr[$_SESSION['jb_id']]['password']. "", "r");
			break;
			case "pause":
				$handle = fopen("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/pause?p=". $jbArr[$_SESSION['jb_id']]['password']. "", "r");
			break;
			case "previous":
				$handle = fopen("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/prev?p=". $jbArr[$_SESSION['jb_id']]['password']. "", "r");
			break;
			case "next":
				$handle = fopen("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/next?p=". $jbArr[$_SESSION['jb_id']]['password']. "", "r");
			break;
			case "volume":
				// Now we have to set the value based on 0-255
				$vol = 255 * ($_POST['jbvol'] / 100);
				$handle = fopen("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/setvolume?p=". $jbArr[$_SESSION['jb_id']]['password']. "&level=". $vol, "r");			
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
				@file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/stop?p=". $jbArr[$_SESSION['jb_id']]['password']);
				@file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/setplaylistpos?p=". $jbArr[$_SESSION['jb_id']]['password']. "&index=". $pos);
				usleep(1000);
				@file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/play?p=". $jbArr[$_SESSION['jb_id']]['password']);
			break;
			case "clear":
				@file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/stop?p=". $jbArr[$_SESSION['jb_id']]['password']);
				@file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/delete?p=". $jbArr[$_SESSION['jb_id']]['password']);
				if ($goBack){
					?>
					<script>
						history.back();
					</script>
					<?php
				}
			break;
			case "delone":
				$arr = array();
				for ( $i = sizeof($_POST['jbSelectedItems']) - 1; $i  >= 0; $i--) {
		    		$arr['index'] = $_POST['jbSelectedItems'][$i];
		   			httpqRequest("deletepos",$arr);
				}
				$_SESSION['jbSelectedItems'] = array();
			break;
			case "random_play":
				// Ok, now we have to get the whole list, then shuffle it
				$list = @file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/getplaylistfile?p=". $jbArr[$_SESSION['jb_id']]['password']);
				$lArray = explode("<br>",$list);
				// Now let's shuffle that
				shuffle($lArray);
				// Now we have to write it back out
				$pList = implode("\n",$lArray);
				// Now we have to play this, but first stop and clear the list
				@file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/stop?p=". $jbArr[$_SESSION['jb_id']]['password']);
				@file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/delete?p=". $jbArr[$_SESSION['jb_id']]['password']);
				playlist($pList);
			break;
			case "addwhere":
				$_SESSION['jb-addtype'] = $_POST['addplat'];
			break;
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
		
		$status = @file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/isplaying?p=". $jbArr[$_SESSION['jb_id']]['password']);
		
		writeLogData("messages","Winamp3: Returning player status: ". $status);
		
		switch ($status){
			case "1":
				return "playing";
			break;
			case "0":
				return "stopped";
			break;
			case "3":
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
		
		$track = @file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/getcurrenttitle?p=". $jbArr[$_SESSION['jb_id']]['password']);
		$val = substr($track,strpos($track,". ")+2);
		
		writeLogData("messages","Winamp3: Returning current track name: ". $val);

		return  $val;
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
		
		$length = getCurTrackLength();
		$cur = getCurTrackLocation();
		$val = (($length - $cur) + 2);
		
		writeLogData("messages","Winamp3: Returning time remaining: ". $val);
		
		return $val;
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

		$val = @file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/getoutputtime?p=". $jbArr[$_SESSION['jb_id']]['password']. "&frmt=1");;
		
		writeLogData("messages","Winamp3: Returning current track length: ". $val);
		
		return $val;
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
		
		$val = @file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/getoutputtime?p=". $jbArr[$_SESSION['jb_id']]['password']. "&frmt=0");
		$val = round(($val / 1000),0);
		
		writeLogData("messages","Winamp3: Returning current track location: ". $val);
		
		return $val;
	}

/**
 * Simple code to send an HTTPQ request.
 *
 * @author Ben Dodson
 * @since 9/8/05
 *
 */
function httpqRequest($action, $args = array()) {
  global $jbArr;
  $str = "";
  if ($args != array()) {
    foreach ($args as $key => $val) {
      $str = $str . '&' . rawurlencode($key) . "=" . rawurlencode($val);
    }
  }

  return @file_get_contents("http://". $jbArr[$_SESSION['jb_id']]['server']. ":". $jbArr[$_SESSION['jb_id']]['port']. "/${action}?p=". $jbArr[$_SESSION['jb_id']]['password'] . $str);
}
?>