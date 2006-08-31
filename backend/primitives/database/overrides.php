		/* * * * * * * * * * * * * * * * * * *
		 *            Overrides              *
		 * * * * * * * * * * * * * * * * * * */
		
/**
 * Returns the date the node was added.
 * 
 * @author Ben Dodson <bdodson@seas.upenn.edu>
 * @version 5/14/2004
 * @since 5/14/2004
 */
function getDateAdded() {
	global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
	               		
    $path = jz_db_escape($this->getPath("String"));
    $results = jz_db_simple_query("SELECT date_added FROM jz_nodes WHERE path = '$path'");
    return $results['date_added'];
}

/**
 * Checks whether or not
 * this media has been updated within $days days.
 * If not, returns false. Otherwise, returns
 * the number of days since the most recent media was added.
 *
 * @author Ben Dodson
 * @version 4/9/05
 * @since 4/9/05
 **/
function newSince($days = false) {
  global $days_for_new;

  if ($days === false) {
    $days = $days_for_new;
  }

  $time = $curtime = time();
  $time -= ($days*24*60*60);

  if ($this->getLevel() == 0) {
    $pathstring = "%";
  } else {
    $pathstring = jz_db_escape($this->getPath("String"));
    $pathstring .= "/%";
  }
  $sql = "SELECT date_added FROM jz_nodes WHERE ";
  $sql .= "path LIKE '$pathstring' AND date_added >= $time ORDER BY date_added desc LIMIT 1";

  $results = jz_db_simple_query($sql);

  if (false === $results) {
    return false;
  } else {
    return ceil(abs($curtime - $results['date_added']) / (24*60*60));
  }
}


/**
 * Returns the element's ID
 *
 * @author Ben Dodson
 * @version 3/11/05
 * @since 3/11/05
 **/
function getID() {
  if (isset($this->myid) && $this->myid !== false) {
    return $this->myid;
  } else {
    $path = jz_db_escape($this->getPath("String"));
    $results = jz_db_simple_query("SELECT my_id FROM jz_nodes WHERE path = '$path'");
    return $results['my_id'];
  }
}

/**
 * Sets the elements ID.
 * Returns true on success, false on failure.
 *
 * @author Ben Dodson
 * @version 3/11/05
 * @since 3/11/05
 **/
function setID($id) {
  if (!$link = jz_db_connect())
    die ("could not connect to database.");
  
  $path = jz_db_escape($this->getPath("String"));
  $mid = jz_db_escape($id);
  $res = jz_db_query($link, "UPDATE jz_nodes SET my_id='${mid}' WHERE path = '$path'");
  if ($res === false) { // bad ID; could be a collision.
    jz_db_close($link);
    return false;
  }
  
  if ($this->isLeaf()) {
    $res = jz_db_query($link, "UPDATE jz_tracks SET my_id='${mid}' WHERE path = '$path'");
  }
  
  $this->myid = $id;
  jz_db_close($link);
  return true;
}

/**
 * Converts an id to a path
 *
 * @author Ben Dodson
 * @version 3/11/05
 * @since 3/11/05
 **/
function idToPath($id) {
    $results = jz_db_simple_query($link, "SELECT path FROM jz_nodes WHERE my_id = '$id'");
    return $results['path'];
}

		/**
		* Returns the number of times the node has been played.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 5/14/2004
		* @since 5/14/2004
		*/
		function getPlayCount() {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
			
			if (isset($this->playcount)) {
			  return $this->playcount;
			}
	
           	$path = jz_db_escape($this->getPath("String"));
           	$results = jz_db_simple_query("SELECT playcount FROM jz_nodes WHERE path = '$path'");
			$this->playcount = $results['playcount'];
            return $results['playcount'];
		}
		
		
		/**
		* Increments the node's playcount, as well
		* as the playcount of its parents.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 5/14/2004
		* @since 5/14/2004
		*/
		function increasePlayCount() {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
      		
            $path = jz_db_escape($this->getPath("String"));
			$sql = "UPDATE jz_nodes SET playcount = playcount+1, lastplayed = " . time();

            jz_db_simple_query("$sql  WHERE path = '$path'");
			                     	
            if (sizeof($ar = $this->getPath()) > 0) {
            	array_pop($ar);
                $next = &new jzMediaNode($ar);
				$next->increasePlayCount();
            }
		}
	
		/**
		* Sets the elements playcount.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 5/14/2004
		* @since 5/14/2004
		*/
		function setPlayCount($n) {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
			
			if (!(is_int($n) || is_numeric($n))) {
				return false;
			}
            $path = jz_db_escape($this->getPath("String"));
            jz_db_simple_query("UPDATE jz_nodes SET playcount = $n WHERE path = '$path'");
		}


	
       /**
		* Increments the node's view count
		*
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 3/15/2005
		* @since 3/15/2005
		*/
		function increaseViewCount() {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
			
			$path = jz_db_escape($this->getPath("String"));
            jz_db_simple_query("UPDATE jz_nodes SET viewcount = viewcount+1 WHERE path = '$path'");
		}


		/**
		* Returns the number of times the node has been viewed.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 3/15/2005
		* @since 3/15/2005
		*/
		function getViewCount() {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
			
            $path = jz_db_escape($this->getPath("String"));
            $results = jz_db__simple_query("SELECT viewcount FROM jz_nodes WHERE path = '$path'");
            return $results['viewcount'];
		}

       /**
		* Sets the elements viewcount
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 8/11/05
		* @since 8/11/05
		*/
		function setViewCount($n) {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;

			if (!(is_int($n) || is_numeric($n))) {
				return false;
			}
            $path = jz_db_escape($this->getPath("String"));
            jz_db_simple_query("UPDATE jz_nodes SET viewcount = $n WHERE path = '$path'");
		}

		/**
		* Returns the number of times the node has been downloaded.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 5/14/2004
		* @since 5/14/2004
		*/
		function getDownloadCount() {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
					       
			if (isset($this->dlcount)) {
			  return $this->dlcount;
			}
	
            $path = jz_db_escape($this->getPath("String"));
            $results = jz_db_simple_query("SELECT dlcount FROM jz_nodes WHERE path = '$path'");
			$this->dlcount = $results['dlcount'];
        	return $results['dlcount'];
		}
		
		
		/**
		* Increments the node's download count, as well
		* as the count of its parents.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 5/14/2004
		* @since 5/14/2004
		*/
		function increaseDownloadCount() {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
			 		
            $path = jz_db_escape($this->getPath("String"));
            jz_db_simple_query("UPDATE jz_nodes SET dlcount = dlcount+1 WHERE path = '$path'");
                     	
            if (sizeof($ar = $this->getPath()) > 0) {
            	array_pop($ar);
                $next = &new jzMediaNode($ar);
				$next->increasePlayCount();
            }
		}

		/**
		* Sets the elements download count.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 5/14/2004
		* @since 5/14/2004
		*/
		function setDownloadCount($n) {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
			
			if (!(is_int($n) || is_numeric($n))) {
				return false;
			}
            $path = jz_db_escape($this->getPath("String"));
            jz_db_simple_query("UPDATE jz_nodes SET dlcount = $n WHERE path = '$path'");
		}

		/**
		* Returns the main art for the node.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 5/14/04
		* @since 5/14/04
		*/
		function getMainArt($dimensions = false, $createBlank = true, $imageType="audio") {
		  global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db,$jzSERVICES;
		  
		  $path = jz_db_escape($this->getPath("String"));
		  $results = jz_db_simple_query("SELECT main_art FROM jz_nodes WHERE path = '$path'");
		  
		  if ($results['main_art']) {
		    // Now let's make create the resized art IF needed
		    $this->artpath = jz_db_unescape($results['main_art']);
		    return parent::getMainArt($dimensions,$createBlank, $imageType);
		  } else if ($this->isLeaf() === false) { 
		    // Now let's see if we can get art from the tags
		    $tracks = $this->getSubNodes("tracks");
		    if (count($tracks) > 0){
		      $meta = $jzSERVICES->getTagData($tracks[0]->getDataPath());
		      // Did we get it?
		      if ($meta['pic_name'] <> ""){
			if ($dimensions){
			  // Now lets check or create or image and return the resized one
			  return $jzSERVICES->resizeImage("ID3:". $tracks[0]->getDataPath(), $dimensions, $imageType);
			} else {
			  return "ID3:". $tracks[0]->getDataPath();
			}
		      }
		    }
		  }
		  // inheritance is sweet.
		  return parent::getMainArt($dimensions,$createBlank, $imageType);
		}
		
		/**
		* Sets the node's main art
		* 
		* @author Ben Dodson
		* @version 6/7/04
		* @since 6/7/04
		*/
		function addMainArt($image) {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
			
			$image = jz_db_escape($image);
            $path = jz_db_escape($this->getPath("String"));
            $results = jz_db_simple_query("UPDATE jz_nodes SET main_art = '$image' WHERE path = '$path'");
		}



		/**
		* Returns the miscellaneous artwork attached to the node.
		* 
		* @author 
		* @version 
		* @since 
		*/
		function getRandomArt() {}
		
		
		/**
		* Adds misc. artwork to the node.
		* 
		* @author 
		* @version 
		* @since 
		*/
		function addRandomArt($image) {}



		/**
		* Returns a brief description for the node.
		* 
		* @author Ben Dodson
		* @version 5/21/04
		* @since 5/21/04
		*/
		function getShortDescription() {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
					
            $path = jz_db_escape($this->getPath("String"));
            $results = jz_db_simple_query("SELECT descr FROM jz_nodes WHERE path = '$path'");
            if (isset($results['descr'])) {
	        	return jz_db_unescape($results['descr']);
	        } else { return false; }
		}
		
		
		/**
		* Adds a brief description.
		* 
		* @author Ben Dodson 
		* @version 5/21/04 
		* @since 5/21/04
		*/		
		function addShortDescription($text) {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
			
			$text = jz_db_escape($text); 		
            $path = jz_db_escape($this->getPath("String"));
            $results = jz_db_simple_query("UPDATE jz_nodes SET descr = '$text' WHERE path = '$path'");
		}


		/**
		* Returns the description of the node.
		* 
		* @author Ben Dodson
		* @version 5/21/04
		* @since 5/21/04
		*/
		function getDescription() {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
			
			if (isset($this->longdesc)) {
			  $desc = $this->longdesc;
				while (substr($desc,0,4) == "<br>" or substr($desc,0,6) == "<br />"){
					if (substr($desc,0,4) == "<br>"){
						$desc = substr($desc,5);
					}
					if (substr($desc,0,6) == "<br />"){
						$desc = substr($desc,7);
					}
				}
				return $desc;
			}
    		
			$path = jz_db_escape($this->getPath("String"));
			$results = jz_db_simple_query("SELECT longdesc FROM jz_nodes WHERE path = '$path'");
			jz_db_close($link);

			if ($results['longdesc']) {
				
				$desc = jz_db_unescape($results['longdesc']);
				while (substr($desc,0,4) == "<br>" or substr($desc,0,6) == "<br />"){
					if (substr($desc,0,4) == "<br>"){
						$desc = substr($desc,5);
					}
					if (substr($desc,0,6) == "<br />"){
						$desc = substr($desc,7);
					}
				}
				$this->longdesc = $desc;
				return $desc;
			} else { 
				$this->longdesc = false;
				return false; 
			}
		}
		
		/**
		* Adds a description.
		* 
		* @author Ben Dodson
		* @version 5/21/04
		* @since 5/21/04
		*/		
		function addDescription($text) {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
			
			$text = jz_db_escape($text);
            $path = jz_db_escape($this->getPath("String"));
            $results = jz_db_simple_query("UPDATE jz_nodes SET longdesc = '$text' WHERE path = '$path'");
		}


		/**
		* Gets the overall rating for the node.
		* 
		* @author Ben Dodson
		* @version 6/7/04
		* @since 6/7/04
		*/
		function getRating() {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
				
            $path = jz_db_escape($this->getPath("String"));
            $results = jz_db_simple_query("SELECT rating_val FROM jz_nodes WHERE path = '$path'");
            return $results['rating_val'];
		}
		
		
		/**
		* Add a rating for the node.
		* 
		* @author Ben Dodson
		* @version 6/7/04
		* @since 6/7/04
		*/		
		function addRating($rating, $weight = false) {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db, $rating_weight, $jzUSER;
			
			if ($weight === false) {
			  $weight = $jzUSER->getSetting('ratingweight');
			}

			if (!$link = jz_db_connect())
                     		die ("could not connect to database.");
                     	
			$addRating = $rating * $weight;
			$addWeight = $weight;

                     	$path = jz_db_escape($this->getPath("String"));

			$results = jz_db_query($link, "SELECT rating,rating_count FROM jz_nodes WHERE path = '$path'");
			if ($results->data[0]['rating_count'] == 0) {
			  $rval = $rating;
			} else {
			  $rval = estimateRating(($results->data[0]['rating'] + $addRating) / ($results->data[0]['rating_count'] + $addWeight));
			}

			$results = jz_db_query($link, "UPDATE jz_nodes SET rating=rating+$addRating,
                                                     rating_count=rating_count+$addWeight, rating_val=$rval WHERE path = '$path'");
		
			if ($rating_weight > 0 && $this->getLevel() > 0) {
				$path = $this->getPath();
				array_pop($path);
				$next = &new jzMediaNode($path);
				$next->addRating($rating, $weight * $rating_weight);
			}
			jz_db_close($link);
		}
		
		/**
		* Gets the number of people who have rated this element
		* 
		* @author Ben Dodson
		* @version 6/11/04
		* @since 6/11/04
		*/
		function getRatingCount() {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
			
            $path = jz_db_escape($this->getPath("String"));
            $results = jz_db_simple_query("SELECT rating_count FROM jz_nodes WHERE path = '$path'");
            return $results['rating_count'];
		}


		/**
		* Returns the node's discussion
		* 
		* @author Ben Dodson
		* @version 6/7/04
		* @since 6/7/04
		*/
		function getDiscussion() {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
			
			if (!$link = jz_db_connect())
                     		die ("could not connect to database.");
                     		
                     	$path = jz_db_escape($this->getPath("String"));
                     	
                     	$results = jz_db_query($link, "SELECT * FROM jz_discussions WHERE path LIKE '$path' ORDER BY my_id");
                     	
                     	$discussion = array();
                     	$i = 0;
                     	foreach ($results->data as $key => $data) {
                     		$discussion[$i]['user'] = jz_db_unescape($data['user']);
                     		$discussion[$i]['comment'] = jz_db_unescape($data['comment']);
				$discussion[$i]['id'] = $data['my_id'];
				$discussion[$i]['date'] = $data['date_added'];
                     		
                     		$i++;
                     	}
                     	jz_db_close($link);
                     	return ($discussion == array()) ? false : $discussion;
		}


		/**
		* Adds a blurb to the node's discussion
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 8/11/05
		* @since 5/15/04
		*/				
		function addDiscussion($text,$username) {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
			
			if (!$link = jz_db_connect())
                     		die ("could not connect to database.");
                     		
                     	$path = jz_db_escape($this->getPath("String"));
                     	$text = jz_db_escape($text);
                     	$username = jz_db_escape($username);
                     	
			$res = jz_db_query($link,"SELECT * FROM jz_discussions");
			$num = $res->rows + 1;
                     	if (false === jz_db_query($link, "INSERT INTO jz_discussions(my_id,path,my_user,comment,date_added)
                     	                  VALUES($num,'$path','$username','$text',".time().")")) die(jz_db_error($link));
			jz_db_close($link);
		}


		
        /**
		 * Adds a full discussion,
		 * given from $element->getDiscussion();
		 *
		 * @author Ben Dodson
		 * @version 8/11/05
		 * @since 8/11/05
		 **/
         function addFullDiscussion($disc) {
		   global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
		   
		   $path = jz_db_escape($this->getPath("String"));
		   foreach ($disc as $entry) {
		     $user = jz_db_escape($entry['user']);
		     $comment = jz_db_escape($entry['comment']);
		     $id = $entry['id'];
		     $date = $entry['date'];
		     
		     jz_db_simple_query("INSERT INTO jz_discussions(my_id,path,user,comment,date_added)
                     	                  VALUES($id,'$path','$user','$comment',$date)") || die(jz_db_error($link));
		   }	     
		 }
		



		/**
		* Returns the year of the element;
		* if it is a leaf, returns the info from getMeta[year]
		* else, returns the first matching year it finds.
		* Entry is '-' for no year.
		* 
		* @author Ben Dodson
		* @version 5/21/04
		* @since 5/21/04
		*/		
		function getYear() {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
			
			if (isset($this->year)) {
			  return $this->year;
			}

            $path = jz_db_escape($this->getPath("String"));
            if ($this->isLeaf()) {
            	$results = jz_db_simple_query("SELECT year FROM jz_tracks WHERE path = '$path'");
				$this->year = $results['year'];
	            return $results['year'];
            } else { 
	        	$results = jz_db_simple_query( "SELECT year FROM jz_tracks WHERE path LIKE '${path}/%' AND year != '-' ORDER BY path LIMIT 1");
	            if (false !== $results) {
					$this->year = $results['year'];
					return $results['year'];
	       		} else { 
					$this->year = "-";
					return "-"; 
				}
           }
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
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db, $allow_filesystem_modify;
									
			if ($allow_filesystem_modify) {
				$path = jz_db_escape($this->getPath("String"));
                $results = jz_db_simple_query("SELECT filepath FROM jz_nodes WHERE path = '$path'");
                return jz_db_unescape($results['filepath']);
			}
			else {
				return $this->data_dir;
			}
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
		function getFilePath() {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db, $allow_filesystem_modify;
										
            $path = jz_db_escape($this->getPath("String"));
            $results = jz_db_simple_query("SELECT filepath FROM jz_nodes WHERE path = '$path'");
           	return jz_db_unescape($results['filepath']);
		}
		
		
		function setFilePath($mypath) {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db, $allow_filesystem_modify;
	
            $path = jz_db_escape($this->getPath("String"));
            $mypath = jz_db_escape($mypath);
            $results = jz_db_simple_query("UPDATE jz_nodes SET filepath = '$mypath' WHERE path = '$path'");
		}
		
		
		/**
		* Marks this element as hidden.
		* 
		* @author Ben Dodson
		* @version 9/18/04
		* @since 9/18/04
		*/
		function hide() {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;
	
            $path = jz_db_escape($this->getPath("String"));
            jz_db_simple_query("UPDATE nodes SET hidden='true' WHERE path = '$path'");
                     	
            if ($this->isLeaf()) {
            	jz_db_simple_query("UPDATE jz_tracks SET hidden='true' WHERE path = '$path'");
            }	
		}


		/**
		* Unhides the element
		* 
		* @author Ben Dodson
		* @version 9/18/04
		* @since 9/18/04
		*/
		function unhide() {
			global $sql_type,$sql_pw,$sql_usr,$sql_socket,$sql_db;

            $path = jz_db_escape($this->getPath("String"));
            jz_db_simple_query("UPDATE jz_nodes SET hidden='false' WHERE path = '$path'");
                     	
            if ($this->isLeaf()) {
            	jz_db_simple_query("UPDATE jz_tracks SET hidden='false' WHERE path = '$path'");
            }
		}
