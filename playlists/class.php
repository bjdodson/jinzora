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
	* @since 10/29/04
	* @author Ben Dodson
	*/
	class jzPlaylist {
			
		var $list; // list of jzMediaNodes and jzMediaTracks.
		var $name; // name of playlist
		var $isPublic; // if it is public, we cannot erase it until it is made private.
		var $id;
		
		
		/**
		 * Constructor for a playlist.
		 *
		 * @author Ben Dodson
		 * @param type the type of this playlist: static|dynamic
		 **/
		function jzPlaylist($mylist = array(), $myname = false, $type = "static") {
		  if (!is_array($mylist)) {
		    $l = array();
		    $l[] = $mylist;
		    $mylist = $l;
		  }
			$this->list = $mylist;
			if ($type == "dynamic") {
			  $this->id = uniqid("DY");
			} else {
			  $this->id = uniqid("PL");
			}

			if ($myname === false) {
				$myname = "Untitled";
			}
			else {
				$this->name = $myname;
			}
		}

		/**
		 * Gets a playable link for this playlist.
		 * @author Ben Dodson
		 * @since 1/3/08
		 **/
		function getPlayHREF($random=false,$limit=0) {
		  global $jzUSER;
		  // do they have permissions or should we just do text?
		  // return null otherwise 
		  
		  $arr = array();
		  $arr['type'] = 'playlist';
		  $arr['jz_pl_id'] = $this->getID();
		  $arr['action'] = "playlist";
		  if ($limit != 0) { $arr['limit'] = $limit; }
		  if ($random){ $arr['mode'] = "random"; }
		  if ($clips){ $arr['clips'] = "true"; }
		  
		  if (isset($_GET['frame'])){
		    $arr['frame'] = $_GET['frame'];
		  }

		  return urlize($arr);
		}
		
		/**
		* Gets the playlist's name.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 10/29/04
		* @since 10/29/04
		*/
		function getName() {
			return $this->name;
		}
		
		
		/**
		* Returns the type of the variable (jzPlaylist)
		* 
		* @author Ben Dodson
		* @version 10/31/04
		* @since 10/31/04
		*/
		function getType() {
			return "jzPlaylist";
		}
		
		/** 
		 * Returns the type of this playlist.
		 * Currently one of: static|dynamic
		 *
		 * @author Ben Dodson
		 * @since 4/23/05
		 * @version 4/23/05
		 **/
		function getPlType() {
		  return getListType($this->id);
		}

		/**
		* Renames the playlist.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 10/29/04
		* @since 10/29/04
		*/		
		function rename($newname) {
			$this->name = $newname;
		}
		
		/**
		* Gets the playlist's ID.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 10/29/04
		* @since 10/29/04
		*/
		function getID() {
			return $this->id;
		}
		/**
		* Gets the playlist's length
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 10/30/04
		* @since 10/30/04
		*/
		function length() {
			return sizeof($this->list);
		}

		/**
		 * Returns the track count for this playlist.
		 */
		function getTrackCount() {
		  $count = 0;
		  for ($i = 0; $i < sizeof($this->list); $i++) {
		    $el = $this->list[$i];
	
		    if ($el->getType() == "jzMediaTrack") {
		      $count++;
		    }
		    else if ($el->getType() == "jzMediaNode") {
		      $count += $el->getSubNodeCount("tracks",-1);
		    }
		    else if ($el->getType() == "jzPlaylist") {
		      $count += $el->getTrackCount();
		    }
		    else {
		      die("Unknown type for an element in the playlist.");
		    }
		  }
		  return $count;
		}

		/** 
		 * 'Intelligently' creates a random playlist from the given list.
		 * 
		 * @author Ben Dodson
		 * @param The number of elements to return
		 * @param type the type of the playlist.
		 *
		 * false - equally weights tracks
		 * radio - creates a radio from the given artist playlist should only contain 1 artist)
		 */
		function getSmartPlaylist($count = 0, $type = "equi-track") {
		  global $max_playlist_length, $jzSERVICES;

		  if ($type == "radio") {
		    if ($count == 0) {
		      $lim = false;
		    } else {
		      $lim = $count;
		    }

		    $el = $this->getAt(0);
		    $mainArray = $el->getSubNodes("tracks",-1,true,$lim);
		    
		    // Now let's get the top 5 similar artists
		    $simArray = $jzSERVICES->getSimilar($el);
		    $simArray = seperateSimilar($simArray);
		    $i=0;$limit=6;
		    // Now let's shuffle
		    $similarArray = array();
		    for ($e=0;$e<count($simArray['matches']);$e++){
		      if (isset($simArray['matches'][$e])){		
			// Ok, this is one that we want, let's get it's path
			$simArt = $simArray['matches'][$e];
			if ($lim) {
				$subArray = $simArt->getSubNodes("tracks",-1,true,ceil($lim/4));
			} else {
				$subArray = $simArt->getSubNodes("tracks",-1,true);
			}
			$similarArray = array_merge($similarArray,$subArray);
			$i++;
			if ($limit){if ($i>$limit){ break; }}
		      }
		    }
		    $finArray = array_merge($similarArray,$mainArray);
		    $pl = &new jzPlaylist($finArray);
		    $pl->shuffle();
		    $pl->truncate($lim);
		    return $pl;
		  }
		  //*****************//
		  // default 'smartPlaylist'
		  $trackcount = $this->getTrackCount();

		  if ($count <= 0) {
		    $count = $trackcount;
		  }
		 
		  if ($trackcount <= $count) {
		    $pl = $this;
		    $pl->flatten();
		    $pl->shuffle();
		    $pl->truncate($max_playlist_length);
		    return $pl;
		  }

		  $pl = new jzPlaylist();
		  $numbers = range(0,$trackcount-1);
		  srand((float)microtime() * 1000000);
		  $numbers = array_rand($numbers,$count);
		  
		  $size_list = array();
		  $list = $this->getList();


		  if ($list[0]->getType() == "jzMediaTrack") {
		    $size_list[0] = 1;
		    } else if ($list[0]->getType() == "jzMediaNode") {
		      $size_list[0] = $list[0]->getSubNodeCount("tracks",-1);
		    } else {
		      $size_list[0] = $list[0]->getTrackCount();
		    }

		  for ($i = 1; $i < sizeof($list); $i++) {
		    if ($list[$i]->getType() == "jzMediaTrack") {
		      $size_list[$i] = 1 + $size_list[$i-1];
		    } else if ($list[$i]->getType() == "jzMediaNode") {
		      $size_list[$i] = $size_list[$i-1] + $list[$i]->getSubNodeCount("tracks",-1);
		    } else {
		      $size_list[$i] = $size_list[$i-1] + $list[$i]->getTrackCount();
		    }
		  }
		  $element_count = array();
		  for ($i = 0; $i < sizeof($list); $i++) {
		    $element_count[$i] = 0;
		  }
		  $j = 0;
		  for ($i = 0; $i < sizeof($numbers); $i++) {
		    if ($numbers[$i] < $size_list[$j]) {
		      $element_count[$j]++;
		    } else {
		      $j++;
		      $i--;
		    }
		  }

		  $final = array();

		  for ($i = 0; $i < sizeof($list); $i++) {
		    if ($element_count[$i] > 0) {
		      switch ($list[$i]->getType()) {
		      case "jzMediaNode":
			$final = array_merge($final,$list[$i]->getSubNodes("tracks",-1,true,$element_count[$i]));
			break;
		      case "jzMediaTrack":
			$final[] = $list[$i];
			break;
		      default:
			$more = $list[$i];
			$more->flatten();
			$more->shuffle();
			$more->truncate($element_count[$i]);
			$final = array_merge($final,$more);
			break;
		      }
		    }
		  }

		  $pl->add($final);
		  $pl->shuffle();
		  return $pl;
		  // for ($i = 0; $i < $count; $i++) {
		    // $pl->add($this->getAt($numbers[$i],"track"));
		    // Too slow...
		  //}
		}


		/**
		 * Preprocess the playlist
		 *
		 * @author Ben Dodson
		 * @version 4/17/05
		 * @since 4/17/05
		 *
		 **/
		function preProcess($intro = true) {
		  global $include_path,$site_title,$root_dir,$audio_types,$video_types;

		  if ($intro) {
		    if ($handle = opendir($include_path.'playlists/intros')) {
		      $files = array();
		      while (false !== ($file = readdir($handle))) {
						if ($file == "." || $file == "..") {
							continue;
						}
						if (preg_match("/\.($audio_types)$/i", $file) || preg_match("/\.($video_types)$/i", $file)) {
							$files[] = $file;
						}
		      }
		      if (sizeof($files) > 0) {
						srand((float) microtime() * 10000000);
						$key = rand(0,sizeof($files)-1);
						$track = &new jzMediaTrack('intro');
						$track->meta = array();
						$track->meta['length'] = 0;
						$track->meta['artist'] = $site_title;
						$track->meta['title'] = "Intro";
						$link = "http://" . $_SERVER['SERVER_NAME'];
						if ($_SERVER['SERVER_PORT'] != 80) {
							$link .= ":" . $_SERVER['SERVER_PORT'];
						}
						$link .= $root_dir . "/playlists/intros/" . rawurlencode($files[$key]);
						$track->playpath = $link;
						$track->artpath = "-";
						$this->add($track,0);
		      }
		    }
		  }
		  //if ($flatten) {
		  //  $this->flatten();
		  //}
		}

		/**
		* returns a playable playlist.
		* $purpose is one of: stream|jukebox|general
		* $intro means if we find intros in the playlists/intros folder, use one at random.
		* Stream uses their local path, if possible. Otherwise, uses the virtual path.
		* Jukebox uses the physical path
		* General uses the virtual path always.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 10/30/04
		* @since 10/30/04
		*/
		function createPlaylist($purpose = false, $intro = true, $m_playlist_type = false) {
			global $use_ext_playlists, $jukebox, $audio_types, $video_types, $playlist_type,$root_dir, 
				   $include_path, $allow_resample,$jzUSER,$jzSERVICES,$embedded_player,$site_title;

			if ($m_playlist_type == false || $m_playlist_type == "") {
				$m_playlist_type = $playlist_type;
				if ($m_playlist_type === false || $m_playlist_type == "") {
				  $m_playlist_type = $jzUSER->getSetting('playlist_type');
				}
			}
			
			if ($purpose !== false) {
				if ($jukebox != "false") {
					$m_playlist_type = "jukebox";
				}
			}

			$list = $this;
			$list->preProcess($intro);

			// Now, does this user use an embedded player?
			if (checkPlayback() == "embedded"){
			  // Ok, they want an embedded player - lets load it up
			  if ($embedded_player <> ""){
			    $player = $embedded_player;
			  }
			  if ($jzUSER->getSetting('player') <> ""){
			    $player = $jzUSER->getSetting('player');
			  }
			  $jzSERVICES->loadService("players",$player);
			  $jzSERVICES->openPlayer($list);
			  exit();
			}
			
			// Now let's look at the list and IF it's a single track let's possibly change the list type
			if (isset($_GET['type']) && $_GET['type'] == "track"){
				// Ok, now we need to know the file extension of what's being played
				$fArr = explode("/",$_GET['jz_path']);
				$file = $fArr[count($fArr)-1];
				$eArr = explode(".",$file);
				$ext = $eArr[count($eArr)-1];
				switch($ext){
					case "rm":
						$m_playlist_type = "ram";
						$jzSERVICES->loadService("playlist",$m_playlist_type);
					break;
				}
			}

			// Ok, now we need to load up the playlist service that's appropriate to this playlist type
			return $jzSERVICES->createPlaylist($list,$m_playlist_type);
		}
		
		/**
		* Gets the element at the given position.
		* The position is either an integer or an ordered list of indices:
		* 1:4:1
		*
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @param pos the position to get.
		* @param method the method for searching. If it is 'track', look for the track in position pos
		* when the list is thought of as being flat.
		* Otherwise, index the list as described above.
		*
		* @version 10/30/04
		* @since 10/30/04
		*/
		function getAt($pos, $method = "list") {
		  if ($method == "track") {
		    $i = 0; // index for the track we want.
		    $j = 0; // index for the list
		    $listy = $this->list;
		    while ($i < $pos) {
		      if ($listy[$j]->getType() == "jzMediaTrack") {
			$j++;
			$i++;
		      }
		      else if ($listy[$j]->getType() == "jzMediaNode") {
			if (($a = $listy[$j]->getSubNodeCount('tracks',-1)) + $i < $pos) {
			  $i += $a;
			  $j++;
			} else {
			  // It's in this list.
			  $list = $listy[$j]->getSubNodes('tracks',-1);
			  return $list[$pos - $i];
			}
		      }
		      else { // a playlist
			if (($a = $listy[$j]->getTrackCount()) + $i < $pos) {
			  $i += $a;
			  $j++;
			} else {
			  $list = $this;
			  $list->flatten();
			  return $list->getAt($pos - $i);
			}
		      }
		    }
		    return $listy[$i];
		  }
		  else {
		    $p = explode(":",$pos);
		    if (sizeof($p) == 1) {
		      if ($p[0] >= $this->length())
			return false;
		      return $this->list[$p[0]];
		      
		    }
		    else {
		      $cur = array_shift($p);
		      $pos = implode(":",$p);
		      
		      $list = &new jzPlaylist();
		      $list->add($this->getAt($cur));
		      $list->flatten(0,1);
		      return $list->getAt($pos);
		    }
		  }
		}
		  
		/**
		* Gets the entire list of elements.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 10/30/04
		* @since 10/30/04
		*/
		function getList() {
		  return $this->list;
		}
		
		/**
		* Moves an element in the playlist.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 10/30/04
		* @since 10/30/04
		*/
		function move($pos,$new_pos) {
			if ($pos > $this->length())
				return;
				
			if ($new_pos > $this->length()-1)
				$new_pos = $this->length()-1;
			
			if ($pos > $new_pos) {
				// backwards
				$el = $this->list[$pos];
				for ($i = $pos; $i > $new_pos; $i--)
					$this->list[$i] = $this->list[$i-1];
					
				$this->list[$new_pos] = $el;
			}
			else if ($pos < $new_pos) {
				// forwards
				$el = $this->list[$pos];
				for ($i = $pos; $i < $new_pos; $i++)
					$this->list[$i] = $this->list[$i+1];
					
				$this->list[$new_pos] = $el;
			
			}
		}
		
		/**
		* Flattens a node. If no node is specified,
		* flatten them all.
		* If $degree > 0, flatten it that much.
		* Else, flatten to jzMediaTracks.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 10/30/04
		* @since 10/30/04
		*/
		function flatten($pos = false, $degree = -1) {
			global $max_playlist_length;
			
			$new_list = array();
			$newsize = 0;
			if ($pos === false) {
				for ($i = 0; $i < $this->length(); $i++) {
					$cur = $this->list[$i];
					if ($cur->getType() == "jzMediaTrack") {
						$new_list[] = $cur;
						$newsize++;
					} else if ($cur->getType() == "jzPlaylist") {
						$more = $cur->flatten();
						foreach ($more as $mo) {
							$new_list[] = $mo;
							$newsize++;
							if ($max_playlist_length > 0 && $newsize >= $max_playlist_length) {
								$this->list = $new_list;
								$this->truncate();
								return;
							}							
						}
					} else { // jzMediaNode
						if ($degree > 0) {
							$more = $cur->getSubNodes("both",$degree);
							foreach ($more as $mo) {
								$new_list[] = $mo;
								$newsize++;
								if ($max_playlist_length > 0 && $newsize >= $max_playlist_length) {
									$this->list = $new_list;
									$this->truncate();
									return;
								}
							}
							
						}
						else {
							$more = $cur->getSubNodes("tracks",-1);
							foreach ($more as $mo) {
								$new_list[] = $mo;
								$newsize++;
								if ($max_playlist_length > 0 && $newsize >= $max_playlist_length) {
									$this->list = $new_list;
									$this->truncate();
									return;
								}
							
							}
						}
					}
				}
				$this->list = $new_list;
				$this->truncate();
			}
			else {
				for ($i = 0; $i < $pos; $i++) {
					$new_list[] = $this->list[$i];
				}
				
				$cur = $this->list[$pos];
				if ($cur->getType() == "jzMediaNode") {
					if ($degree > 0) {
						$more = $cur->getSubNodes("both",$degree);
						foreach ($more as $mo) {
							$new_list[] = $mo;
							$newsize++;
							if ($max_playlist_length > 0 && $newsize >= $max_playlist_length) {
								$this->list = $new_list;
								$this->truncate();
								return;
							}
						}
					}
					else {
						$more = $cur->getSubNodes("tracks",-1);
						foreach ($more as $mo) {
							$new_list[] = $mo;
							$newsize++;
							if ($max_playlist_length > 0 && $newsize >= $max_playlist_length) {
								$this->list = $new_list;
								$this->truncate();
								return;
							}
						
						}
					}
				}
				else if ($cur->getType() == "jzPlaylist") {
					$more = $cur->flatten();
					foreach ($more as $mo) {
						$new_list[] = $mo;
						$newsize++;
						if ($max_playlist_length > 0 && $newsize >= $max_playlist_length) {
							$this->list = $new_list;
							$this->truncate();
							return;
						}	
					}
				}
				else {
					$new_list[] = $cur;
				}
				
				for ($i = $pos+1; $i < $this->length(); $i++) {
					$new_list[] = $this->list[$i];
				}
				
				$this->list = $new_list;
				$this->truncate();
			}
		}
		
		
		/**
		* Shuffles the playlist.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 10/29/04
		* @since 10/29/04
		*/
		function shuffle() {
			srand((float)microtime() * 1000000);
			shuffle($this->list);
		}
		
		
		/**
		* Adds an element to the list. If a position is not given, add it to the end.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 10/30/04
		* @since 10/30/04
		*/
		function add($element,$pos=false) {
		  if ($this->getPlType() == "dynamic") {
		  	$this->addRule(0,'exact',$element->isLeaf() ? 'track' : 'node',$element->getPath("string"));
		    return;
		  }
			if ($pos === false) {
				if (is_array($element))
					$this->list = $this->list + $element;
				else
					$this->list[] = $element;
			}
			else {
				$newlist = array();
				for ($i = 0; $i < $pos; $i++) {
					$newlist[] = $this->list[$i];
				}
				
				if (is_array($element))
					$newlist = $newlist + $element;
				else
					$newlist[] = $element;
					
				for ($i = $pos; $i < sizeof($this->list); $i++)
					$newlist[] = $this->list[$i];
				
				$this->list = $newlist;
			}
			
			$this->truncate();
		}

		/**
		 * Gets the global limit of the playlist
		 *
		 * @author Ben Dodson
		 * @since 4/24/05
		 *
		 **/
		function getLimit() {
		  return (isset($this->limit)) ? $this->limit : 0;
		}
		
		/**
		 * Gets the global limit of the playlist
		 *
		 * @author Ben Dodson
		 * @since 4/24/05
		 *
		 **/
		function setLimit($n) {
		  $this->limit = $n;
		}

		/* 
		 * Gets the list of rules for a dynamic playlist.
		 *
		 * @author Ben Dodson
		 * @since 4/23/05
		 *
		 **/
		function getRules() {
		  if ($this->getPlType() != "dynamic") {
		    return false;
		  }
		  if (!is_array($this->rulelist)) {
		    return array();
		  }
		  return $this->rulelist;
		}

		/**
		 * Adds a rule to a dynamic playlist
		 *
		 * @author Ben Dodson
		 * @since 4/23/05
		 *
		 **/
		function addRule($amount, $function, $type, $source) {
		  if ($this->getPlType() != "dynamic") {
		    return false;
		  }
		  if (!is_array($this->rulelist)) {
		    $this->rulelist = array();
		  }
		  
		  $rule = array();
		  $rule['amount'] = $amount;
		  $rule['function'] = $function;
		  $rule['type'] = $type;
		  $rule['source'] = $source;

		  $this->rulelist[] = $rule;
		}

		function removeRule($i) {
		  $list = array();
		  $e = 0;
		  foreach ($this->getRules() as $rule) {
		    if ($e != $i) {
		      $list[] = $rule;
		    }
		    $e++;
		  }
		  $this->rulelist = $list;
		}

		/*
		 * Generates a playlist and stores it in $this->list
		 * from the playlist's rules.
		 *
		 * @author Ben Dodson
		 * @since 4/23/05
		 **/
		function handleRules() {
		  global $jzSERVICES;

		  if ($this->getPlType() != "dynamic") {
		    return false;
		  }

		  $temp = array();
		  $this->list = array();

		  foreach ($this->rulelist as $rule) {
		    $source = new jzMediaNode($rule['source']);
		    $count = $rule['amount'];
		    if ($rule['type'] == "track" || $rule['type'] == "tracks") {
		      $type = "tracks";
		      $distance = -1;
		    } else {
		      $type = "nodes";
		      $distance = distanceTo("album",$source);
		    }

		    switch ($rule['function']) {
		    case "exact":
		    	if ($type == "tracks") {
		    		$source = new jzMediaTrack($rule['source']);
		    	}
		    	$temp = array($source);
		    	break;
		    case "random":
		      $temp = $source->getSubNodes($type,$distance,true,$count);
		      break;
		    case "topplayed":
		      $temp = $source->getMostPlayed($type,$distance,$count);
		      break;
		    case "recentlyadded":
		      $temp = $source->getRecentlyAdded($type,$distance,$count);
		      break;
		    case "similar":
		      if ($source->getPType() == "artist") {
			$mainArray = $source->getSubNodes($type,$distance,true,$count);
			// Now let's get the top 5 similar artists
			$simArray = $jzSERVICES->getSimilar($source);
			$simArray = seperateSimilar($simArray);
			$i=0;$limit=8;
			// Now let's shuffle
			$similarArray = array();
			for ($e=0;$e<count($simArray['matches']);$e++){
			  if (isset($simArray['matches'][$e])){		
			    // Ok, this is one that we want, let's get it's path
			    $simArt = $simArray['matches'][$e];
			    $subArray = $simArt->getSubNodes($type,$distance,true,($count/1.5));
			    $similarArray = array_merge($similarArray,$subArray);
			    $i++;
			    if ($limit){if ($i>$limit){ break; }}
			  }
			}
			$finArray = array_merge($similarArray,$mainArray);
			shuffle($finArray);
			$temp = array();
			for ($i = 0; $i < $count; $i++) {
			  $temp[] = $finArray[$i];
			}
		      } else {
			$temp = $source->getSubNodes($type,$distance,true,$count);
		      }
		      break;
		    }

		    foreach ($temp as $l) {
		      $this->list[] = $l;
		    }
		  }
		  shuffle($this->list);
		  if (isset($this->limit) && $this->limit > 0) {
		    $this->truncate($this->limit);
		  }
		}

		/**
		* Removes an element from the list.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 10/30/04
		* @since 10/30/04
		*/
		function remove($pos) {
			for ($i = $pos; $i < $this->length()-1; $i++) {
				$this->list[$i] = $this->list[$i+1];
			}
			unset($this->list[$this->length()-1]);
		}
		
		/**
		* Filters in (audio|video) files.
		* Note that this will also flatten the playlist.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 10/31/04
		* @since 10/31/04
		*/
		function filter($type = "audio") {
			global $audio_types,$video_types;
			$this->flatten();

			for ($i = 0; $i < $this->length(); $i++) {
				$track = $this->getAt($i);
				if ($type == "audio") {
					if (!preg_match("/\.($audio_types)$/i", $track->getPath("String"))) {
						$this->remove($i);
						$i--; // so we dont skip an element.
					}
				}
				else if ($type == "video") {
					if (!preg_match("/\.($video_types)$/i", $track->getPath("String"))) {
						$this->remove($i);
						$i--; // so we dont skip an element.
					}
				}
			}
		}
	

		/**
		 * Adds the elements from a form to the playlist.
		 * The list is called jz_list.
		 * If the list does not contain tracks,
		 * The type of the list must be set as jz_list_type.
		 * The list is assumed to be a POST variable jz_list.
		 *
		 * @author Ben Dodson
		 * @version 1/12/05
		 * @since 1/12/05
		 *
		 */
		function addFromForm() {
		  if (isset($_POST['jz_list_type']) && $_POST['jz_list_type'] == "nodes") {
		    foreach ($_POST['jz_list'] as $file) {
		      $new = &new jzMediaNode($file);
		      $this->add($new);
		    } 
		  } else if (isset($_POST['jz_list_type']) && $_POST['jz_list_type'] == "playlists") {
		    foreach ($_POST['jz_list'] as $file) {
		      //$new = &new jzPlaylist($file);
		      //$this->add($new);
		      // not functional yet.
		    }
		  } else {
		    foreach ($_POST['jz_list'] as $file) {
		      $new = &new jzMediaTrack($file);
		      $this->add($new);
		    }
		    
		  }
		}
		   
		/**
		 * Plays media from a remote playlist
		 *
		 * @param url the URL of the playlist
		 * @author Ben Dodson
		 * @since 2/4/09
		 **/
		 function addFromExternal($url) {
			// requires stream support in file handlers
			$list = file($_REQUEST['external_playlist']);
	
			for ($i=0; $i<sizeof($list);$i++) {
				if ($list[$i][0] != '#') {
					$name = null;
					if ($i > 0 && false !== strstr($list[$i-1],'#EXTINF')) {
						$name = substr($list[$i-1],1+strpos($list[$i-1],','));
					}
					$track = new JzMediaTrack();
					$track->name=$name;
					$track->playpath = $list[$i];
					$this->add($track);
				}
			}

			fclose($stream);
		 }
		
   
		/**
		* Truncates a list to the given size
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 11/2/04
		* @since 11/1/04
		*/
		function truncate($size = false) {
			global $max_playlist_length;

			if ($size === false) {
				if (!($max_playlist_length > 0))
					return;
				$size = $max_playlist_length;
			}
			if ($size == 0) {
				$this->list = array();
				return;
			}
			if ($size < 0 || $this->length() <= $size)
				return;
			
			for ($i = $this->length()-1; $i >= $size; $i--) {
				unset($this->list[$i]);
			}
			
		}
		
		/**
		* Creates a random playlist
		* 
		* @author Ben Dodson
		* @version 10/31/04
		* @since 10/31/04
		*/
		function generate($random_play_type, $random_play_number, $random_play_genre = false,$resample = false) {
			global $web_root, $root_dir, $media_dir, $audio_types, $this_site, $directory_level;
			
			$this->truncate(0);
			
			// Let's initalize some variables 
			$final_ctr = 0;
			
			if ($random_play_genre !== false && $random_play_genre != ""){
				$node = &new jzMediaNode($random_play_genre);
			} else {
				$node = &new jzMediaNode();
			}
					
			// Ok, now let's see what kind of random list they wanted 
			switch (strtolower($random_play_type)) {
				case "songs" : # Ok, they wanted some random songs
				       $tracks = $node->getSubNodes("tracks",-1,true,$random_play_number*6);
					$finalArray = array();
					$c=0;
					foreach ($tracks as $track){
						// Now let's make sure it's an audio type
						if (preg_match("/\.($audio_types)$/i", $track->getPath("String"))) {
							// Ok, it is let's add it to our final array
							$finalArray[] = $track;
							$c++;
						}
						// Now let's see if we should stop
						if ($c >= $random_play_number){ break; }
					}
					// Ok, now let's send this to the playlist generator
					$this->add($finalArray);
					$this->filter("audio");
					return $this;
				break;
				
				case "albums" : # Ok, they wanted some random Albums
					$albums = $node->getSubNodes("nodes",distanceTo("album",$node),true,$random_play_number);
					$this->add($albums);
					$this->filter("audio");
					
					return $this;
				break;
				
				case "artists" : # Ok, they wanted some random Artists
					$artists = $node->getSubNodes("nodes",distanceTo("artist",$node),true,$random_play_number);
					
					$this->add($artists);
					$this->filter("audio");
					
					return $this;
					
					
				break;
				
				case "genres" : # Ok, they wanted some random Genres
					$node = &new jzMediaNode();
					$genres = $node->getSubNodes("nodes",distanceTo("genre",$node),true,$random_play_number);
					
					$this->add($genres);
					$this->filter("audio");
					
					return $this;
				
				break;
				
			}

		}
		
		
		/* ACTIONS FOR A PLAYLIST: */
		
		/**
		* Streams the playlist to the user.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 11/12/04
		* @since 11/12/04
		*/
		function stream($fileExt = false, $redirect = false) {
			global $web_root, $root_dir, $media_dir, $audio_mimes, $playlist_ext,$jzUSER, $this_site,$jzSERVICES;

			if ($fileExt === false || $fileExt == "") {
			    $fileExt = $jzUSER->getSetting('playlist_type');
				if ($fileExt === false || $fileExt == "") {
					$fileExt = "m3u";
				}
			}
			
			// Now let's look at the list and IF it's a single track let's possibly change the list type
			if (isset($_GET['type']) && $_GET['type'] == "track"){
				// Ok, now we need to know the file extension of what's being played
				$fArr = explode("/",$_GET['jz_path']);
				$file = $fArr[count($fArr)-1];
				$eArr = explode(".",$file);
				$ext = $eArr[count($eArr)-1];
				switch($ext){
					case "ra":
					case "rm":
						$jzSERVICES->loadService("playlist","ram");
						$con_type = $jzSERVICES->getPLMimeType("ram");
						$fileExt = "ram";
					break;
					case "mov":
						$jzSERVICES->loadService("playlist","qt");
						$con_type = $jzSERVICES->getPLMimeType("qt");
						$fileExt = "qt";
					break;
					default:
						$jzSERVICES->loadService("playlist",$fileExt);
						$con_type = $jzSERVICES->getPLMimeType();
					break;
				}
			} else {
				$jzSERVICES->loadService("playlist",$fileExt);
				$con_type = $jzSERVICES->getPLMimeType();
			}

			// Now let's set the proper header IF we don't need to redirect
			if (!$redirect){
			  //$playlist = $this->createPlaylist(false, true, $fileExt);
			  if (isset($_SERVER['HTTP_USER_AGENT']) && false !== stristr($_SERVER['HTTP_USER_AGENT'],'Windows CE')) {
			      $disposition = 'attachment';
			  } else {
			    $disposition = 'inline';
			  }
			  if (checkPlayback() != "embedded") {
			    header("Accept-Range: bytes");
			    header("Content-Type: ". $con_type);
			    header('Content-Disposition: '.$disposition.'; filename="playlist.'.$fileExt.'"');
			    header("Cache-control: private"); //IE seems to need this.
			  }
			    $this->createPlaylist(false, true, $fileExt);
				//echo $playlist;
			} else {
				// Now we need to create a playlist from this so we can redirect to it
				$fileName = $web_root. $root_dir. "/temp/search-result.pls.m3u";
				$handle = fopen($fileName, "w");
				fwrite($handle,$this->createPlaylist(false, true, $fileExt));	
				fclose ($handle);
				
				// Now let's send them to that page
				echo '<meta http-equiv="refresh" content="0; url='. $this_site. $root_dir. '/playlists.php?searchpl=true">';
			}
		}
		
		/**
		* Sends the playlist to the jukebox
		* 
		* @author Ross Carlson
		* @version 2/10/05
		* @since 2/10/05
		*/
		function jukebox($playlist = false) {
			global $include_path, $web_root, $root_dir, $media_dir, $audio_mimes, $playlist_ext;
			
			if ($playlist === false) {
			  $playlist = $this;
			}
			// Now let's create the jukebox object
			include_once($include_path. "jukebox/class.php");
			$jb = new jzJukebox();
			
			$jb->passPlaylist($playlist);
		}
		
		
		/**
		* Does the default action on this playlist (play|jukebox)
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 11/12/04
		* @since 11/12/04
		*/
		function play() {
		  global $jukebox,$jzUSER,$jz_path;
		  
		  if (checkPermission($jzUSER,'play',$jz_path) === false) {
		    return false;
		  }

		  $l = $this;
		  if ($l->getPlType() == "dynamic") {
		    $l->handleRules();
		  }
		  
		  $l->flatten();

		  if (sizeof($l->list) == 0) {
		    return;
		  }
		  if (checkPlayback() == 'jukebox') {
		    $l->jukebox();
		  } else {
		    $l->stream();
		  }
		}

		/* Downloads the playlist
		 * 
		 * @author Ben Dodson
		 * @since 2/24/05
		 * @version 2/24/05
		 *
		 **/		
		function download() {
			global $include_path;
			
		  include_once($include_path. 'lib/jzcomp.lib.php');
		  include_once($include_path. 'lib/general.lib.php');
		  $pl = $this;
		  
		  if ($pl->getPlType() == "dynamic") {
		    $pl->handleRules();
		  }
		  $list = $pl->getList();

		  if (sizeof($list) == 0) {
		    return;
		  }

		  // Can we download it?
		  if (!checkStreamLimit($list)) {
		    echo word('Sorry, you have reached your download limit.');
		    exit();
		  }

		  foreach ($list as $el) {
		    $el->increaseDownloadCount();
		  }

		  $pl->flatten();
		  $list = $pl->getList();		  
		  $i = 0;
		  $files = array();
		  $m3u = "";$oldPath="";$onepath=true;
		  foreach ($list as $track) {
		    $files[$i] = $track->getFileName("host");
			// Let's also create the m3u playlist for all this
			$tArr = explode("/",$files[$i]);
			$m3u .= "./". $tArr[count($tArr)-1]. "\n";
		    $i++;
			// Now let's get the path and make sure we only see 1 unique path
			// If we see only one path we'll add art IF we can
			$pArr = $track->getPath();
			unset($pArr[count($pArr)-1]);
			$path = implode("/",$pArr);
			if ($path <> $oldPath and $oldPath <> ""){
				$onepath = false;
			} else {
				$oldPath = $path;
			}
		  }
		  
		  $name = $this->getName();
		  if ($name === false || $name == "") {
		    $name = "Playlist";
		  }
		  
		  // Now should we add art?
		  if ($onepath){
		  	// Ok, let's create the node so we can get the art
			$artNode = new jzMediaNode($oldPath);
			if ($artNode->getMainArt() <> ""){
				$i++;
				$files[$i] = $artNode->getMainArt();
			}
		  }
		 
		  // Silly to send a 1 element playlist
		  if (sizeof($files) > 1) {
		  	// Now let's write that to the temp dir
			$fileName = $include_path. "temp/playlist.m3u";
			$handle = @fopen($fileName, "w");
			@fwrite($handle,$m3u);				
			@fclose($handle);	
			$files[$i+1] = $fileName;		
		  }
			// Now let's send it
			sendFileBundle($files, $name);
		}

		function makePublic($comment) {} // add/remove this list from the public list of playlists.
		
		function removePublic() {} // removes it from the public list.
	
	}
	
	
?>
