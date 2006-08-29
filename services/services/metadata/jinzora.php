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
	 * - This is the Jinzora Metadata service - it retrieves data from www.jinzora.com
	 *
	 * @since 01.14.05
	 * @author Ross Carlson <ross@jinzora.org>
	 * @author Ben Dodson <ben@jinzora.org>
	 */
	
	$jzSERVICE_INFO = array();
	$jzSERVICE_INFO['name'] = "Jinzora.com";
	$jzSERVICE_INFO['url'] = "http://www.jinzora.com";

	define('SERVICE_METADATA_jinzora','true');
	
	function sendItemData($node, $displayOutput, $album, $artist, $year, $image, $review, $rating, $return){
	
		// Should we share this data?
		$shareData=false;
		if ($shareData == "true"){
			// Ok, now let's insert this at jinzora.com
			$snoopy = new Snoopy;
			$snoopy->fetch("http://www.jinzora.com/metasearch.php?add=true&type=album&name=". urlencode($album). "&artist=". urlencode($artist). "&year=". urlencode($year). "&image=". urlencode($image). "&review=". urlencode($review). "&rating=". urlencode($rating));
			$contents = $snoopy->results;
			unset($snoopy);
		}

		// Now that we have all the data we should write it back out
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

	/*
	 * Gets the metadata for an album
	 * 
	 * @author Ross Carlson
	 * @version 1/15/05
	 * @since 1/15/05
	 * @param $node The current node we are looking at
	 * @param $displayOutput Should we dispaly output (defaults to true)
	 **/	
	function SERVICE_GETALBUMMETADATA_jinzora($node, $displayOutput = true, $return = false) {
		global $include_path;
		
		if ($displayOutput){
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				ars.innerHTML = '<?php echo word("Status: Searching"). " jinzora.com..."; ?>';
				-->
			</SCRIPT>
			<?php
			flushdisplay();
		}
		
		// Ok, now we need to see if we are reading a album or an artist
	 	$album = $node->getName(); 
		$parent = $node->getParent();
		$artist = $parent->getName();
				
		include_once($include_path. "lib/snoopy.class.php");
		
		// First we'll try to get this from Jinzora.com
		$snoopy = new Snoopy;
		$snoopy->fetch("http://www.jinzora.com/metasearch.php?type=album&name=". urlencode($album). "&artist=". urlencode($artist));
		$contents = $snoopy->results;
		unset($snoopy);

		if ($contents <> "false"){
			// We got data, let's figure out what
			$data = unserialize($contents);			
			
			if ($data['id'] <> ""){
				$retArr['id'] = $data['id'];
			} 
			if ($data['year'] <> ""){
				$retArr['year'] = $data['year'];
			} else {
				// Ok, now let's try to get the year from MSN Music
				include_once($include_path. "services/services/metadata/msnmusic.php");
				$retArr['review'] = SERVICE_GETALBUMMETADATA_msnmusic($node, false, "year");
			}
			if ($data['image'] <> ""){
				$retArr['image'] = $data['image'];
			} else {
				// Ok, now let's try to get the image from MSN Music
				include_once($include_path. "services/services/metadata/msnmusic.php");
				$retArr['image'] = SERVICE_GETALBUMMETADATA_msnmusic($node, false, "image");
			}
			if ($data['review'] <> ""){
				$retArr['review'] = $data['review'];
			} else {
				// Ok, now let's try to get the image from MSN Music
				include_once($include_path. "services/services/metadata/msnmusic.php");
				$retArr['review'] = SERVICE_GETALBUMMETADATA_msnmusic($node, false, "review");
			}
			if ($data['rating'] <> ""){
				$retArr['rating'] = $data['rating'];
			} else {
				// Ok, now let's try to get the image from MSN Music
				include_once($include_path. "services/services/metadata/msnmusic.php");
				$retArr['rating'] = SERVICE_GETALBUMMETADATA_msnmusic($node, false, "rating");
			}
			
			if ($return){
				if ($return == "array"){
					return $retArr;
				} else {
					return $retArr[$return];
				}
			} else {
				return $retArr;
			}
		} else {			
			// Now let's get from MSN Music
			if ($displayOutput){
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					ars.innerHTML = '<?php echo word("Status: Searching"). " MSN Music..."; ?>';
					-->
				</SCRIPT>
				<?php
				flushdisplay();
			}
			include_once($include_path. "services/services/metadata/msnmusic.php");
			$data = SERVICE_GETALBUMMETADATA_msnmusic($node, false, true);
			
			if ($data){
				// Now let's send it
				return sendItemData($node, $displayOutput, $album, $artist, $data['year'], $data['image'], $data['review'], $data['rating'], $return);
			}

			// Now let's get from Yahoo Music
			if ($displayOutput){
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					ars.innerHTML = '<?php echo word("Status: Searching"). " Yahoo Music..."; ?>';
					-->
				</SCRIPT>
				<?php
				flushdisplay();
			}
			include_once($include_path. "services/services/metadata/yahoo.php");
			$data = SERVICE_GETALBUMMETADATA_yahoo($node, false, true);
			
			if ($data){
				// Now let's send it
				return sendItemData($node, $displayOutput, $album, $artist, $data['year'], $data['image'], $data['review'], $data['rating'], $return);
			}
			
			// Now let's get from Rollingstone
			if ($displayOutput){
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					ars.innerHTML = '<?php echo word("Status: Searching"). " Rollingstone..."; ?>';
					-->
				</SCRIPT>
				<?php
				flushdisplay();
			}
			include_once($include_path. "services/services/metadata/rs.php");
			$data = SERVICE_GETALBUMMETADATA_rs($node, false, true);
			
			if ($data){
				// Now let's send it
				return sendItemData($node, $displayOutput, $album, $artist, $data['year'], $data['image'], $data['review'], $data['rating'], $return);
			}
			
			// Now let's get from Musicbrainz
			if ($displayOutput){
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					ars.innerHTML = '<?php echo word("Status: Searching"). " Musicbrainz..."; ?>';
					-->
				</SCRIPT>
				<?php
				flushdisplay();
			}
			include_once($include_path. "services/services/metadata/musicbrainz.php");
			$data = SERVICE_GETALBUMMETADATA_musicbrainz($node, false, true);
			
			if ($data){
				// Now let's send it
				return sendItemData($node, $displayOutput, $album, $artist, $data['year'], $data['image'], $data['review'], $data['rating'], $return);
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
	function SERVICE_GETARTISTMETADATA_jinzora($node, $displayOutput, $return = false){
		global $include_path;
		
		// let's set the artist we're looking at
		$artist = $node->getName();
		
		include_once($include_path. "services/services/metadata/msnmusic.php");
		$bio = SERVICE_GETARTISTMETADATA_msnmusic($node, false, "bio");
		$image = SERVICE_GETARTISTMETADATA_msnmusic($node, false, "image");

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
?>
