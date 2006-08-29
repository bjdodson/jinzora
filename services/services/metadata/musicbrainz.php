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
	 * - Retrieves meta data from Amazon Web Services
	 *
	 * @since 01.14.05
	 * @author Ross Carlson <ross@jinzora.org>
	 */
	
	$jzSERVICE_INFO = array();
	$jzSERVICE_INFO['name'] = "Allmusic.com";
	$jzSERVICE_INFO['url'] = "http://www.allmusic.com";

	define('SERVICE_METADATA_musicbrainz','true');

	/*
	 * Gets the metadata for an album
	 * 
	 * @author Ross Carlson
	 * @version 1/15/05
	 * @since 1/15/05
	 * @param $node The current node we are looking at
	 * @param $displayOutput Should we dispaly output (defaults to true)
	 **/	
	function SERVICE_GETALBUMMETADATA_musicbrainz($node, $displayOutput = true, $return = false) {
		global $include_path;
		
		// Ok, now we need to see if we are reading a album or an artist
	 	$album = $node->getName(); 
		$parent = $node->getParent();
		$artist = $parent->getName();
		
		// Ok, now let's include the musicbrainz classes
		include_once($include_path. "services/services/metadata/musicbrainz/phpBrainz.class.php");
		
		// Let's setup our object
		$queryObj = new mbQuery();
		
		// Now let's get the artist info so we can get the artist ID
		$results = $queryObj->getArtistByName($artist,10);
		$found = false;
		if (count($results) > 0){
			foreach($results as $result) {
				if ($result['title'] == $artist){
					$artistID = substr($result['artistid'],strrpos($result['artistid'],"/")+1);
					break;
				}
			}
			if ($artistID <> ""){
				// Now let's get the album info
				$results = $queryObj->getAlbumByName($album,10);
				// Now let's make sure we got results
				if (count($results) > 0){
					foreach($results as $result) {
						$albumArtistID = substr($result['creator']['artistid'],strrpos($result['creator']['artistid'],"/")+1);
						// Now let's see if we got a match on the artist
						if ($artistID == $albumArtistID){
							$found = true;
							$image = $result['coverArt']['large'];
							// Now let's get the track details
							$tracks = $result['trackList'];
							$albumID = substr($result['albumid'],strrpos($result['albumid'],"/")+1);
							foreach($tracks as $track){
								$trackID = substr($track,strrpos($track,"/")+1);
								$data = $queryObj->getQuickTrackInfoFromID($trackID, $albumID);
								$tArray[] = $data['trackName'];
								if (!isset($year) and isset($data['releaseDateList'])){
									foreach($data['releaseDateList'] as $date){
										if (strlen($date['date']) == 4){
											$year = $date['date'];
										}
									}
								}
							}
						}
					}
				}
			} 
		} 
		
		// Did we find anything?
		if ($found){
			// Now let's convert
			$tracks = $tArray;
			if (!$return){
				writeAlbumMetaData($node, $year, $image, $tracks, false, false, false, false, $displayOutput);
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
		} else {
		  if ($displayOutput) {
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				als.innerHTML = 'Album not found!';					
				-->
			</SCRIPT>
			<?php
			flushdisplay();
		  }
			return false;
		}
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
	function SERVICE_GETARTISTMETADATA_musicbrainz($node = false, $return = false, $artistName = false){
		global $include_path;
		
		return;
	}
	
?>