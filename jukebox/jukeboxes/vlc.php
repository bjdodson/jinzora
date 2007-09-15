<?php
if (!defined(JZ_SECURE_ACCESS))
	die('Security breach detected.');
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
* Binds with the VLC HTTP interface.
*
* @since 2/9/05
* @author Ben Dodson
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
$jbArr[0]['port'] = "8080";
$jbArr[0]['password'] = "jinzora";
$jbArr[0]['description'] = "Living Room";
$jbArr[0]['type'] = "vlc"; // VERY IMPORTANT
  $jbArr[0]['prefix'] = "http"; // use weblinks instead of paths

*/

/**
* The installer function for this jukebox
* 
* @author Ross Carlson
* @version 11/20/05
* @since 11/20/05
* @param $step int The step of the install process we are on
*/
function jbInstall($step) {
	global $jbArr, $root_dir;

	echo "<strong>VLC Jukebox Installer</strong><br><br>";

	// Now which step are we on?
	switch ($step) {
		case "2" :
			// Now let's create the step 2 page
?>
				Please complete the following to setup VLC with Jinzora.<br><br>
				<strong>NOTE:</strong> VLC must already be configured with the http interface and running for this wizard to complete.<br><br>
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
								<input type="text" value="8080" name="edit_port" class="jz_input">
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
								<input type="text" value="VLC Player" name="edit_description" class="jz_input">
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
					<input type="hidden" name="edit_jukebox_type" value="vlc">					
				</form>
				<?php

			exit ();
			break;
		case "3" :
			// Let's test the connection to the player
			// First let's set all the variables for it
			$jbArr[0]['server'] = $_POST['edit_server'];
			$jbArr[0]['port'] = $_POST['edit_port'];
			$jbArr[0]['description'] = $_POST['edit_description'];
			$jbArr[0]['password'] = $_POST['edit_password'];
			$jbArr[0]['type'] = "vlc";
			$_SESSION['jb_id'] = 0;

			echo "Testing connection to VLC...<br>";
			if (playerConnect()) {
				echo "Success! Plesae wait while we write the settings...<br><br>";
				flushdisplay();
				sleep(1);
			} else {
				echo "Failed!  Jinzora had an issue communicating with VLC, ensure that it's running and that you've specified the proper settings";
				exit ();
			}

			// Ok, let's create the settings file
			$content = "<?" . "php\n";
			$content .= "    $" . "jbArr[0]['server'] = '" . $_POST['edit_server'] . "';\n";
			$content .= "    $" . "jbArr[0]['port'] = '" . $_POST['edit_port'] . "';\n";
			$content .= "    $" . "jbArr[0]['description'] = '" . $_POST['edit_description'] . "';\n";
			$content .= "    $" . "jbArr[0]['password'] = '" . $_POST['edit_password'] . "';\n";
			$content .= "    $" . "jbArr[0]['type'] = 'vlc';\n";
			$content .= "?>";

			// Now let's write it out IF we can
			$filename = getcwd() . "/jukebox/settings.php";
			if (is_writable($filename)) {
				$handle = fopen($filename, "w");
				fwrite($handle, $content);
				fclose($handle);
?>
					<form method="post">
						<input type="submit" name="continue" value="Continue to the Jukebox interface" class="jz_submit"><br><br>
					</form>
					<?php

				exit ();
			} else {
				echo 'It looks like your jukebox settings file at "' . $filename . '" is not writeable.<br>You must make it writeable to proceed!';
				echo '<br><br>If you are on Linux you can execute "chmod 666 ' . $filename . '" at a shell to make it writeable.<br><br>';
				echo "Or copy and paste the below information into the file at: " . getcwd() . "/jukebox/settings.php<br><br>";
				echo str_replace("    ", "&nbsp;&nbsp;&nbsp;&nbsp;", nl2br(str_replace("<?php", "&lt;php", $content)));
				echo "<br><br>";
				exit ();
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
function retJBStats() {
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
function returnJBAbilities() {

	$retArray['playbutton'] = true;
	$retArray['pausebutton'] = true;
	$retArray['stopbutton'] = true;
	$retArray['nextbutton'] = true;
	$retArray['prevbutton'] = true;
	$retArray['shufflebutton'] = true;
	$retArray['clearbutton'] = true;
	$retArray['repeatbutton'] = true;
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
	//$retArray['move'] = true;

	return $retArray;
}

/**
* Returns the connection status of the player true or false
* 
* @author Ben Dodson
* @version 9/15/07
* @since 9/15/07
* @param return Returns true or false
*/
function playerConnect() {
	global $jbArr;

	writeLogData("messages", "VLC: Testing connection to the player");

	$status = @ VLCRequest(array (), "status");

	if ($status == "") {
		return false;
	} else {
		return true;
	}
}

/**
* Returns Addon tools for the jukebox.
* 
* @author Ross Carlson
* @version 2/9/05
* @since 2/9/05
*/
function getAllAddOnTools() {
	return;
}

/**
* Returns the currently playing tracks path so we can get the node
* 
* @author Ben Dodson
* @version 9/15/07
* @since 9/15/07
* @param return Returns the currently playling track's path
*/
function getCurTrackPath() {
	global $jbArr;
	$val = '';

	$xml = getPlaylistXML();
	$res = $xml->xpath("//leaf[@current='current']");
	if ($res) {
	  $val = $res[0]['uri'];
	}
	writeLogData("messages", "VLC: Returning the current playing track path: " . $val);

	return $val;
}

/**
* Returns the currently playing track number
* 
* @author Ben Dodson
* @version 9/15/07
* @since 9/15/07
* @param return Returns the currently playling track number
*/
function getCurPlayingTrack() {
	global $jbArr;
	$val = -1;

	$xml = getPlaylistXML();
	
	$res = $xml->xpath('//leaf');
		
	while (list($i, $node) = each($res)) {
	  if (isset($node['current']) && $node['current'] == 'current') {
	    $val = $i;
	    break;
	  }
	}
	writeLogData("messages", "VLC: Returning the current playing track number: " . $val);

	return $val;
}

/**
* Returns the currently playing playlist
* 
*
* @author Ben Dodson
* @version 9/15/07
* @since 9/15/07
* @param return Returns the currently playling playlist
*/
function getCurPlaylist() {
	global $jbArr;
	
	$val = array ();
	/* alternate method for getting entry names if using Jinzora's weblinks. */
	if (isset ($jbArr[$_SESSION['jb_id']]['prefix']) && $jbArr[$_SESSION['jb_id']]['prefix'] == "http") {
	  $xml = getPlaylistXML();
	  $res = $xml->xpath('//leaf');
		
		while (list(, $node) = each($res)) {
		  $url = $node['name'];
		  if (false != ($id = getTrackIdFromURL($url))) {
				$val[] = idToName($id);
			} else {
				$val[] = $url; // faster
				// better way: query VLC for title.
			}
		}
	} else {
	  $xml = getPlaylistXML();
	  $res = $xml->xpath('//leaf');
	  
	  while (list(, $node) = each($res)) {
	    $attr = $node->attributes();
	    $val[] = $attr['name'];
	  }
	}
	writeLogData("messages", "VLC: Returning the current playlist");
	
	return $val;
}

/**
* Passes a playlist to the jukebox player
* 
* @author Ben Dodson
* @version 9/15/07
* @since 9/15/07
* @param $playlist The playlist that we are passing
*/
function playlist($playlist) {
	global $include_path, $jbArr, $jzSERVICES;

	if (isset ($jbArr[$_SESSION['jb_id']]['prefix']) && $jbArr[$_SESSION['jb_id']]['prefix'] == "http") {
		$content = "";
		foreach ($playlist->getList() as $track) {
			$content .= $track->getFileName("user") . "\n";
		}
		$playlist = $content;
	} else {
		$playlist = $jzSERVICES->createPlaylist($playlist, "jukebox");
		$arr = explode("\n", $playlist);
		$arr2 = array ();
		foreach ($arr as $a) {
			if (false === stristr($a, "://")) {
				$arr2[] = $a;
			} else {
				$arr2[] = $a;
			}
		}
		$playlist = implode("\n", $arr2);
	}
	writeLogData("messages", "VLC: Creating a playlist");

	// First we need to get the current playlist so we can figure out where to add
	$xml = getPlaylistXML();
	$res = $xml->xpath('//leaf');
		
	$curList = array();
	$curIDList = array();
	while (list(, $node) = each($res)) {
	  if (isset($node['uri']) && $node['uri'] <> "") {
	    $curList[] = $node['uri'];
	    $curIDList[] = $node['id'];
	  }
	}

	// Let's get where we are in the current list
	writeLogData("messages", "VLC: Getting the current playing track number");
	$curTrack = getCurPlayingTrack();

	switch ($_SESSION['jb-addtype']) {
		case "end" :
		case "current" :
			$restart_playback = false;
			break;
		default :
			$restart_playback = true;
	}

	// Ok, now we need to figure out where to add the stuff
	if ($_SESSION['jb-addtype'] == "current") {
		// Ok, let's split our first playlist in 2 so we can add in the middle
		//$begArr = array_slice($curList,0,$curTrack+1);
		if (is_array($curList)) {
			$begArr = array ();
			$endArr = array_slice($curList, $curTrack +1);
		} else {
			$begArr = array ();
			$endArr = array ();
		}
	} else
		if ($_SESSION['jb-addtype'] == "begin") {
			$begArr = array ();
			$endArr = $curList;
		} else
			if ($_SESSION['jb-addtype'] == "end") {
				$begArr = array ();
				$endArr = array ();
			} else
				if ($_SESSION['jb-addtype'] == "replace") {
					$begArr = array ();
					$endArr = array ();
				}

	if ($restart_playback === true) {
		writeLogData("messages", "VLC: Clearing the current playlist");
		control("clear", false);
	} else
		if ($_SESSION['jb-addtype'] == "current") {
			// Remove everything at the end of the playlist, since we are going to readd it all.
			$arr = array (
				"command" => "pl_delete"
			);
			for ($i = sizeof($curList); $i > $curTrack; $i--) {
				$arr['id'] = $curIDList[$i];
				VLCRequest($arr);
			}
		}

	writeLogData("messages", "VLC: Sending the new playlist to the player");

	// Now let's send the new playlist to the player
	for ($i = 0; $i < count($begArr); $i++) {
		// Now let's add this
		if ($begArr[$i] <> "") {
			$arr = array (
				"command" => "in_enqueue"
			);
			$arr['input'] = $begArr[$i];
			VLCRequest($arr);
		}
	}
	// Ok, Now let's add the new stuff
	$pArray = explode("\n", $playlist);
	for ($i = 0; $i < count($pArray); $i++) {
		if ($pArray[$i] <> "") {
			$arr = array (
				"command" => "in_enqueue"
			);
			$arr['input'] = $pArray[$i];
			VLCRequest($arr);
		}
	}
	// Now let's finish this out
	for ($i = 0; $i < count($endArr); $i++) {
		// Now let's add this
		if ($endArr[$i] <> "") {
			$arr = array (
				"command" => "in_enqueue"
			);
			$arr['input'] = $endArr[$i];
			VLCRequest($arr);
		}
	}

	// Now let's jump to where we need to play
	switch ($_SESSION['jb-addtype']) {
		case "current" :
			if ($curTrack == 0) {
				$curTrack = -1;
			}
			$_POST['jbjumpto'] = $curTrack +1;
			break;
		case "end" :
			$_POST['jbjumpto'] = $curTrack;
			break;
		case "replace" :
		case "begin" :
			$_POST['jbjumpto'] = 0;
			break;
	}
	if ($restart_playback) {
	  //control("stop");
	  //control("jumpto");
	  control("pause"); // play/pause
	}

	if (defined('NO_AJAX_JUKEBOX')) {
?>
		<script>
			history.back();
		</script>
		<?php

	}
	exit ();
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
function control($command, $goBack = true) {
	global $jbArr;

	writeLogData("messages", "VLC: Sending command to jukebox: " . $command);
	// Now let's execute the command
	switch ($command) {
		case "play" :
			$handle = VLCRequest(array (
				"command" => "pl_pause"
			)); // fix this?
			break;
		case "stop" :
			$handle = VLCRequest(array (
				"command" => "pl_stop"
			));
			break;
		case "pause" :
			$handle = VLCRequest(array (
				"command" => "pl_pause"
			));
			break;
		case "previous" :
			$handle = VLCRequest(array (
				"command" => "pl_previous"
			));
			break;
		case "next" :
			$handle = VLCRequest(array (
				"command" => "pl_next"
			));
			break;
		case "volume" :
			// Max volume is 1024, which is "400%".
			// We will scale this back to 256.
			$vol = 256 * ($_POST['jbvol'] / 100);
			VLCRequest(array (
				"command" => "volume",
				"val" => $vol
			));
			$_SESSION['jz_jbvol-' . $_SESSION['jb_id']] = $_POST['jbvol'];
			break;
		case "playwhere" :
			// Ok, let's set where they are playing
			$_SESSION['jb_playwhere'] = $_POST['jbplaywhere'];
			// Now let's figure out it's ID
			for ($i = 0; $i < count($jbArr); $i++) {
				if ($jbArr[$i]['description'] == $_SESSION['jb_playwhere']) {
					$_SESSION['jb_id'] = $i;
				}
			}
			break;
		case "jumpto" :
		  // Only way to do this now: get the playlist, skip to $pos and get it's ID.
		  // Jinzora treats playlists as having entries 0 through n;
		  // VLC assigns IDs. To reconcile, we flatten their list.
			$pos = $_POST['jbjumpto'];
			$tmp = VLCRequest(array (), "playlist");
			$xml = new SimpleXMLElement($tmp);
			$res = $xml->xpath('//leaf[@id]');
			
			VLCRequest(array (
				"command" => "pl_play",
				"id" => $res[$pos]['id']
			));
			break;
		case "clear" :
			$handle = VLCRequest(array (
				"command" => "pl_stop"
			));
			$handle = VLCRequest(array (
				"command" => "pl_empty"
			));
			if ($goBack) {
?>
					<script>
						history.back();
					</script>
					<?php

			}
			break;
		case "no_repeat" :
		case "repeat" :
			VLCRequest(array (
				"command" => "pl_repeat"
			));
			break;
		case "delone" :
			$arr = array (
				"command" => "pl_delete"
			);
			
			$xml = getPlaylistXML();
			$res = $xml->xpath('//leaf[@id]');
			
			for ($i = sizeof($_POST['jbSelectedItems']) - 1; $i >= 0; $i--) {
				$arr['id'] = $res[$_POST['jbSelectedItems'][$i]]['id'];
				VLCRequest($arr);
			}
			$_SESSION['jbSelectedItems'] = array ();
			break;
		case "random_play" :
			VLCRequest(array (
				"command" => "pl_random"
			));
			break;
		case "addwhere" :
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
function getStatus() {
	global $jbArr;

	$xml = getStatusXML();
	
	writeLogData("messages", "VLC: Returning player status: " . $status);
	$status = $xml->state;
	switch ($status) {
	case "stop" :
	  return "stopped";
	  break;
	default: return $status;
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
function getCurTrackName() {
	global $jbArr;
	
	$xml = getPlaylistXML();
	$res = $xml->xpath("//leaf[@current='current']");
	writeLogData("messages", "VLC: Returning current track name: " . $val);

	if (isset ($jbArr[$_SESSION['jb_id']]['prefix']) && $jbArr[$_SESSION['jb_id']]['prefix'] == "http") {
	  if (false !== ($id = getTrackIdFromURL($res[0]['name']))) {
	    return idToName($id);
	  }
	}
	return $res[0]['name'];
}

/**
* Returns how long is left in the current track (in seconds)
* 
* @author Ross Carlson
* @version 2/9/05
* @since 2/9/05
* @return Returns the name of the current playing track
*/
function getCurTrackRemaining() {
	global $jbArr;

	$xml = getStatusXML();

	writeLogData("messages", "VLC: Returning time remaining: " . $val);

	return $xml->length - $xml->time;
}

/**
* Gets the length of the current track
* 
* @author Ben Dodson
* @version 9/15/07
* @since 9/15/07
* @param return returns the amount of time remaining in seconds
*/
function getCurTrackLength() {
	global $jbArr;
	$xml = getStatusXML();
	
	
	return $xml->length;
}

/**
* Returns how long is left in the current track (in seconds)
* 
 * @author Ben Dodson
* @version 9/15/07
* @since 9/15/07
* @return Returns the name of the current playing track
*/
function getCurTrackLocation() {
	global $jbArr;
	$xml = getStatusXML();
	
	writeLogData("messages", "VLC: Returning current track location: " . $val);

	return $xml->time;
}

/**
 * Simple code to send a VLC HTTP request.
 *
 * @author Ben Dodson
 * @since 9/8/05
 *
 */
function VLCRequest($args = array (), $action = "status") {
	global $jbArr;
	$str = "";
	if (is_array($args) && sizeof($args) > 0) {
	  foreach ($args as $key => $val) {
	    $str = $str . '&' . rawurlencode($key) . "=" . rawurlencode($val);
	  }
	}

	return @ file_get_contents("http://" .
	$jbArr[$_SESSION['jb_id']]['server'] .
	":" . $jbArr[$_SESSION['jb_id']]['port'] .
	"/requests/${action}.xml?" . $str);
}

/**
 * Gets the current status XML document
 * If we have already pulled it on this
 * request, use that copy.
 */

function getStatusXML() {
  static $xml = NULL;
  if ($xml != NULL) {
    return $xml;
  }

  $tmp = VLCRequest(array (), "status");
  $xml = new SimpleXMLElement($tmp);
  
  return $xml;
}

/**
 * Same function for the playlist XML
 */
function getPlaylistXML() {
  static $xml = NULL;
  if ($xml != NULL) {
    return $xml;
  }

  $tmp = VLCRequest(array (), "playlist");
  $xml = new SimpleXMLElement($tmp);
  
  return $xml;
}

/**
 * Gets the track title from
 * its ID.
 */
function idToName($id) {
  $track = new jzMediaTrack($id, 'id');
  $meta = $track->getMeta();
  $title = '';
  if (!isNothing($meta['artist'])) {
    $title .= $meta['artist'];
  }
  if (!isNothing($meta['title'])) {
    if (!isNothing($title)) {
      $title .= ' - ';
    }
    $title .= $meta['title'];
  }
  if (isNothing($title)) {
    $title = $track->getName();
  }
  return $title;
}

?>
