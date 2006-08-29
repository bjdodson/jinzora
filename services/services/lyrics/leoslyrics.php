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
$jzSERVICE_INFO['name'] = "Leo's Lyrics";
$jzSERVICE_INFO['url'] = "http://www.leoslyrics.com";


define('SERVICE_LYRICS_leoslyrics','true');

/*
 * Gets the lyrics via Leo's Lyrics
 * 
 * @author Ross Carlson
 * @version 1/15/05
 * @since 1/15/05
 * @param $track a jzMediaTrack
 **/

function SERVICE_GETLYRICS_leoslyrics($track) {
	global $include_path; 
	
	include_once($include_path. "lib/snoopy.class.php");
	$meta = $track->getMeta();
	$artist = $meta['artist'];
	$name = $meta['title'];
	
	// Let's up the max execution time here
	ini_set('max_execution_time','60000');
	
	// Now let's see if we can get close...
	$snoopy = new Snoopy;
	$snoopy->fetch("http://api.leoslyrics.com/api_search.php?auth=Jinzora&artist=". urlencode($artist). '&songtitle='. urlencode($name). '&search=true');
	$contents = $snoopy->results;
	unset($snoopy);
	
	// Now let's see if we got an exact match
	if (stristr($contents,'exactMatch="true"') 
		or (strstr($contents,'SUCCESS')
		and (stristr($contents,$artist) and stristr($contents,$name)))
		){
		$lyrics = "";
		// Ok, now let's get the ID number
		$song_hid = substr($contents,strpos($contents,"hid=")+5,50);
		$song_hid = substr($song_hid,0,strpos($song_hid,'"'));
		// Now that we've got the HID let's get the lyrics
		// Now let's see if we get back the lyrics from leo's lyrics...
		$snoopy = new Snoopy;
		$snoopy->fetch("http://api.leoslyrics.com/api_lyrics.php?auth=Jinzora&hid=". urlencode($song_hid). '&file= NULL');
		$lyrics = $snoopy->results;
		unset($snoopy);
		
		// Now let's make sure that was successful
		if (stristr($lyrics,"SUCCESS")){
			// Now let's clean them up
			$lyrics = substr($lyrics,strpos($lyrics,"<text>")+6,999999);
			$lyrics = stripslashes(substr($lyrics,0,strpos($lyrics,"</text>")));
		}
	}
	
	if ($lyrics == "") {
		return false;
	}  
	return $lyrics;
}

?>