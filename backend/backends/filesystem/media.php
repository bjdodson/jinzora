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
	* This is the media backend for the database adaptor.
	*
	* @since 05.10.04
	* @author Ross Carlson <ross@jinzora.org>
	*/
	// Most classes should be included from header.php
	
	
	// The music root is $media_dirs, (but should only be one directory).
	class jzMediaNode extends jzRawMediaNode {

		/**
		* Constructor wrapper for jzMediaNode.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 5/14/04
		* @since 5/13/04
		*/
		function jzMediaNode($par = array(),$mode="path") {
			$this->_constructor($par,$mode);
			
		}

		/**
		* Returns a string that points to the location
		* where this node's non-jinzora-specific data should be stored
		* (album art, text, etc.)
		* 
		* @author Ben Dodson
		* @version 9/18/04
		* @since 9/18/04
		*/
		function getDataPath() {
			global $data_in_filesystem;
			
			if ($data_in_filesystem) {
				$this->getPath("String");
			}
			else {
				return $this->data_dir; // THIS IS NOT RIGHT. WHERE SHOULD IT GO? /backend/data? /data?
			}
		}
		
		function getFilePath() {
			global $media_dirs;
			return $media_dirs . '/' . $this->getPath("String");
		}
		
		/**
		* Counts the number of subnodes $distance steps down.
		* $distance = -1 does a recursive count.
		*
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 5/14/2004
		* @since 5/14/2004
		*/
		function getSubNodeCount($type='both', $distance=false) {
		
			global $web_root, $root_dir, $media_dirs, 
				$audio_types, $video_types;
			
			$fullpath = $media_dirs;
			$mypath = $this->getPath("String");
			$fullpath .= "/" . $mypath;
			$sum = 0;
			
			if ($type == "tracks") {
				$type = "leaves";
			}
			
			if ($distance === false) {
				$distance = $this->getNaturalDepth();	
			}
			
			$path_array = array($fullpath);
			$distance_array = array(0);
			if (0 <= $distance && $distance <= 1) {
				while ($path_array != array()) {
					$p = array_pop($path_array);
					$d = array_pop($distance_array);
					$dir = opendir($p);
					while ($file = readdir($dir)) {
						if ($file == "." || $file == "..") {
							continue;
						}
						else if ($distance < 0) {
							if (preg_match("/\.($audio_types)$/i", $file) || preg_match("/\.($video_types)$/i", $file)) {
								if ($type == "leaves" || $type == "both") {
									$sum++;
								}
							}
							else if (is_dir($p."/".$file)) {
								if ($type == "nodes" || $type == "both") {
									$sum++;
								}
								array_push($path_array, $p."/".$file);
								array_push($distance_array, $d+1);
							}
						}
						else if ($d == $distance) {
							if ($type == "nodes" || $type == "both") {
								$sum++;
							}
						}
						else if ($d+1 == $distance) {
							if (preg_match("/\.($audio_types)$/i", $file) || preg_match("/\.($video_types)$/i", $file)) {
								if ($type == "leaves" || $type == "both") {
									$sum++;
								}
							} 
							else if (is_dir($p."/".$file)) {
								if ($type == "nodes" || $type == "both") {
									$sum++;
								}
							}
						}
						else if ($d < $distance) {
							if (is_dir($p."/".$file)) {
								array_push($path_array, $p."/".$file);
								array_push($distance_array, $d+1);
							}
						}
					}
				}
				return $sum;
			}
			else {
				return parent::getSubNodeCount($type, $distance);
			}
		}

		/**
		* Returns the subnodes as an array.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 5/15/2004
		* @since 5/14/2004
		*/
		function getSubNodes($type='nodes', $distance=false, $random=false, $limit=0) {
			global $web_root, $root_dir, $media_dirs, $audio_types, $video_types;
			$fullpath = $media_dirs;
			$mypath = $this->getPath("String");
			$fullpath .= "/" . $mypath;
			
			if ($type == "tracks") {
				$type = "leaves";
			}
			
			if ($distance === false) {
				$distance = $this->getNaturalDepth();	
			}

			if (0 <= $distance && $distance <= 1) { // Handle it.
				$arr = array();
				
				if ($distance == 0) {
					return array($this);
				}
				else {
					if (!($handle = opendir($fullpath))) 
						die("Could not access directory $path");
					while ($file = readdir($handle)) {
						if ($file == "." || $file == "..") {
							continue;
						}
						else {
							$newpath = $mypath . "/" . $file;
							if (is_dir($fullpath . "/" . $file) && ($type == "nodes" || $type == "both")) {
								$next = &new jzMediaNode($newpath);
								$ndistance = ($distance == -1) ? -1 : $distance - 1;
								$more = $next->getSubNodes($type,$ndistance,$random,$limit);
								for ($i = 0; $i < sizeof($more); $i++) {
									$arr[] = $more[$i];
								}
							}
							if (preg_match("/\.($audio_types)$/i", $file)
							    || preg_match("/\.($video_types)$/i", $file)) {
								if ($type == "leaves" || $type == "both") {
									if ($distance == 1 || $distance == -1) {
										$arr[] = new jzMediaTrack($newpath);
									}
								}
							}
						}
					} 
				}
				if ($random) {
					srand((float)microtime() * 1000000);
					shuffle($arr);
				}
				else {
					usort($arr, "compareNodes");
				}
				
				if ($limit > 0 && $limit < sizeof($arr)) {
					$final = array();
					for ($i = 0; $i < $limit; $i++) {
						$final[] = $arr[$i];
					}
					return $final;
				}
				else {
					return $arr;
				}
			}
			else { // use the cache
				return parent::getSubNodes($type, $distance, $random, $limit);
			}
		}
	

		/**
		* Alphabetical listing of a node.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 5/14/2004
		* @since 5/14/2004
		*/	
		function getAlphabetical($letter, $distance = false) {
			global $web_root, $root_dir, $media_dirs, $audio_types, $video_types;
			
			if ($distance === false) {
				$distance = $this->getNaturalDepth();	
			}
			
			$fullpath = $media_dirs;
			$mypath = $this->getPath("String");
			$fullpath .= "/" . $mypath;
			$name = strtolower($this->getName());
			$letter = strtolower($letter);
			
			if (0 <= $distance && $distance <= 1) { // handle it.
						
				$arr = array();
				
				if ($distance == 0 || $distance == -1) {
					if ($letter == "#") {
						if (!('a' <= $name[0] && $name[0] <= 'z')) {
							return array($this);
			    			}
					}
					else if ($letter == "*") {
						return array($this);
					}
					else {
						if ($name[0] == $letter) {
							return array($this);
						}
					}
					
					return;
				}
				else {
					if (!($handle = opendir($fullpath))) 
						die("Could not access directory $path");
					while ($file = readdir($handle)) {
						if ($file == "." || $file == "..") {
							continue;
						}
						else {
							$newpath = $mypath . "/" . $file;
							if (is_dir($fullpath . "/" . $file)) {
								$next = &new jzMediaNode($newpath);
								$ndistance = ($distance == -1) ? -1 : $distance - 1;
								$more = $next->getAlphabetical($letter,$ndistance);
								for ($i = 0; $i < sizeof($more); $i++) {
									$arr[] = $more[$i];
								}
							}
							if (preg_match("/\.($audio_types)$/i", $file)
							    || preg_match("/\.($video_types)$/i", $file)) {
							    	if ($distance == -1 || $distance == 1) {
							    		if ($letter == "#") {
							    			if (!('a' <= $file[0] && $file[0] <= 'z')) {
							    				$arr[] = jzMediaTrack($newpath);
							    			}
							    		}
							    		else if ($letter == "*") {
							    			$arr[] = jzMediaTrack($newpath);
							    		}
							    		else {
									    	if ($file[0] == $letter) {
											$arr[] = jzMediaTrack($newpath);
										}
									}
								}
							}
						}
					} 
				}
				usort($arr,"compareNodes");
				return $arr;
			}
			else { // It would be too slow. Use the cache.
				return parent::getAlphabetical($letter, $distance);
			}
		}
	
		/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 * Do NOT modify the below: modify overrides.php instead,        *
		 * change to jinzora/backend, and run `php global_include.php`   *
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
		// begin global_include: overrides.php
		/* * * * * * * * * * * * * * * * * * *
		 *            Overrides              *
		 * * * * * * * * * * * * * * * * * * */
		// TO ADD: getMainArt(), getDescription()
		// Then change to the directory called backend and run 'php global_include.php' to add the changes.
		// end global_include: overrides.php
		
	}

	class jzMediaTrack extends jzRawMediaTrack {
	
		/**
		* Constructor wrapper for jzMediaTrack.
		* 
		* @author 
		* @version 
		* @since 
		*/
		function jzMediaTrack($par = array(),$mode="path") {
			$this->_constructor($par,$mode);
		}
		
		/**
		* Returns a string that points to the location
		* where this node's non-jinzora-specific data should be stored
		* (album art, text, etc.)
		* 
		* @author Ben Dodson
		* @version 9/18/04
		* @since 9/18/04
		*/
		function getDataPath() {
			$node = $this->getParent();
			return $node->getDataPath();
		}
		
		// begin global_include: overrides.php
		/* * * * * * * * * * * * * * * * * * *
		 *            Overrides              *
		 * * * * * * * * * * * * * * * * * * */
		// TO ADD: getMainArt(), getDescription()
		// Then change to the directory called backend and run 'php global_include.php' to add the changes.
		// end global_include: overrides.php
	}
?>
