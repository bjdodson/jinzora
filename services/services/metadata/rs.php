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
	 * - This is the leo's lyrics service.
	 *
	 * @since 01.14.05
	 * @author Ross Carlson <ross@jinzora.org>
	 * @author Ben Dodson <ben@jinzora.org>
	 */
	
	$jzSERVICE_INFO = array();
	$jzSERVICE_INFO['name'] = "Rollingstone.com";
	$jzSERVICE_INFO['url'] = "http://www.rollingstone.com";

    define('SERVICE_METADATA_rs','true');

	/*
	 * Gets the metadata for an album
	 * 
	 * @author Ross Carlson
	 * @version 1/15/05
	 * @since 1/15/05
	 * @param $node The current node we are looking at
	 * @param $displayOutput Should we dispaly output (defaults to true)
	 **/	
	function SERVICE_GETALBUMMETADATA_rs($node, $displayOutput = true, $return = false) {
		global $include_path;
		
		// Ok, now we need to see if we are reading a album or an artist
	 	$album = $node->getName(); 
		$parent = $node->getAncestor("artist");
		$artist = $parent->getName();
				
		include_once($include_path. "lib/snoopy.class.php");
		
		$snoopy = new Snoopy;
		$snoopy->fetch("http://www.rollingstone.com/?searchtype=RSAlbum&query=". urlencode($album));
		$contents = $snoopy->results;
		unset($snoopy);
							
		$retVal=0;
		// Now let's fix up the name
		$albumName = str_replace("&","&amp;",$album);
		$artist = str_replace("&","&amp;",$artist);

		// Ok, let's see if we got the exact album or a page of possibles
		$link = false;
		if (strpos($contents,$artist)){
			// Ok, we found the album (or we think) now let's move to it
			$contents = substr($contents,strpos($contents,'"<strong>')+10);
			$contents = substr($contents,strpos($contents,'<tr>')+4);
			// Now let's build an array so we can find the right link
			$linkArray = explode("</tr>",$contents);
			for ($i=0; $i < count($linkArray); $i++){
				// Now let's see if this one has our artist
				if (stristr($linkArray[$i],$artist)){
					// Ok, we've got our block, let's get the link
					$link = substr($linkArray[$i],strpos($linkArray[$i],'href="')+6);
					$link = substr($link,0,strpos($link,'"'));
					break;
				}
			}
		}

		// Ok, did we find a link?
		if ($link){
			$snoopy = new Snoopy;
			$snoopy->fetch($link. "?rnd=1107178952184&has-player=true&version=6.0.12.1040");
			$contents = $snoopy->results;
			unset($snoopy);
			
			// Alright, now let's parse this out
			if (strlen($contents) > 0 && strlen($artist) > 0 && stristr($contents, $artist) and stristr($contents,$album)){
				// First let's get the album art
				$image = substr($contents,strpos($contents,"http://image.listen.com"));
				$image = substr($image,0,strpos($image,'"'));
				
				// Now let's get the year
				$year = substr($contents,strpos($contents,"Originally released:")+strlen("Originally released:"));
				$year = trim(substr($year,0,strpos($year,"<")));
				
				// Now let's get the tracks
				$tracks = substr($contents,strpos($contents,"Track List"));
				$tracks = substr($tracks,0,strpos($tracks,"Download</a> this album"));
				$tArray = explode("</table>",$tracks);
				$e=0;
				for ($i=0; $i < count($tArray); $i++){
					// Let's get the track number
					$tNum = substr($tArray[$i],strpos($tArray[$i],"<td"));
					$tNum = substr($tNum,strpos($tNum,'">')+2);
					$tNum = substr($tNum,0,strpos($tNum,'</'));
					// Let's get the track name
					$tName = substr($tArray[$i],strpos($tArray[$i],'alt="Audio')+10);
					$tName = substr($tName,strpos($tName,'<a')+2);
					$tName = substr($tName,strpos($tName,'">')+2);
					$tName = substr($tName,0,strpos($tName,'</'));
					
					$trackArray[$e]['number'] = $tNum;
					$trackArray[$e]['name'] = $tName;
					$e++;
				}
				
				// Now let's get the rating
				$rating=false;
				if (!stristr($contents,"http://i.rollingstone.com/rs/images/ratings/notrated_left_small.gif")){
					$rating = substr($contents,strpos($contents,"http://i.rollingstone.com/rs/images/ratings")+strlen("http://i.rollingstone.com/rs/images/ratings")+1);
					$rating = substr($rating,0,strpos($rating,'"'));
					$rating = (substr($rating,0,1) / 2);
				}
				
				// Now let's get the review
				$review = substr($contents,strpos($contents,'<div id="storyContainer">')+strlen('<div id="storyContainer">'));
				$review = substr($review,strpos($review,'<p>')+3);
				$review = trim(substr($review,0,strpos($review,' (RS')));
				$review = str_replace("</p>","",$review);
				$review = str_replace("<p>","<br><br>",$review);
				if ($review == ""){$review=false;}
				
				// Now that we have all the data we should write it back out
				if (!$return){
					writeAlbumMetaData($node, $year, $image, $trackArray, $review, $rating, false, false, $displayOutput);
					return true;
				} else {
					if ($return == "array"){
						$retArr['year'] = $year;
						$retArr['image'] = $image;
						$retArr['review'] = $review;
						$retArr['rating'] = $rating;
						
						return $retArr;
					} else {
						return $$return;
					}
				}
			}
		}
		if ($displayOutput) {
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			als.innerHTML = 'Status: Album not found';					
			-->
		</SCRIPT>
		<?php
		flushdisplay();
		}
		return false;
	}
	
	/*
	 * Gets the metadata for an artist
	 * 
	 * @author Ross Carlson
	 * @version 1/15/05
	 * @since 1/15/05
	 * @param $node The current node we are looking at
	 * @param $return should we return or write data (defaults to write), 
	 *                and if return what do we return (image = binaryImageData, genre, description)
	 **/	
	function SERVICE_GETARTISTMETADATA_rs($node = false, $return = false, $artistName = false){
		global $include_path;
		
		// Let's up the max execution time...
		ini_set('max_execution_time','600');
		
		// let's set the artist we're looking at
		if ($node){
			$artist = $node->getName();
		} else {
			$artist = $artistName;
		}
		
		include_once($include_path. "lib/snoopy.class.php");
		
		// Now let's open it up
		$snoopy = new Snoopy;
		$snoopy->fetch("http://www.rollingstone.com/?searchtype=RSArtist&query=". urlencode($artist));
		$contents = $snoopy->results;
		unset($snoopy);
		
		// Now let's fix up the name
		$albumName = str_replace("&","&amp;",$album);
		$artist = str_replace("&","&amp;",$artist);

		// Ok, let's see if we got the exact album or a page of possibles
		$link = false;
		if (strpos($contents,$artist)){
			// Ok, we found the album (or we think) now let's move to it
			$contents = substr($contents,strpos($contents,'"<strong>')+10);
			$contents = substr($contents,strpos($contents,'<tr>')+4);
			// Now let's build an array so we can find the right link
			$linkArray = explode("</tr>",$contents);
			for ($i=0; $i < count($linkArray); $i++){
				// Now let's see if this one has our artist
				if (stristr($linkArray[$i],$artist)){
					// Ok, we've got our block, let's get the link
					$link = substr($linkArray[$i],strpos($linkArray[$i],'href="')+6);
					$link = substr($link,0,strpos($link,'"'));
					break;
				}
			}
		}

		// Ok, did we find a link?
		if ($link){
			$snoopy = new Snoopy;
			$snoopy->fetch($link. "?rnd=1107178952184&has-player=true&version=6.0.12.1040");
			$contents = $snoopy->results;
			unset($snoopy);
			
			// First let's get the artist image
			$image = substr($contents,strpos($contents,'class="artistName">')+20);
			$image = substr($image,strpos($image,'src="')+5);
			$image = substr($image,0,strpos($image,'"'));
			
			// Now let's get the bio from that link
			$bioLink = substr($contents,strpos($contents,'/artist/bio/'));
			$bioLink = "http://www.rollingstone.com". substr($bioLink,0,strpos($bioLink,'"'));
			$snoopy = new Snoopy;
			$snoopy->fetch($bioLink);
			$contents = $snoopy->results;
			unset($snoopy);
			
			// Now let's get the bio
			$bio = substr($contents,strpos($contents,'<div class="bio">')+strlen('<div class="bio">'));
			$bio = substr($bio,0,strpos($bio,"</"));
			
			// Now let's find the similar artists
			$similar = substr($contents,strpos($contents,'contemporaries.gif')+30);
			$similar = substr($similar,strpos($similar,'</td>')+5);
			$simArray = explode("</tr>",$similar);
			for ($i=0; $i < count($simArray); $i++){
				if (stristr($simArray[$i],"title=") and !stristr($simArray[$i],"<img class")){
					$sim = substr($simArray[$i],strpos($simArray[$i],'<a')+2);
					$sim = substr($sim,strpos($sim,'">')+2);
					$sim = substr($sim,0,strpos($sim,'</'));
				}
			}
			
			// Now let's get the genre
			$genre = substr($contents, strpos($contents,"Genre:")+7);
			$genre = substr($genre,strpos($genre,'">')+2);
			$genre = substr($genre,0,strpos($genre,','));
			
			// Now let's write the data
			if ($return){
				if ($return == "array"){
					$retArr['bio'] = $bio;
					$retArr['image'] = $image;		
					$retArr['genre'] = $genre;		
					return $retArr;
				} else {
					return $$return;
				}
			} else {
				$artReturn = writeArtistMetaData($image, $bio, $genre, $node, $return);
			}
		}
		return false;
	}
?>