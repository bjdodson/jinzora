<?php 
	if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
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
	* - This allows updates to be sent to AudioScrobbler when streaming media
	*
	* @since 08.25.05
	* @author Ross Carlson <ross@jinzora.org>
	*/


	define('SERVICE_REPORTING_audioscrobbler','true');
	

	function SERVICE_REPORTING_audioscrobbler($node){
		global $jzUSER, $enable_audioscrobbler, $as_override_user, $as_override_pass, $as_override_all;
		// First is audioscrobbler enabled?
		if ($enable_audioscrobbler <> "true"){
			return;
		}
		
		writeLogData("as_debug","AudioScrobbler: Starting up (inside startup routine) ");

		// Let's get the meta data from the track
		$meta = $node->getMeta();
		$timestamp = $node->getStartTime();
		
		// Now let's get the user info
		if ($as_override_user <> "" and $as_override_all == "true"){
			$user = $as_override_user;
			$pass = $as_override_pass;
		} else {
			$user = $jzUSER->getSetting("asuser");
			$pass = $jzUSER->getSetting("aspass");
			if ($user == false and $as_override_user <> ""){
				$user = $as_override_user;
				$pass = $as_override_pass;
			}
		}
		
		if ($user == ""){
			return;
		}
		
		writeLogData("as_debug","AudioScrobbler: Sending data for user: ". $user);
		
		// Now let's create the audioscrobbler object
		$as = new Scrobbler($user, $pass);
	
		writeLogData("as_debug","AudioScrobbler: Sending data for user: ". $user);
 		
 		// Now let's create the audioscrobbler object
 		$as = new Scrobbler($user, $pass);
	
		// Attempt to send any previously queued tracks
		if($as->handshake()) {
			writeLogData("as_debug","AudioScrobbler: Sending previously queued tracks");
			if(!$as->submitTracks()) {
				writeLogData("messages",'AudioScrobbler->submitTracks Error, will retry later: '.$as->errorMsg);
				writeLogData("as_debug",'AudioScrobbler->submitTracks Error, will retry later: '.$as->errorMsg);
			}
		} else {
 			writeLogData("messages",'AudioScrobbler->handshake: Error, retry later: '. $as->errorMsg);
			writeLogData("as_debug",'AudioScrobbler->handshake: Error, retry later: '. $as->errorMsg);
 			return;
 		}
		
		// Gotta make sure it's at least 31 seconds long
		$length = $meta['length'];
		if ($length < 30){
			$length = 31;
			if ($length < 30){
				writeLogData("messages", "AudioScrobbler: Warning: Track length less than 30 seconds.  Not submitting");
				writeLogData("as_debug", "AudioScrobbler: Warning: Track length less than 30 seconds.  Not submitting");
				return;
			}
 		}

		// Now queue current track
		writeLogData("as_debug","AudioScrobbler: Queueing data for: ". $meta['title']);
		if(empty($timestamp)) {
			$timestamp = time();
 		}

		$as->queueTrack($meta['artist'], $meta['album'], $meta['title'], $timestamp, $length);
		writeLogData("as_debug","AudioScrobbler: Sending data complete");
	}
	
	
	// This defines the audioscrobbler class
	class Scrobbler {
		var $errorMsg;
		var $username;
		var $password;
		var $challenge;
		var $submitHost;
		var $submitPort;
		var $submitURL;
		var $queuedTracks;
		var $postMDF;
		var $rawurl;
		var $interval;
	
		function scrobbler($username, $password) {
			$this->errorMsg = '';
			$this->username = $username;
			$this->password = $password;
			$this->challenge = '';
			$this->queuedTracks = array();
		}

		function handshake() {
      global $scrobble_server, $scrobble_client_id, $scrobble_plugin_version;
			
			$asSocket = fsockopen('post.audioscrobbler.com', 80, $errno, $errstr, 10);
			if(!$asSocket) {
				$this->errorMsg = $errstr;
				return FALSE;
			}
			    $username = rawurlencode($this->username);
			    $requestURLString = "";
			    $requestURLString = 'GET /';
			    $requestURLString .= '?';
			    $requestURLString .= 'hs=true';
			    $requestURLString .= '&';
			    $requestURLString .= 'p=1.1';
			    $requestURLString .= '&';
			    $requestURLString .= 'c=' . $scrobble_client_id;
			    $requestURLString .= '&';
			    $requestURLString .= 'v=' . $scrobble_plugin_version;
			    $requestURLString .= '&';
			    $requestURLString .= 'u=' . $username;
			    $requestURLString .= " HTTP/1.1\r\n";
			writeLogData("as_debug","request URL: \n" . $requestURLString);

			fwrite($asSocket, $requestURLString);
			fwrite($asSocket, "Host: post.audioscrobbler.com\r\n");
			fwrite($asSocket, "Accept: */*\r\n\r\n");
	
	
			$buffer = '';
			while(!feof($asSocket)) {
				$buffer .= fread($asSocket, 8192);
			}
			fclose($asSocket);

			if(preg_match('/UPTODATE\n(.*)\n(http:.*)\nINTERVAL ([0-9]*)/', $buffer, $matches)) {
				$this->challenge = $matches[1];	
				$this->rawurl = $matches[2];
				$this->interval = $matches[3];
			} else if(preg_match('/UPDATE(.*)/', $buffer, $matches)) {
				$this->errorMsg = 'You need to update your client: ' . $buffer;
                                return FALSE;
			} else {
				$this->errorMsg = 'Did not receive a valid response ';
				$this->errorMsg .= $buffer;
        return FALSE;
			}

			if(preg_match('/http:\/\/(.*):(\d+)(.*)/', $this->rawurl, $matches)) {
				$this->submitHost = $matches[1];
				$this->submitPort = $matches[2];
				$this->submitURL = $matches[3];
			} else {
				$this->errorMsg = 'Invalid POST URL returned, unable to continue' . $matches[1];
				return FALSE;
			}
	
			return TRUE;
		}

		/* @author Chris Hescott 
		 * @version Jan-5-05
		 * @since Jan-5-05
		 */
		function queueTrack($artist, $album, $track, $timestamp, $length) {
			$date = gmdate('Y-m-d H:i:s', $timestamp);
			$mydate = date('Y-m-d H:i:s T', $timestamp);
	
			if($length < 10) return FALSE;
	
			$newtrack = array();
			$newtrack['artist'] = $artist;
			$newtrack['album'] = $album;
			$newtrack['track'] = $track;
			$newtrack['length'] = $length;
			$newtrack['time'] = $date;
			$newtrack['stoptime'] = $length + $timestamp;
			$newtrack['retrycnt'] = 0;
			$this->queuedTracks = $_SESSION['as_queued_tracks'];	
 			$this->queuedTracks[$timestamp] = $newtrack;
			// Save this to our sesion variable
			$_SESSION['as_queued_tracks'] = $this->queuedTracks;

			$this->printCurrentQueue("Queued Tracks");
	
			$this->queuedTracks[$timestamp] = $newtrack;
			return TRUE;
		}
	
		function submitTracks() {
			$this->queuedTracks = $_SESSION['as_queued_tracks'];
			if(count($this->queuedTracks) == 0) {
				$this->errorMsg = "No tracks to submit\n";
				return FALSE;
			}
	
			ksort($this->queuedTracks); //sort array by timestamp
			$passwordMD5 = md5($this->password);  // TODO: Change stored password to be md5 version (this line would then be unnecessary	
			$queryStr =  'u='.rawurlencode($this->username);
			$queryStr .= '&s='.rawurlencode(md5($passwordMD5.$this->challenge));

			$trackQueryStr .= $this->TestTrackLengths();
			if(empty($trackQueryStr)) { # Didn't find any track to report
				$this->errorMsg = "No valid tracks to submit\n";
				return FALSE;
 			}
			
			$queryStr .= $trackQueryStr;
			//writeLogData("as_debug","queryStr = ".$queryStr."\n");
	
			$asSocket = fsockopen($this->submitHost, $this->submitPort, $errno, $errstr, 10);
			if(!$asSocket) {
				$this->errorMsg = "Could not open socket. ". $errstr;
				return FALSE;
			}
	
			$action = "POST ".$this->submitURL." HTTP/1.0\r\n";
			fwrite($asSocket, $action);
			fwrite($asSocket, "Host: ".$this->submitHost."\r\n");
			fwrite($asSocket, "Accept: */*\r\n");
			fwrite($asSocket, "Content-type: application/x-www-form-urlencoded\r\n");
			fwrite($asSocket, "Content-length: ".strlen($queryStr)."\r\n\r\n");
	
			fwrite($asSocket, $queryStr."\r\n\r\n");
	
			$buffer = '';
			while(!feof($asSocket)) {
				$buffer .= fread($asSocket, 8192);
			}
			fclose($asSocket);
	
			if(preg_match('/BADPASS/', $buffer)) {
				$this->errorMsg = 'Invalid username/password';
				$success = "FALSE";
			} else
			if(preg_match('/BADAUTH/', $buffer)) {
				$this->errorMsg = 'Authentication error: most likely bad username/password';
				$success = "FALSE";
			} else
			if(preg_match('/FAILED(.*)/', $buffer, $match)) {
				$this->errorMsg = 'General Failure '.$match;
				$success = "FALSE";
			} else
			if(preg_match('/OK/', $buffer)) {
				$this->errorMsg = 'Success';
				$success = "TRUE";
			} else { // Unknown Error
				$this->errorMsg = 'Unkown Error: '.$buffer;
				$success = "FALSE";
			}	
			if(preg_match('/INTERVAL (.*)/', $buffer, $match)) {
				$postInterval = $match;

				// TODO: Update interval counter here.  You know the one we don't have yet.	
			}

			// TODO: How to handle Resubmit and Retries since we aren't threaded?
			//  	 For now just return the result
			if($success == "TRUE") {
				// Clear the list.
 				$this->queuedTracks = array();
				$_SESSION['as_queued_tracks'] = array();
			}

			return $success;
		}
		
		/* @author Chris Hescott 
		 * @version 1/5/06
		 * @since 1/5/06
		 */
		function printCurrentQueue($msg = "") {
			$this->queuedTracks = $_SESSION['as_queued_tracks'];
			if(count($this->queuedTracks) == 0) {
				return TRUE;
			}
			writeLogData("as_debug", "\n---$msg----------------------------------");
			while( list($timestamp, $track) = each($this->queuedTracks) ) {
				writeLogData("as_debug", "Track: ". $track['track'] . " start time: ".$timestamp.
					     " correct stop time: ".$track['stoptime']. " current: ".time(). 
					     " retry: ".$track['retrycnt']);
			}
			writeLogData("as_debug", "\n--------------------------------------------------");
		}


		/* @author Chris Hescott 
		 * @version 1/05/06
		 * @since 1/05/06
		 */
		// Makes the query string for track i to be sent to audioscrobbler
		function BuildQueryString($track, $i) {
			$queryStr = "&a[$i]=".rawurlencode($track['artist']);
			$queryStr .= "&t[$i]=".rawurlencode($track['track']);
			$queryStr .= "&b[$i]=".rawurlencode($track['album']);
			$queryStr .= "&m[$i]=&l[$i]=".rawurlencode($track['length']);
			$queryStr .= "&i[$i]=".rawurlencode($track['time']);
			return $queryStr;
		}

		/* @author Chris Hescott 
		 * @version 1/05/06
		 * @since 1/05/06
		 */
		// Test all queued tracks for appropriate stop time and retry count
		// Note stop time is assumed to be current time so really this should only remove
		// the last song on the queue as any previously queued songs will have already passed the 
		// test on previous checks.
		function TestTrackLengths () {
			global $as_max_retry;

			$current_time = time();
			$resubmit = array();
			$i = 0;

			// Go through each song on the queue and determine if it played long enough
			while( list($timestamp, $track) = each($this->queuedTracks) ) {
				// Check to see if half of song was played and retry count threshold not exceeded 
				if($current_time > $timestamp + 0.5 * $track['length'] && $track['retrycnt'] < $as_max_retry ) { 
					$queryStr .= $this->BuildQueryString($track, $i);
					$i++;
					$track['retrycnt']++;
					$resubmit[$timestamp] = $track;
				} else {	// stopped prematurely or retried too many times
				}
			}
			# Save the Finished tracks in case we have submission problems with audioscrobbler site
			$this->queuedTracks = $_SESSION['as_queued_tracks'] = $resubmit;
			$this->printCurrentQueue("Songs to be submitted ($i)--Max Retry ($as_max_retry)");
			return $queryStr;
		}

	}
?>