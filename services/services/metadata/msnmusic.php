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
	 * - replaced MSN-code: X-Coder
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
	$jzSERVICE_INFO['name'] = "music.msn.com";
	$jzSERVICE_INFO['url'] = "http://music.msn.com";

	define('SERVICE_METADATA_msnmusic','true');

	/*
	 * Gets the metadata for an album
	 * 
	 * @author Ross Carlson
	 * @version 1/15/05
	 * @since 1/15/05
	 * @param $node The current node we are looking at
	 * @param $displayOutput Should we dispaly output (defaults to true)
	 **/	
	function SERVICE_GETALBUMMETADATA_msnmusic($node, $displayOutput = true, $return = false) {
		global $include_path;
		
		// Ok, now we need to see if we are reading a album or an artist
	 	$album = $node->getName(); 
		$parent = $node->getParent();
		$artist = $parent->getName();
				
		include_once($include_path. "lib/snoopy.class.php");
		
		// First let's get the artist page
		$snoopy = new Snoopy;
		$snoopy->fetch("http://music.msn.com/search/all/?ss=". urlencode($artist));
		$contents = $snoopy->results;
		unset($snoopy);
		
		$retVal=0;
		// Now let's fix up the name
		$albumName = str_replace("&","&amp;",$album);
		$artist = str_replace("&","&amp;",$artist);

		// Ok, now let's see if we got a direct hit or a link
		if (stristr($contents,$artist)){
			// Now let's see if we can get the right link
			$contents = substr($contents,strpos($contents,$artist. "</a>")-50);
			$link = substr($contents,strpos($contents,"href")+6);
			$link = substr($link,0,strpos($link,'"'));
			$idartist = substr($link,strpos($link,'=')+1);
			// Now let's go to the artist page to get the albums
			$snoopy = new Snoopy;
			$snoopy->fetch("http://music.msn.com/xml/getartistcontent.aspx?id_type=artist&id=". $idartist. "&contenttype=artistalbums&category=content");
			$contents = $snoopy->results;
			unset($snoopy);
			
			// Now let's find the album
			if (stristr($contents,'album name="'.$album)){
			
				// Now let's get the year
				$year = substr($contents,strpos($contents,"reldate=")+9);
				$year = substr($year,0,strpos($year,'" id='));
				
				
				//Album-ID
				$link = substr($contents,strpos($contents,'album name="'.$album)+12);
				$link = substr($link,strpos($link,'id="')+4);
				$idalbum = substr($link,0,strpos($link,'" pop'));
				
				// Now let's get that
				$snoopy = new Snoopy;
				$snoopy->fetch("http://music.msn.com/album/?album=". $idalbum);
				$contents = $snoopy->results;
				unset($snoopy);
				
				// Now let's get the album image				
				$contents = substr($contents,strpos($contents,"http://images.windowsmedia.com"));
				$image = substr($contents,0,strpos($contents,'"'));
				if (!stristr($image,".jpg") or !stristr($image,"http://")){
					$image = "";
				}
				
				// Now let's get the rating
				$rating = substr($contents,strpos($contents,"AVG=")+4);
				$rating = str_replace("_",".",substr($rating,0,strpos($rating,">")));				

				
				// Now let's get the review
				$snoopy = new Snoopy;
				$snoopy->fetch("http://music.msn.com/xml/getalbumcontent.aspx?id_type=album&id=". $idalbum. "&contenttype=review&category=content");
				$contents = $snoopy->results;
				unset($snoopy);
				
				$contents = "\n\n\n". substr($contents,strpos($contents,'<reviewtext>')+strlen('<reviewtext>'));
				$review = substr($contents,0,strpos($contents,"</reviewtext>"));
				
				// Now let's fix the links
				$review = str_replace('href=/artist/?','target="_blank" href=http://music.msn.com/artist/?',$review);
				$review =html_entity_decode($contents);
				// Now that we have all the data we should write it back out
				
				$albumart = $image;
				if (!$return){
					writeAlbumMetaData($node, $year, $image, false, $review, $rating, false, false, $displayOutput);
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

		return false;		
	}

	/*
	 * Gets the metadata for an artist
	 * 
	 * @author Ross Carlson
	 * @version 1/15/05
	 * @since 1/15/05
	 * @param $node The current node we are looking at
	 **/	
	function SERVICE_GETARTISTMETADATA_msnmusic($node, $displayOutput, $return = false){
		global $include_path;
		
		// let's set the artist we're looking at
		$artist = $node->getName();
		
		include_once($include_path. "lib/snoopy.class.php");
		$snoopy = new Snoopy;
		$snoopy->fetch("http://music.msn.com/search/all/?ss=". urlencode($artist));
		$contents = $snoopy->results;
		unset($snoopy);
		
		// Ok, now let's see if we got a direct hit or a link
		if (stristr($contents,$artist)){			
		
			// Did we get the wrong page?
			if (stristr($contents, "Did you mean:")){
				$link = substr($contents,strpos($contents,"Did you mean:"));
				$link = substr($link,strpos($link,'href="')+6);
				$link = substr($link,0,strpos($link,'"'));
				$artist = substr($contents,strpos($contents,"Did you mean:"));
				$aritst = substr($artist,strpos($artist,'">')+2);
				$aritst = substr($aritst,0,strpos($aritst,'</a>'));
				
				// Now let's get that page back
				$snoopy = new Snoopy;
				$snoopy->fetch("http://music.msn.com". $link);
				$contents = $snoopy->results;
				unset($snoopy);
			}

			// Now let's see if we can get the right link
			$contents = substr($contents,strpos(strtolower($contents),strtolower($artist). "</a>")-50);
			$link = substr($contents,strpos($contents,"href")+6);
			$link = substr($link,0,strpos($link,'"'));
			if ($link == ""){
				return false;
			}
			
			// Now let's get that page back
			$snoopy = new Snoopy;
			$snoopy->fetch("http://music.msn.com". $link);
			$contents = $snoopy->results;
			unset($snoopy);

			
			// Now let's find the artist image
			$contents = substr($contents,strpos($contents,".jpg")-100);
			$image = substr($contents,strpos($contents,'src="')+5);
			$image = substr($image,0,strpos($image,'"'));	
			if (!stristr($image,".jpg") or !stristr($image,"http://")){
				$image = "";
			}

			
			// Now what content?
			$idartist = substr($link,strpos($link,'=')+1);
			$snoopy = new Snoopy;			
			$snoopy->fetch("http://music.msn.com/xml/getartistcontent.aspx?id_type=artist&id=". $idartist. '&contenttype=bio&category=content');
			$contents = $snoopy->results;
			unset($snoopy);
			
			//$contents = substr($contents,strpos($contents,'<td class="p10">')+strlen('<td class="p10">'));
			$bio = substr($contents,strpos($contents,'<bio>')+5);
			
			
			$bio = substr($bio,0,strpos($bio,'</bio>'));
			
			$bio = str_replace('href=/artist/?','target="_blank" href=http://music.msn.com/artist/?',$bio);
			$bio = html_entity_decode(str_replace('href=/album/?','target="_blank" href=http://music.msn.com/album/?',$bio));
	
			
			// Now let's write the data
			if ($return){
				if ($return == "array"){
					$retArr['bio'] = $bio;
					$retArr['image'] = $image;					
					return $retArr;
				} else {
					return $$return;
				}
				return $$return;
			} else {
				$artReturn = writeArtistMetaData($node, $image, $bio, $displayOutput);
			}
		}
		return false;	
	}
?>
