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
	
	define('SERVICE_SIMILAR_msnmusic','true');
	
	/**
	* Returns an array of similar artists
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 01/14/05
	* @since 01/14/05
	* @param $node The node for the artist/album
	* @param $limit Limit the number of results
	*/
	function SERVICE_SIMILAR_msnmusic($element, $limit = false) {
		global $include_path;
		// Let's setup the root node
		$root = new jzMediaNode();
		$artist = $element->getName();
		
		// Let's make sure we've flushed up to here since this thing is slow
		flushdisplay();
		
		// Now let's search music.msn.com
		include_once($include_path. "lib/snoopy.class.php");
		$snoopy = new Snoopy;
		$snoopy->fetch("http://music.msn.com/search/all/?ss=". urlencode($artist));
		$contents = $snoopy->results;
		unset($snoopy);
		
		// Ok, now let's see if we got a direct hit or a link
		if (stristr($contents,$artist)){
			// Now let's see if we can get the right link
			$contents = substr($contents,strpos($contents,$artist. "</a>")-50);
			$link = substr($contents,strpos($contents,"href")+6);
			$link = substr($link,0,strpos($link,'"'));
			
			// Now let's get that page back
			$snoopy = new Snoopy;
			$snoopy->fetch("http://music.msn.com". $link);
			$contents = $snoopy->results;
			unset($snoopy);
			
			// Now let's find the artist image
			$contents = substr($contents,strpos($contents,"Listeners Also Liked"));
			$contents = substr($contents,strpos($contents,"<tr>"));
			
			// Now let's build an array we can search through
			$arr = explode("\n",$contents);
			for ($e=0;$e<count($arr);$e++){
				if (stristr($arr[$e],'href="/artist/?')){
					$artist = substr($arr[$e],strpos($arr[$e],'">')+2);
					$artist = substr($artist,0,strpos($artist,'</a>'));
					$retArray[] = $artist;
				}
			}
			// Now let's return
			return $retArray;
		} else {
			return false;
		}
	}
?>