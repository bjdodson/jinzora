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
	* Please see http://www.jinzora.org/modules.php?op=modload&name=jz_whois&file=index
	* 
	* - Code Purpose -
	* These are the classes extended by the backend adaptors.
	*
	* @since 05.10.04
	* @author Ben Dodson <bdodson@seas.upenn.edu>
	*/
	
	class jzBackendClass {
		
		var $name;
		var $details;
		var $version;
		var $data_dir;
		
		/**
		* Constructor wrapper for a jzBackend
		* 
		* @author Ben Dodson
		* @version 9/06/04
		* @since 9/04/04
		*/
		
		function jzBackendClass() {
			return $this->_constructor();
		}
	
		/**
		* Constructor code for a jzBackend
		* 
		* @author Ben Dodson
		* @version 9/06/04
		* @since 9/04/04
		*/	
		function _constructor() {
			global $backend, $version, $include_path;
			
			$this->name = $backend;
			$this->details = "This is a cache-based backend that uses your filesystem hierarchy to determine the music layout.";
			$this->version = $version;
			$this->data_dir = $include_path. "data/backend";
			return true;
		}
		
		/**
		* Gets a storage directory for the backend's data.
		* 
		* @author Ben Dodson
		* @version 11/12/04
		* @since 11/12/04
		*/
		
		function getDataDir() {
			return $this->data_dir;
		}
		
		/**
		 * Checks if the backend has a certain feature.
		 * 
		 * @author Ben Dodson
		 * @version 8/17/05
		 * @since 8/17/05
		 */
	         function hasFeature($f) {
		   switch ($f) {
		   case "setID":
		   case "charts":
		     return false;
		     break;
		   }
		   return true;
		}

		/**
		* Installation for the backend.
		* This allows for installs that require a web interface
		* and those that don't.
		*
		* Returns 1 when complete
		* Returns 0 when still in progress (requires info from the web)
		* Returns -1 if failed.
		* 
		* Example call to install():
		* 
		* $back = &new jzBackend();
		* if (($val = $back->install()) == 0)
		* 	exit();
		* if ($val == -1)
		*	die ("problem during installation")
		* // do whatever is next.
		* QUESTION: should this update the cache too?
		*
		*
		* @author Ben Dodson
		* @version 11/13/04
		* @since 9/04/04
		*/
		
		function install() {
			global $backend;
			// the default adaptor does not need a web-based install.
			// just store a few variables and return 1, since we are complete.
			$this->install_be();
			$this->install_users();
			
			
			
			echo "The backend has been installed.";
			// now populate it.
			// done.
			return 1;
		}
		
		/**
		* Stores backend information, used by most backends
		* (not in a database)
		* 
		* @author Ben Dodson
		* @version 11/13/04
		* @since 11/13/04
		*/
		function install_be() {
			
			$datapath = $this->data_dir;
			$filename = $datapath . "/backend";		
		
			if (file_exists($filename)) {
				$config = unserialize(file_get_contents($filename));
			}
			else {
				$config = array();
				$config['updated']['root'] = 0;
			}
			$config['name'] = $this->name;
			$config['details'] = $this->details;
			$config['version'] = $this->version;
			
			if (!$handle = @fopen($filename,"w")) {
				touch($filename);
				if (!$handle = @fopen($filename,"w")) {
					echo "Could not open the data file in " . $this->data_dir . ".";
					return -1;
				}
			}
			fwrite($handle,serialize($config));
			fclose($handle);
		}
		
		/**
		* Sets up user backend.
		* 
		* @author Ben Dodson
		* @version 11/20/04
		* @since 11/20/04
		*/
		function install_users() {
			global $backend;
			
			$datapath = $this->data_dir;
			
			// USERS:
			$filename = $datapath . "/users";
			if (!isset($password)){$password="";}
			if (!file_exists($filename)) {
				$users = array();
				$users['NOBODY']['password'] = jz_password($password);
				$users['NOBODY']['id'] = uniqid("USR");
				
				if (!$handle = @fopen($filename,"w")) {
					touch($filename);
					if (!$handle = @fopen($filename,"w")) {
						echo "Could not open the data file in " . $this->data_dir . ".";
						return -1;
					}
				}
				fwrite($handle,serialize($users));
				fclose($handle);	
			}
			// USER SETTINGS:
			$filename = $datapath . "/user_settings";
			if (!file_exists($filename)) {
				$usersettings = array();
				if (!$handle = @fopen($filename,"w")) {
					touch($filename);
					if (!$handle = @fopen($filename,"w")) {
						echo "Could not open the data file in " . $this->data_dir . ".";
						return -1;
					}
				}
				fwrite($handle,serialize($usersettings));
				fclose($handle);	
			}
			// GROUPS:
			$filename = $datapath . "/groups";
			if (!file_exists($filename)) {
				$groups = array();
				$groups[ALL_MEDIA_GROUP] = ALL_MEDIA_GID;
				if (!$handle = @fopen($filename,"w")) {
					touch($filename);
					if (!$handle = @fopen($filename,"w")) {
						echo "Could not open the data file in " . $this->data_dir . ".";
						return -1;
					}
				}
				fwrite($handle,serialize($groups));
				fclose($handle);	
			}
		}
		
		
		/**
		* Gets the backend name.
		* 
		* @author Ben Dodson
		* @version 9/04/04
		* @since 9/04/04
		*/
		
		function getName() {
			global $backend;
			
			$datapath = $this->data_dir;
			$filename = $datapath . "/backend";
			$config = unserialize(file_get_contents($filename));
			
			return $config['name'];
		}
		
		/**
		* Gets the backend details.
		* 
		* @author Ben Dodson
		* @version 9/04/04
		* @since 9/04/04
		*/
		
		function getDetails() {
			global $backend;
			
			$datapath = $this->data_dir;
			$filename = $datapath . "/backend";
			$config = unserialize(file_get_contents($filename));
			
			return $config['details'];
		}
		
		/**
		* Gets the backend version.
		* 
		* @author Ben Dodson
		* @version 9/04/04
		* @since 9/04/04
		*/
		
		function getVersion() {
			global $backend;
			
			$datapath = $this->data_dir;
			$filename = $datapath . "/backend";
			$config = unserialize(file_get_contents($filename));
			
			return $config['version'];
		}
		
		/**
		* Gets the time the database was last updated.
		* 
		* @author Ben Dodson
		* @version 10/15/04
		* @since 10/15/04
		*/
		
		function getUpdated($dir = false) {
			global $backend;
			
			if ($dir === false)
				$dir = 'root';
			
			$datapath = $this->data_dir;
			$filename = $datapath . "/backend";
			$config = unserialize(file_get_contents($filename));
			
			return isset($config['updated'][$dir]) ? $config['updated'][$dir] : 0;
		}
		
		/**
		* Sets the last updated of the $dir to $when (or now)
		* 
		* @author Ben Dodson
		* @version 11/13/04
		* @since 10/15/04
		*/
		
		function setUpdated($dir = false, $when = false) {
			global $backend;
			if ($dir === false)
				$dir = 'root';
			
			if ($when === false)
				$when = date("U");
			
			$datapath = $this->data_dir;
			$filename = $datapath . "/backend";
			$config = unserialize(file_get_contents($filename));
			
			$config['updated'][$dir] = $when;
			$handle = fopen($filename,"w");
			fwrite($handle,serialize($config));
			fclose($handle);
		}
		
		/**
		* Clears our updated times.
		* 
		* @author Ben Dodson
		* @version 11/13/04
		* @since 11/13/04
		*/
		function clearUpdated() {
			global $backend;

			$datapath = $this->data_dir;
			$filename = $datapath . "/backend";
			$config = unserialize(file_get_contents($filename));
			
			$config['updated'] = array();
			$handle = fopen($filename,"w");
			fwrite($handle,serialize($config));
			fclose($handle);
		}
		

		/**
		 * Sets the 'currently playing' info for the user.
		 *
		 * @author Ben Dodson
		 * @since 1/23/05
		 * @version 1/23/05
		 **/
		function setPlaying($user, $track, $sid) {
			global $jzUSER;
			
			// Let's set the file that will store this data
			$filename = $this->getDataDir() . "/now-playing.dat";
			
			// Let's see if it exists and if it does lets load it
			if (file_exists($filename)) {
				$array = unserialize(file_get_contents($filename));
			} else {
				$array = array();
			}
			
			// Let's pull all the meta data from the current playing track
			$meta = $track->getMeta();
			
			// Let's create the array to store
			$path = $track->getDataPath("String");
			$array[$user][$sid]['track'] = $track->getName();
			$array[$user][$sid]['user'] = $user;
			$array[$user][$sid]['name'] = $jzUSER->lookupName($user);
			$array[$user][$sid]['fullname'] = $jzUSER->getSetting('fullname');
			$array[$user][$sid]['length'] = $meta['length'];
			$array[$user][$sid]['time'] = time();
			$array[$user][$sid]['artist'] = $meta['artist'];
			$array[$user][$sid]['album'] = $meta['album'];
			$array[$user][$sid]['path'] = $track->getPath("String");
			$array[$user][$sid]['fpath'] = $track->getDataPath("String");
			$handle = fopen($filename,"w");
			fwrite($handle,serialize($array));
			fclose($handle);
			
			// Now let's track this so we'll know what each user has played
			$this->storeData("playhistory-". $user, $this->loadData("playhistory-". $user). "\n". $track->getPath("String"));
		}

		/**
		 * Unsets the 'currently playing' info for the user.
		 * If $user is false, removes all tracks.
		 * If $sid is false, removes all tracks for $user.
		 *
		 * @author Ben Dodson
		 * @since 1/23/05
		 * @version 1/23/05
		 **/
		function unsetPlaying($user = false, $sid = false) {
			$filename = $this->getDataDir() . "/now-playing.dat";

			if (file_exists($filename)) {
				$array = unserialize(file_get_contents($filename));
			} else {
				$array = array();
			}

			// First let's make sure we didn't do this too fast
			// If we're using some players they just buffer too fast
			// And we think the file is done when it's not			
			if (isset($array[$user][$sid]['path'])){
				$track = new jzMediaTrack($array[$user][$sid]['path']);
				$meta = $track->getMeta();
			} else {
				$meta = array();
				$meta['length'] = 60;
			}
			
			if ($user === false) {
			  $array = array();
			} else if ($sid === false) {
			  unset($array[$user]);
			} else if (((time() + $meta['length']) -30) < time()) {
			  unset($array[$user][$sid]);
			}
			
			$handle = fopen($filename,"w");
			fwrite($handle,serialize($array));
			fclose($handle);
		}

		
		/**
		 * Sets the 'currently playing' info for the user.
		 * Note that this must be called before calling increasePlaycount
		 * on the track, or else the playcount won't increase.
		 *
		 * @author Ben Dodson
		 * @since 1/23/05
		 * @version 1/23/05
		 **/
		function getPlaying() {
		  	$filename = $this->getDataDir() . "/now-playing.dat";
		  
			if (file_exists($filename)) {
				$array = unserialize(file_get_contents($filename));
			} else {
				$array = array();
			}
			
			if (sizeof($array) == 0) {
				return $array;
			}
			
			// Ok, now let's clean up tracks to make sure that nothing stuck around too long...
			$modify = false;
			$net_modify = false;
			foreach($array as $user=>$val1) {
				foreach($val1 as $data=>$val2){
					// Ok, now let's make sure this song isn't really done since it's been too long
					if (($val2['time'] + ($val2['length'] * 1.1)) < time()){
						// Ok, these must be old, let's wack them
						$modify = true;
					}
				}
				if ($modify){
				  unset($array[$user][$data]);
				  $modify = false;
				  $net_modify = true;
				}
			}

			if ($net_modify) {
			  // Ok, we need to clean up
			  $handle = fopen($filename,"w");
			  fwrite($handle,serialize($array));
			  fclose($handle);
			}
			
			// Now return them some data.
			$retArray = array();
			
			// Now let's find what this user is streaming
			foreach($array as $user) {
			  $retArray = array_merge($retArray,$user);
			}
			
			return $retArray;
		}

		/* Checks to see if this file's playcount should be increased (if it wasn't played too recently)
		 *
		 * @author Ben Dodson
		 * @since 1/28/05
		 * @version 1/28/05
		 *
		 **/
		function allowPlaycountIncrease($user, $el, $sid) {
		  $arr = $this->getPlaying();
		  $tpath = $el->getPath("String");
		  
		  if (isset($arr) && isset($arr[$user]) && isset($arr[$user][$sid])) {
		  	if ($arr[$user][$sid]['path'] == $tpath) {
		  		return false;
		  	}
		  }
		  
		  return true;
		}
		
		/*
		 * Stores arbitrary data.
		 *
		 * @author Ben Dodson
		 * @version 2/14/05
		 * @since 2/14/05
		 * @param data_id an indentifier for the data.
		 * @param data the data to be stored.
		 * @param cache_duration the amount of time until the data expires (in days).
		 */
		function storeData($data_id, $data, $cache_duration = 0) {
		  $dp = $this->data_dir . "/backend-" . $data_id;

		  if (!$handle = @fopen($dp,"w")) {
		    die("Could not open file " . $dp . " for writing.");
		  }
		  fwrite($handle,serialize($data));
		  fclose($handle);

		  if ($cache_duration > 0) {
		    $cache = $this->loadData('cache_timeouts');
		    if (!is_array($cache)) {
		      $cache = array();
		    }
		    $cache[$data_id] = time() + ($cache_duration*24*60*60);
		    $this->storeData('cache_timeouts',$cache);
		  }
		}

		/*
		 * Loads arbitrary data for the user.
		 *
		 * @author Ben Dodson
		 * @version 2/14/05
		 * @since 2/14/05
		 * @param data_id an indentifier for the data.
		 */
		function loadData($data_id, $timeout = false) {
		  $dp = $this->data_dir . "/backend-" . $data_id;

		  if (!file_exists($dp)) {
		    return false;
		  }
		  
		  if ($timeout) {
		    $cache = $this->loadData('cache_timeouts');
		    if (is_array($cache) && isset($cache[$data_id])) {
		      if (time() > $cache[$data_id]) {
						unset($cache[$data_id]);
						$this->storeData('cache_timeouts',$cache);
						return false;
		      }
		    }
		  }

		  return @unserialize(@file_get_contents($dp));
		}

		/*
		 * Removes a piece of data.
		 *
		 * @author Ben Dodson
		 * @since 2/14/05
		 * @version 2/14/05
		 *
		 **/
		function removeData($data_id) {
		  $dp = $this->data_dir . "/backend-" . $data_id;
			
		  @unlink($dp);
		}

		function registerFile($filename, $pathArray) {
			if (file_exists($filename)) {
				$fs_sync = 'true';
				$path = $this->data_dir . "/REG-" . pathize(dirname($filename));
				$index = $filename; //substr($filename, strlen(dirname($filename))+1);
				if (is_dir($filename)) {
					$type = "node";
				} else {
					$type = "track";
				}
			} else {
				$fs_sync = 'false';
				$path = "/REG";
				$index = $filename;
				$type = "track";
			}
			
			if (file_exists($path)) {
				$arr = unserialize(file_get_contents($path));
			} else {
				$arr = array();
			}
			
			$arr[$index]['path'] = $pathArray;
			$arr[$index]['added'] = time();
			$arr[$index]['fs_sync'] = $fs_sync;
			
			if (!$handle = @fopen($path,"w")) {
		   		die("registerFile: Could not open file " . $path . " for writing.");
		  	}
		  	fwrite($handle,serialize($arr));
		  	fclose($handle);
		}
		
		
		
		function unregisterFile($filename) {
			if (file_exists($filename)) {
				$fs_sync = 'true';
				$path = $this->data_dir . "/REG-" . pathize(dirname($filename));
				$index = $filename; //substr($filename, strlen(dirname($filename))+1);
			} else {
				$fs_sync = 'false';
				$path = "/REG";
				$index = $filename;
			}
			
			$arr = unserialize(file_get_contents($path));
			unset($arr[$filename]);
			
			if (sizeof($arr) == 0) {
				unlink($path);
			} else {
				if (!$handle = @fopen($path,"w")) {
			   		die("unregisterFile: Could not open file " . $path . " for writing.");
			  	}
			  	fwrite($handle,serialize($arr));
			  	fclose($handle);
			}
		}
		
		function lookupFile($filename) {
			if (file_exists($filename)) {
				$path = $this->data_dir . "/REG-" . pathize(dirname($filename));
				$index = $filename; //substr($filename, strlen(dirname($filename))+1);
			} else {
				$path = "/REG";
				$index = $filename;
			}
			
			if (file_exists($path)) {
				$arr = unserialize(file_get_contents($path));
				if (isset($arr[$index])) {
					return $arr[$index];
				}
			}
			
			return false;
		}
		
		function removeDeadFiles($folder = false, $recursive = true) {
			$root = new jzMediaNode();
			
			$recursive_repeat = false;
			if ($folder !== false) {
				$fbase = "REG-" . pathize($folder);
			} else {
				$fbase = "REG";
			}
			$handle = opendir($this->data_dir);
			while ($file = readdir($handle)) {
				$fullpath = $this->data_dir . '/' . $file;
				if (false !== stristr($file,$fbase)) {
					if ($recursive || $file == $fbase) {
						$modified = false;
						$arr = unserialize(file_get_contents($fullpath));
						foreach ($arr as $f => $info) {
							if ($info['fs_sync'] == "true" && !file_exists($f)) {
								$modified = true;
								unset($arr[$f]);
								if ($info['type'] == "track") {
									$root->removeMedia(new jzMediaTrack($info['path']));
								} else {
									$root->removeMedia(new jzMediaNode($info['path']));
									$recursive_repeat = true;
								}
							}
						}
						if ($modified) {
							if (sizeof($arr) == 0) {
								unlink($fullpath);	
							} else {
								$handle2 = fopen($fullpath,"w");
		  						fwrite($handle2,serialize($arr));
		  						fclose($handle2);
							}
						}
					}
				}
			}
			if ($recursive_repeat) {
				$this->removeDeadFiles($folder,true);
			}
		}

}		

?>
