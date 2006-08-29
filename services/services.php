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
 * - These are auxilary functions for the services.
 *
 * @since 01.14.05
 * @author Ross Carlson <ross@jinzora.org>
 * @author Ben Dodson <ben@jinzora.org>
 */

/*
 * Gets all available CMS's as an array of:
 * cms_type => CMS Display Name.
 * The order should be sensible- please leave Standalone at the top.
 * 
 * @author Ben Dodson
 * @since 6/26/06
 */
 function getAllCMS() {
 	$cms = array();
 	
 	$cms['standalone'] = 'Standalone';
 	$cms['cpgnuke']    = 'CPGNuke';
 	$cms['e107']      = 'e107';
 	$cms['geeklog']    = 'Geeklog';
 	$cms['mambo']      = 'Joomla-Mambo';
 	$cms['mdpro']      = 'MDPro';
 	$cms['phpnuke']    = 'PHPNuke';
 	$cms['postnuke']   = 'POSTNuke';
 	$cms['xoops']      = 'XooPS';

 	return $cms;
 }

/* Seperates matches from nonmatches.
 * The array returned is of the form:
 * $array['matches']
 * $array['nonmatches']
 * Where $array['matches'] is an array of jzMediaNodes
 * and $array['nonmatches'] is an array of strings.
 *
 * @author Ben Dodson
 * @since 1/14/05
 * @version 1/16/05
 **/
function seperateSimilar($array) {
  if (!is_array($array) || sizeof($array) == 0) {
    $ret = array();
    $ret['nonmatches'] = array();
    $ret['matches'] = array();

    return $ret;
  }
  // We don't want more artists in the array than we came with.
  // The searching is what actually needs to be improved (with operator = "exact-or")

  $ret = array();
  $root = new jzMediaNode();
  $found = $root->search($array,"nodes",distanceTo('artist'),sizeof($array),"or");
  $ret['matches'] = $found;
  // Now let's remove the matches:
  $matches = array();
  foreach ($found as $e) {
    $matches[] = $e->getName();
  }
  $nonmatches = array();

  foreach ($array as $entry) {
    $foundit = false;
    foreach ($matches as $match) {
      if (0 == strcmp(strtolower($match),strtolower($entry))) {
	// Found it
	$foundit = true;
	break;
      }
    }
    if (!$foundit) {
      $nonmatches[] = $entry;  
    }
  }
  $ret['nonmatches'] = $nonmatches;

  return $ret;
}

/**
	* Writes out the meta data of an album
	* 
	* @author Ross Carlson
	* @version 08/10/04
	* @param $node The node we are looking at
	* @param $year The year of the album
	* @param $image The URL of the image for the album
	* @param $tracks The list of tracks for the album (an array)
	* @param $review The review or discription of the album
	* @param $rating The rating of the album in number of stars (1-5)
	* @param $price The price of the album
	* @param $genre The genre of the album
	* @param $displayOutput Should we display output while writing?
	*/
	function writeAlbumMetaData($node, $year=false, $image=false, $tracks=false, $review=false, $rating=false, $price=false, $genre=false, $displayOutput=false, $write_now = false){
		global $web_root, $root_dir, $media_dir, $audio_types, $allow_id3_modify, $allow_filesystem_modify, $include_path, $backend;
		

		// Ok, now let's write out the description
		if ($review){
			if ($displayOutput){ 
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					ars.innerHTML = 'Status: Writing Review';					
					-->
				</SCRIPT>
				<?php
				flushdisplay();
			}
			// Now let's write this data to the node
			$node->addDescription($review);
			// Now let's write it to a file if we should
			if ($allow_filesystem_modify == "true" and !stristr($backend,"id3")){
				$bioFile = $node->getFilePath(). "/album-desc.txt";
				$handle = @fopen($bioFile, "w");
				@fwrite($handle,$review);				
				@fclose($handle);			
			}
		} 
		
		// Now let's write out the image
		if (stristr($image,".jpg")){
			include_once($include_path. "lib/snoopy.class.php");
			$snoopy = new Snoopy;
			$snoopy->fetch($image);
			$imageData = $snoopy->results;
			unset($snoopy);
			
			// Now let's make sure that was valid
			if (strlen($imageData) < 1000){
				$imageData = ""; 
			}
		} else {
			$imageData = "";
		}
		
		// Now let's write it out
		if ($imageData <> ""){
			if ($displayOutput){ 
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					ars.innerHTML = 'Status: Writing Album Image';					
					-->
				</SCRIPT>
				<?php
				flushdisplay();
			}
			
			// Ok, now can we write to the filesystem?
			if ($allow_filesystem_modify == "false" or stristr($backend,"id3")){
				$imgFile = $include_path. "data/images/". str_replace("/","--",$node->getPath("String")). "--". $node->getName(). ".jpg";
			} else {
				$imgFile = $node->getFilePath(). "/". $node->getName(). ".jpg";
			}

			// Now let's write it out
			if (writeImage($imgFile, $imageData)){
				$node->addMainArt($imgFile);
			}
			
			if ($displayOutput){ 
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					ars.innerHTML = 'Status: Writing Album Image - Success';					
					-->
				</SCRIPT>
				<?php
				flushdisplay();
			}
			$retVal=1;
		}
		
		// Now let's write the rating
		if ($rating <> "" and is_numeric($rating)){
			$node->addRating($rating);
			if ($displayOutput){ 
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					ars.innerHTML = 'Status: Rating Album';					
					-->
				</SCRIPT>
				<?php
				flushdisplay();
			}
		}
		
		if ($displayOutput){ 
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				ars.innerHTML = 'Status: Writing data to files...';					
				-->
			</SCRIPT>
			<?php
			flushdisplay();
		}		
		
		// Did they want to write this to the id3 tags?
		if ($allow_id3_modify == "true" and $write_now == true){
			// Now let's set the meta fields so they get updated for all the tracks
			$meta['albumYear'] = $year;
			$meta['image-data'] = $imageData;
			$meta['image-file'] = $imgFile;
			$meta['image-ext'] = ".jpg";
			$meta['image-name'] = $imgShortName;
			$node->bulkMetaUpdate($meta,false,$displayOutput);
			
			if ($displayOutput){ 
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					ars.innerHTML = 'Status: Complete!';					
					-->
				</SCRIPT>
				<?php
				flushdisplay();
			}
		}
	}
	
	/**
	* Writes out the meta data of an artist
	* 
	* @author Ross Carlson
	* @version 08/10/04
	* @param string $link the link of where the data is
	*/
	function writeArtistMetaData($node, $image=false, $bio=false, $displayOutput){
		global $web_root, $root_dir, $media_dir, $allow_filesystem_modify, $allow_id3_modify, $include_path, $backend, $include_path;

		// Let's write the bio
		if ($bio){
			if ($displayOutput){ 
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					ars.innerHTML = 'Status: Writing Description';					
					-->
				</SCRIPT>
				<?php
				flushdisplay();
				usleep(250000);
			}
			// Now let's write this data to the node
			$node->addDescription($bio);
			// Now let's write it to a file if we should
			if ($allow_filesystem_modify == "true" and !stristr($backend,"id3")){
				$bioFile = $node->getFilePath(). "/". $node->getName(). ".txt";
				$handle = @fopen($bioFile, "w");
				@fwrite($handle,$bio);				
				@fclose($handle);			
			}
		}
		// Now let's write out the image
		$imgFile="";
		if (stristr($image,".jpg")){
			include_once($include_path. "lib/snoopy.class.php");
			$snoopy = new Snoopy;
			$snoopy->fetch($image);
			$imageData = $snoopy->results;
			unset($snoopy);

			// Now let's make sure that was valid
			if (strlen($imageData) < 2000){
				//$imageData = ""; 
			}
		} else {
			$imageData = "";
		}
		//echo strlen($imageData);

		// Now let's write it out
		if ($imageData <> ""){
			if ($displayOutput){ 
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					ars.innerHTML = 'Status: Writing Image';					
					-->
				</SCRIPT>
				<?php
				flushdisplay();
				usleep(250000);
			}
			
			// Ok, now can we write to the filesystem?
			if ($allow_filesystem_modify == "false" or stristr($backend,"id3")){
				$imgFile = $include_path. "data/images/". str_replace("/","--",$node->getPath("String")). "--". $node->getName(). ".jpg";
			} else {
				$imgFile = $node->getFilePath(). "/". $node->getName(). ".jpg";
			}

			// Now let's write it out
			if (writeImage($imgFile, $imageData)){
				
				$node->addMainArt($imgFile);
			}

			if ($displayOutput){ 
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					ars.innerHTML = 'Status: Writing Image - Success';					
					-->
				</SCRIPT>
				<?php
				flushdisplay();
				usleep(250000);
			}
			$retVal=1;
		} 
		return true;
	}

?>