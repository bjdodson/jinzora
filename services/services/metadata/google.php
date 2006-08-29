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
	$jzSERVICE_INFO['name'] = "google";
	$jzSERVICE_INFO['url'] = "http://www.google.com";

	define('SERVICE_METADATA_google','true');

	/*
	 * Gets the metadata for an album
	 * 
	 * @author Ross Carlson
	 * @version 1/15/05
	 * @since 1/15/05
	 * @param $node The current node we are looking at
	 * @param $displayOutput Should we dispaly output (defaults to true)
	 **/	
	function SERVICE_GETALBUMMETADATA_google($node, $displayOutput = true, $return = false) {
		global $include_path;

		// Ok, now we need to see if we are reading a album or an artist
	 	$album = $node->getName(); 
		$parent = $node->getParent();
		$artist = $parent->getName();
				
		include_once($include_path. "lib/snoopy.class.php");
		$snoopy = new Snoopy;
		$snoopy->fetch("http://www.google.com/musicsearch?btnG=Search+Music&q=%22". urlencode($album). "%22+%22". urlencode($artist). "%22");
		$contents = $snoopy->results;
		unset($snoopy);

		// Now let's fix up the name
		$albumName = str_replace("&","&amp;",$album);
		$artist = str_replace("&","&amp;",$artist);
		
		// First let's get the image
		$contents = substr($contents,strpos($contents,"All albums with matching titles shown"));
		$contents = substr($contents,strpos($contents,"<img src=") + strlen("<img src="));
		$image = substr($contents,0,strpos($contents," "));

		// Now let's get the rating
		$rating = substr_count($contents,"showtimes-star-on.gif");
		
		// Now let's return
		if (!$return){
			writeAlbumMetaData($node, "", $image, "", "", $rating, "", "", $displayOutput);
			return true;
		} else {
			if ($return == "array"){
				$retArr['image'] = $image;
				$retArr['rating'] = $rating;
				
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
	function SERVICE_GETARTISTMETADATA_google($node){
		global $include_path;
		
		// Ok, now we need to see if we are reading a album or an artist
		$artist = $node->getName();
				
		include_once($include_path. "lib/snoopy.class.php");
		$snoopy = new Snoopy;
		$snoopy->fetch("http://www.google.com/musicsearch?btnG=Search+Music&q=%22". urlencode($artist). "%22");
		$contents = $snoopy->results;
		unset($snoopy);
		
		$contents = substr($contents,strpos($contents,"All artists shown"));
		$contents = substr($contents,strpos($contents,"<img src=") + strlen("<img src="));
		$image = trim(substr($contents,0,strpos($contents," ")));
		
		// Now let's write the data
		if ($return){
			if ($return == "array"){
				$retArr['image'] = $image;					
				return $retArr;
			} else {
				return $$return;
			}
			return $$return;
		} else {
			$artReturn = writeArtistMetaData($node, $image, "", $displayOutput);
		}
	}
?>