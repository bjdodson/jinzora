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
	$jzSERVICE_INFO['name'] = "music.msn.com";
	$jzSERVICE_INFO['url'] = "http://music.msn.com";

	define('SERVICE_METADATA_yahoo','true');

	/*
	 * Gets the metadata for an album
	 * 
	 * @author Ross Carlson
	 * @version 1/15/05
	 * @since 1/15/05
	 * @param $node The current node we are looking at
	 * @param $displayOutput Should we dispaly output (defaults to true)
	 **/	
	function SERVICE_GETALBUMMETADATA_yahoo($node, $displayOutput = true, $return = false) {
		global $include_path;
		
		// Ok, now we need to see if we are reading a album or an artist
	 	$album = $node->getName(); 
		$parent = $node->getParent();
		$artist = $parent->getName();
				
		include_once($include_path. "lib/snoopy.class.php");
		
		// First let's get the artist page
		$snoopy = new Snoopy;
		$snoopy->fetch("http://search.music.yahoo.com/search/?m=album&x=15&y=7&p=". urlencode($album));
		$contents = $snoopy->results;
		unset($snoopy);
		
		// Did we get anything?
		if (stristr($contents,"no matches found")){
			return false;
		}
		
		// Now let's fix up the name
		$albumName = str_replace("&","&amp;",$album);
		$artist = str_replace("&","&amp;",$artist);
		
		// Ok, now let's see if we got a direct hit or a link
		if (!stristr($contents,$artist) or !stristr($contents,$album)){
			// Ok, we missed let's try to mangle the name and try again?
			$album = trim(substr($album,0,strpos($album," -")));
			$snoopy = new Snoopy;
			$snoopy->fetch("http://search.music.yahoo.com/search/?m=album&x=15&y=7&p=". urlencode($album));
			$contents = $snoopy->results;
			unset($snoopy);
		}
		if (!stristr($contents,$artist) or !stristr($contents,$album)){
			// Ok, we missed let's try to mangle the name and try again?
			$album = trim(substr($album,0,strpos($album,"[")));
			$snoopy = new Snoopy;
			$snoopy->fetch("http://search.music.yahoo.com/search/?m=album&x=15&y=7&p=". urlencode($album));
			$contents = $snoopy->results;
			unset($snoopy);
		}
		if (!stristr($contents,$artist) or !stristr($contents,$album)){
			// Ok, we missed let's try to mangle the name and try again?
			$album = trim(substr($album,0,strpos($album,"(")));
			$snoopy = new Snoopy;
			$snoopy->fetch("http://search.music.yahoo.com/search/?m=album&x=15&y=7&p=". urlencode($album));
			$contents = $snoopy->results;
			unset($snoopy);
		}
		if (!stristr($contents,$artist) or !stristr($contents,$album)){
			return false;
		}

		// Now let's move up to the artist and back
		$link = substr($contents,strpos($contents,$artist)-300);
		$link = substr($link,strpos($link,"<a href=http://music.yahoo.com/release")+8);
		$link = substr($link,0,strpos($link,' '));
		if (stristr($link,">")){
			$link = substr($link,0,strpos($link,'>'));
		}
		
		// Now let's get that page
		$contents = @file_get_contents($link);
		
		// Now let's get the image
		$contents = substr($contents,strpos($contents,'onClick="ext_link'));
		$contents = substr($contents,strpos($contents,'Album Release Date')-400);
		$image = substr($contents,strpos($contents,'<img src="http://')+10);
		$image = substr($image,0,strpos($image,'"'));
		
		if (!stristr($image,".jpg") or !stristr($image,"http://")){
			$image = "";
		}
		
		// Now let's get the release year
		$year = substr($contents,strpos($contents,'Album Release Date:&nbsp;')+strlen('Album Release Date:&nbsp;'));
		$year = substr($year,0,strpos($year,'</td>'));
		
		if (!$return){
			writeAlbumMetaData($node, $year, $image, false, false, false, false, false, $displayOutput);
		} else {
			if ($return == "array"){
				$retArr['year'] = $year;
				$retArr['image'] = $image;
				$retArr['review'] = false;
				$retArr['rating'] = false;
				
				return $retArr;
			} else {
				return $$return;
			}
		}
	}

	/*
	 * Gets the metadata for an artist
	 * 
	 * @author Ross Carlson
	 * @version 1/15/05
	 * @since 1/15/05
	 * @param $node The current node we are looking at
	 **/	
	function SERVICE_GETARTISTMETADATA_yahoo($node, $displayOutput, $return = false){
		global $include_path;
		
		// let's set the artist we're looking at
		$artist = $node->getName();
		
		include_once($include_path. "lib/snoopy.class.php");

		$snoopy = new Snoopy;
		$snoopy->fetch("http://search.music.yahoo.com/search/?m=artist&x=23&y=10&p=". urlencode($artist));
		$contents = $snoopy->results;
		unset($snoopy);
		
		// Did we get anything?
		if (stristr($contents,"no matches found")){
			return false;
		}
		
		// Ok, now let's see if we got a direct hit or a link
		if (stristr($contents,$artist)){			
			// Now let's see if we can get the right link
			$contents = substr($contents,strpos($contents,'<a href=http://music.yahoo.com/') + 8);
			$link = trim(substr($contents,0,strpos($contents,">")));
			$link = str_replace("---","-bio--",$link);
			
			// Now let's get the bio back
			$contents = @file_get_contents($link);
			$bio = substr($contents,strpos($contents,'width="401">'));
			$bio = substr($bio,strpos($bio,'<td>')+4);
			$bio = substr($bio,0,strpos($bio,'</td>'));
			
			// Now let's get the artist image
			$image = substr($contents,strpos($contents,'<img src="http://')+10);
			$image = substr($image,0,strpos($image,'"'));
			if (!stristr($image,".jpg") or !stristr($image,"http://")){
				$image = "";
			}
			
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
