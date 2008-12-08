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
	* Contains the basic Jukebox class
	*
	* @since 2/9/05
	* @author Ross Carlson <ross@jinzora.org>
	*/

	class jzJukebox {
	  var $id;


	  static function getJbArr() {
	    global $jbArr;

	    if  (isset($jbArr)) {
	      return $jbArr;
	    }

	    @include_once($include_path. "jukebox/settings.php");


	    /**
	     * Get names of available 'quickboxes'
	     */
	    $backend = new jzBackend();
	    $boxes = $backend->loadData('quickboxes');
	    $newboxes = array();
	    $clear_old = false;
	    if ($boxes) {
	      foreach ($boxes as $id=>$box) {
		if ($box['poll_time']*5 + $box['active_time'] < time()) {
		  $clear_old  = true;
		  continue;
		}
		$newboxes[$id] = $box;
		$jb = array('type'=>'quickbox');
		$jb['description'] = $box['id'];
		 
		$jbArr[] = $jb;
	      }

	      // clear old jukeboxes
	      if ($clear_old) {
		$backend->storeData('quickboxes',$newboxes,1);
	      }
	    }


	    return $jbArr;
	  }

		/**
		* Constructor for the class.
		* 
		* @author Ross Carlson
		* @version 2/9/05
		* @since 2/9/05
		*/
		function jzJukebox(){
			global $include_path, $jbArr;

			jzJukebox::getJbArr();
			// Ok, now we need to include the right subclass for this player
			if (!isset($_SESSION['jb_id']) || $_SESSION['jb_id']>=sizeof($jbArr)){ $_SESSION['jb_id'] = 0; }
			$this->id = $_SESSION['jb_id'];


			// Now let's make sure they have installed the jukebox
			if (!isset($jbArr[0]['type'])){ 
				// Let's take them through the installer
				$this->install();
			}
			writeLogData("messages","Jukebox: building the jukebox object of type " . $this->getSetting('type'));
			include_once($include_path. "jukebox/jukeboxes/". $this->getSetting('type'). ".php");
		}


		/**
		* Sets up the jukebox installer
		* 
		* @author Ross Carlson
		* @version 11/20/05
		* @since 11/20/05
		*/
		function install(){
			global $include_path;
			
			// What step are they on?
			if (isset($_POST['edit_step'])){
				// Ok, let's display step two by including the right jukebox and running it's installer
				include_once($include_path. "jukebox/jukeboxes/". $_POST['edit_jukebox_type']. ".php");
				jbInstall($_POST['edit_step']);
			}
			?>
			<strong>Welcome to the Jinzora Jukebox Installer</strong><br><br>
			This wizard will guide you through the process of configuring Jinzora to work in Jukebox mode.<br><br>
			<form method="post">
				Jukebox Type: 
				<select name="edit_jukebox_type" class="jz_select">
					<option value="mpd">MPD (Linux)</option>
					<option value="winamp3">Winamp (Windows)</option>
				</select>
				<input type="submit" value="Next ->" name="step2" class="jz_submit">
				<input type="hidden" name="edit_step" value="2">
			</form>
			<br><br>
			<?php
			exit();
		}

		/**
		* Returns a keyed array showing all the functions that this jukebox supports
		* 
		* @author Ross Carlson
		* @version 2/9/05
		* @since 2/9/05
		* @param return Returns a keyed array of the jukeboxe's abilities
		*/
		function jbAbilities(){
			return returnJBAbilities();
		}
		
		/**
		* Returns the stats of the jukebox
		* 
		* @author Ross Carlson
		* @version 2/9/05
		* @since 2/9/05
		* @param return Returns a keyed array of the jukeboxe's abilities
		*/
		function returnJBStats(){
			return retJBStats();
		}
		
		/**
		* Connects to the player and returns true or false based on what happens
		* 
		* @author Ross Carlson
		* @version 2/9/05
		* @since 2/9/05
		* @param return Returns true or false
		*/
		function connect(){
			return playerConnect();
		}
		
		/**
		* Displays add on tools next to the playlist box
		* 
		* @author Ross Carlson
		* @version 2/9/05
		* @since 2/9/05
		* @param return Returns the path to the current track
		*/
		function getAddOnTools(){
			return getAllAddOnTools();
		}
		
		/**
		* returns the currently playing track path so we can get the node
		* 
		* @author Ross Carlson
		* @version 2/9/05
		* @since 2/9/05
		* @param return Returns the path to the current track
		*/
		function getCurrentTrackPath(){
			return getCurTrackPath();
		}
		
		/**
		* returns the currently playing track number
		* 
		* @author Ross Carlson
		* @version 2/9/05
		* @since 2/9/05
		* @param return Returns the currently playling track number
		*/
		function getCurrentPlayingTrack(){
			return getCurPlayingTrack();
		}
		
		/**
		* returns the currently playing playlist
		* 
		* @author Ross Carlson
		* @version 2/9/05
		* @since 2/9/05
		* @param return Returns the currently playling playlist
		*/
		function getCurrentPlaylist(){
			return getCurPlaylist();
		}
		
		/**
		* Passes a playlist to the jukebox
		* 
		* @author Ross Carlson
		* @version 2/9/05
		* @since 2/9/05
		* @param $playlist The playlist that we are passing
		*/
		function passPlaylist($playlist){
			playlist($playlist);
		}
		
		/**
		* Passes a command to the jukebox player
		* 
		* @author Ross Carlson
		* @version 2/9/05
		* @since 2/9/05
		* @param $command The command that we passed to the player
		*/
		function passCommand($command){
		  control($command);
		}
		
		/**
		* Returns the status of the player
		* 
		* @author Ross Carlson
		* @version 2/9/05
		* @since 2/9/05
		*/
		function getPlayerStatus($type = "playback"){
			return getStatus($type);
		}
		
		/**
		* Gets the current track that is playing by the jukebox
		* 
		* @author Ross Carlson
		* @version 2/9/05
		* @since 2/9/05
		*/
		function getCurrentTrackName(){
			return getCurTrackName();
		}
		
		/**
		* Gets the ammount of time remaining in the current track
		* 
		* @author Ross Carlson
		* @version 2/9/05
		* @since 2/9/05
		* @param return returns the amount of time remaining in seconds
		*/
		function getCurrentTrackRemaining(){
			return getCurTrackRemaining();
		}		
		
		/**
		* Gets the length of the current track
		* 
		* @author Ross Carlson
		* @version 2/9/05
		* @since 2/9/05
		* @param return returns the amount of time remaining in seconds
		*/
		function getCurrentTrackLength(){
			return getCurTrackLength();
		}
		
		/**
		* Gets the length location of the track (how many seconds into the track we are)
		* 
		* @author Ross Carlson
		* @version 2/9/05
		* @since 2/9/05
		* @param return returns the amount of time played in seconds
		*/
		function getCurrentTrackLocation(){
			return getCurTrackLocation();
		}

		/**
		* Updates the database, if required by the jukebox.
		* 
		* @author Ben Dodson
		* @version 12/08/06
		* @since 12/08/06
		* 
		*/
		function updateDB($node, $recursive, $root_path){
			if (function_exists("updateJukeboxDB")) {
				return updateJukeboxDB($node, $recursive, $root_path);
			}
			return false;
		}

		/**
		* Gets a setting for the jukebox from jbArr.
		* 
		* @author Ben Dodson
		* @version 12/16/05
		* @since 12/16/05
		*/
		function getSetting($name){
		  global $jbArr;
		  
		  if (isset($jbArr[$this->id][$name])) {
		    return $jbArr[$this->id][$name];
		  } else {
		    return false;
		  }
		}

	  
	}
?>
