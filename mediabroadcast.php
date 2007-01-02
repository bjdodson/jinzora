<?php define('JZ_SECURE_ACCESS','true');
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
	* - This page handles requests to stream all types of files (images,media,zip)
	*
	* @since 01.11.05
	* @author Ross Carlson <ross@jinzora.org>
	* @author Ben Dodson <ben@jinzora.org>
	* @author Chris Hescott
	*/
	
	$include_path = '';
	/* Session name separate from one used in index.php 
	* This one is embedded in the URL sent in the playlist
	*/	
	session_name('jza');
	session_start();
	
	// Let's set the error reporting level
	@error_reporting(E_ERROR);
	
 	include_once('system.php');
	@include_once('settings.php');
	include_once('backend/backend.php');
	include_once('playlists/playlists.php');
	include_once('lib/general.lib.php');
	include_once('lib/jzcomp.lib.php');
	include_once('services/class.php');
	
	$ssid = strip_tags(SID);
	writeLogData("as_debug","mediabroadcast: SID = ". $ssid);
	
	// Let's setup the services
	$jzSERVICES = new jzServices();
	$jzSERVICES->loadStandardServices();
	
	// Now let's see if we need to split the URL apart
	if (isset($_SERVER['PATH_INFO'])){
		// Ok, now we need to get the variables
		$vars = substr($_SERVER['PATH_INFO'],1);
		$vArr = explode("&",$vars);
		foreach($vArr as $item){
			// Now let's split that out
			$iArr = explode("=",$item);
			// Now let's set the variables
			$_GET[$iArr[0]] = $iArr[1];
		}
	}
	
	// Let's clean up the get vars
	$_GET = unurlize($_GET);
	
	// Let's get the user ID
	$uid = (isset($_GET['jz_user'])) ? $_GET['jz_user'] : false;
	if (false === $uid || $uid == ""){ exit(); }
	
	// Let's setup the new user
	$jzUSER = new jzUser(false,$uid);
	
	// ACTIONS:				
	// play [for on-the-fly, basic playlist generation]
	//    -path
	//    -limit (default max_playlist_length)
	//    -type (specify the type of the path: track|node|playlist; default is to assume node.)
	//    -resample (NOT IMPLEMENTED)
	//
	// download [downloading tracks/nodes/playlist] (NOT IMPLEMENTED)
	//     -path
	//     -type (is this a track or a node? default: assume node.)
	//     -playlist (if this is set, ignore path)
	//
	// image [for displaying images; this makes it possible for images to not be in the webroot]
	//    -path (image path)
	//
	//    The following are for GD-based resizing, NOT HTML tag resizing:
	//    -width (resize the width)
	//    -height (resize the height)
	//    -constrain (constrained resize)
	if (!isset($_GET['action'])) $_GET['action'] = "play";
	
	// Handle $resample
	$resample = (isset($_GET['resample'])) ? $_GET['resample'] : false;
	
	if ($jzUSER->getSetting('resample_lock')){
		$resample = $jzUSER->getSetting('resample_rate');
	}
	
	if (isset($no_resample_subnets) && $no_resample_subnets <> "" && preg_match("/^${no_resample_subnets}$/", $_SERVER['REMOTE_ADDR'])) {
		$resample = false;
	}
	
	switch ($_GET['action']) {
		// play a track:
		case "play":
			if (!(isset($_GET['jz_path']) || isset($_GET['file'])))
				exit();

			// send file directly, not with path:
			if (isset($_GET['file'])) {
				if (preg_match("/\.($audio_types)$/i", $_GET['fname'])) {
				  sendMedia($_GET['file'],$_GET['fname'],$resample);
				}
				exit();
			} else {	
				$be = new jzBackend();
				$el = &new jzMediaTrack($_GET['jz_path'],"id");

				if (isset($_GET['cl'])) {
				  // Send a clip
				  $meta = $el->getMeta();
				  $title = $meta['artist'] . " - " . $el->getName();			  
				  sendClip($el);
				  exit();
				}
				
				// Is the track locked/do they have permission?
				if (!canPlay($el,$jzUSER)) {
				  sendMedia("playlists/messages/media-locked.mp3",word("Track Locked"));
				  exit();
				}
				// Have they reached their limit?
				if (!checkStreamLimit($el)){
				  sendMedia("playlists/messages/streaming-limit-exceeded.mp3",word("Limit Reached"));
				  exit();
				}
				
				if (!isset($_GET['sid'])) {
				  $_GET['sid'] = "none";
				}
				
				if (!isset($_SERVER['HTTP_RANGE'])) {
					// Now let's update the playcount
					if ($be->allowPlaycountIncrease($_GET['jz_user'],$el,$_GET['sid']) !== false) {
						$el->increasePlayCount();
					}	
					
					// Now let's update audioscrobbler
					$el->setStartTime(time());
					if ($enable_audioscrobbler == "true"){
						$jzSERVICES->loadService("reporting","audioscrobbler");
						$jzSERVICES->updatePlayCountReporting($el);
					}
				}
				$meta = $el->getMeta();
				if ($meta['artist'] <> "" and $meta['artist'] <> "-"){
					$title = $meta['artist'] . " - " . $el->getName();
				} else {
					$title = $el->getName();
				}

				if (isset($_GET['jz_user'])) {
				  $be->setPlaying($_GET['jz_user'],$el,$_GET['sid']);
				}
				session_write_close(); // Close session while file is streaming
				sendMedia($el->getFileName("host"),$title, $resample);
			}
			exit();
		break;
		
		// download a collection of media:   
		case "image":
			if (preg_match("/\.($ext_graphic)$/i", $_GET['jz_path'])) {
				showImage($_GET['jz_path']);
			}
			exit();
		break;
	}

?>
