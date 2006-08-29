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
	$jzSERVICE_INFO['name'] = "Echocloud";
	$jzSERVICE_INFO['url'] = "http://www.echocloud.net";
	
	define('SERVICE_SIMILAR_echocloud','true');
	
	/**
	* Returns an array of similar artists
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 01/14/05
	* @since 01/14/05
	* @param $node The node for the artist/album
	* @param $limit Limit the number of results
	*/
	function SERVICE_SIMILAR_echocloud($element, $limit = false) {
	  
	  if ($element === false) return;

		// Let's setup the backend to read the cache to see if we can just return it
		$be = new jzBackend();
		$data = $be->loadData("similar-data-" . $element->getName());
		$tArr = explode("\n",$data);
		// Now let's see if this is more than 1 week old, if so let's update
		if ($data !== false && ($tArr[0] + 604800) > time()){
			// Ok, the cache is less than 1 week old let's return it
			return unserialize($tArr[1]);
		}

		// Let's setup the root node
		$root = new jzMediaNode();
		$artist = $element->getName();
		$artist = preg_replace( "/^$artist, *the$/i", "The $artist", $artist );

		// Let's grab the data first
		$ec_con = returnEchocloudData($artist);
		if (!$ec_con){return false;}

		// Let's make sure that opened ok and if so parse the data
		if ($ec_con <> "" and strlen($ec_con) > 600){
			// Ok, now let's clean up what we got back
			$ec_con = substr($ec_con,0,strpos($ec_con,"</rs>"));
			$ec_con = substr($ec_con,strpos($ec_con,"</date>")+7,strlen($ec_con));
			// Now let's split it out by each item
			$ecArray = explode("<r>",$ec_con);
			$nonMatch = "";
			$search_array = array();
			for ($i=0; $i < count($ecArray); $i++){
				if ($ecArray[$i] <> ""){
					// Ok, now let's read the items
					$ecArtist = substr($ecArray[$i],3,strlen($ecArray[$i]));
					$ecArtist = substr($ecArtist,0,strpos($ecArtist,"<"));
					$ecArtist = str_replace("'","",$ecArtist);
					if ($ecArtist <> $artist and $ecArtist <> ""){
					  $retArray[] = $ecArtist;
					}
				}
			}
			// Now we know all the artists that are 'similar'.
			// Now should we limit?
			if ($limit){
			  $retArray = @array_slice($retArray,0,$limit);
			}
			
			// Now let's store this for later caching
			$be->storeData("similar-data-" . $element->getName(), time(). "\n". serialize($retArray));
			
			// Now let's return
			return $retArray;
		}
		return false;
	}
	
	/**
	* Returns similar artist data from Echocloud
	* 
	* @author Ross Carlson
	* @version 07/16/04
	* @version 07/16/04
	* @param string $data the data to search for
	* @return retuns the text of the description
	*/
	function returnEchocloudData($data){
		global $echocloud;
		
		// Let's see if they wanted to NOT show data
		if ($echocloud == "0"){ return; }
		
		$ec_con = "";
		$fp = @fsockopen('echocloud.jinzora.org',80, $errno, $errstr, 2);
		// Let's make sure that opened ok
		if ($fp){
			$path = '/psearch.php?searchword='. rawurlencode($data). '&nrows=25';
			fputs($fp, "GET $path HTTP/1.1\r\nHost:echocloud.jinzora.org\r\n\r\n");
			fputs($fp, "Connection: close\n\n");
			// Now let's read all the data
			$blnHeader = true;
			while (!feof($fp)){
				if ($blnHeader) {
					if (fgets($fp,1024) == "\r\n"){
						$blnHeader = false;
					}
				} else {
					$ec_con .= fread($fp,1024);
				}	
			}
			fclose($fp);
		}

		if ($ec_con <> ""){
			return $ec_con;
		} else {
			return false;
		}
	}
?>
