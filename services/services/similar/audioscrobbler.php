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
	$jzSERVICE_INFO['name'] = "Audioscrobbler";
	$jzSERVICE_INFO['url'] = "http://www.last.fm";
	
	define('SERVICE_SIMILAR_audioscrobbler','true');
	
	/**
	* Returns an array of similar artists
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 01/14/05
	* @since 01/14/05
	* @param $node The node for the artist/album
	* @param $limit Limit the number of results
	*/
	function SERVICE_SIMILAR_audioscrobbler($element, $limit = false) {
	  
	  if ($element === false) return;
	  
	  $cachename = "similar-scrobbler-" . $element->getName();
	  
	  // Let's setup the backend to read the cache to see if we can just return it
	  $be = new jzBackend();
	  if (false !== ($data = $be->loadData($cachename))) {
	    if ($limit && $limit > sizeof($data)) {
	      return array_slice($data,0,$limit);
	    } else {
	      return $data;
	    }
	  }

	  $artist = $element->getName();
	  if (stristr($artist,", The") !== false) {
	    $artist = str_replace(", The", "", $artist);
	    $artist = "The " . $artist;
	  }
	  // Let's grab the data first
	  $server = "ws.audioscrobbler.com";
	  $page = "/1.0/artist/" . rawurlencode($artist) . "/similar.txt";

	  $fp = @fsockopen($server,80, $errno, $errstr, 2);
	  if ($fp) {
	    fputs($fp, "GET ${page} HTTP/1.1\r\nHost:${server}\r\n\r\n");
	    fputs($fp, "Connection: close\n\n");

	    $data = "";
	    $blnHeader = true;
	    while (!feof($fp)){
	      if ($blnHeader) {
		if (fgets($fp,1024) == "\r\n"){
		  $blnHeader = false;
		}
	      } else {
		$data .= fread($fp,1024);
	      }	
	    }
	    fclose($fp);
	    if (stristr($data,'No artist exists with this name')) {
	      $sim_artists = array();
	    } else {
	      $sim_artists = array();
	      $data = explode("\n",$data);
	      foreach ($data as $entry) {
		$match = substr($entry,0,strpos($entry,','));
		if ($match > 50) { // a decent match... let's add it.
		  $sim_artists[] = substr($entry,strpos($entry,',',strpos($entry,',')+1)+1); // Get it after the second comma.
		} else {
		  break;
		}
	      }
	    }
	  } else {
	    return false;
	  }

	  // Now let's store this for later caching
	  $be->storeData($cachename, $sim_artists, 7); // expires in 7 days
	  
	  // Now let's return
	  if ($limit) {
	    return array_slice($sim_artists,0,$limit);
	  } else {
	    return $sim_artists;
	  }
	}
