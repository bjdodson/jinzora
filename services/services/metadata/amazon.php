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

	define('SERVICE_METADATA_amazon','true');

	/*
	 * Gets the metadata for an album
	 * 
	 * @author Ross Carlson
	 * @version 1/15/05
	 * @since 1/15/05
	 * @param $node The current node we are looking at
	 * @param $displayOutput Should we dispaly output (defaults to true)
	 **/	
	function SERVICE_GETALBUMMETADATA_amazon($node, $displayOutput = true, $return = false) {
		global $include_path;
		
		// Ok, now we need to see if we are reading a album or an artist
	 	$album = $node->getName(); 
		$parent = $node->getParent();
		$artist = $parent->getName();
		
		// Now let's clean that up some
		if (stristr($album,"(")){$album = trim(substr($album,0,strpos($album,"(")));}
		if (stristr($album,"[")){$album = trim(substr($album,0,strpos($album,"[")));}
		$album = str_replace("_"," ",$album);
		
		$amazon_key = "19B1FW4R5ABSKBWNV582";
				
		include_once($include_path. "lib/snoopy.class.php");
		$snoopy = new Snoopy;
		$snoopy->fetch("http://xml.amazon.com/onca/xml3?KeywordSearch=". urlencode($album). "&dev-t=". $amazon_key. "&f=xml&locale=us&mode=music&page=1&t=chipdir&type=heavy");
		$contents = $snoopy->results;
		unset($snoopy);

		// Now let's move to the results
		$contents = substr($contents,strpos($contents,"</TotalPages>")+strlen("</TotalPages>"));
		// Ok, now let's make sure the search we did returned the right album
		if (!stristr($contents,$album) or !stristr($contents,"<Artist>". $artist) or stristr($contents,"There are no exact matches for the search.")){
			// Now let's try doing it with adding the artist to the query
			$snoopy = new Snoopy;
			$snoopy->fetch("http://xml.amazon.com/onca/xml3?KeywordSearch=". urlencode($album. " ". $artist). "&dev-t=". $amazon_key. "&f=xml&locale=us&mode=music&page=1&t=chipdir&type=heavy");
			$contents = $snoopy->results;
			unset($snoopy);
	
			// Now let's move to the results
			$contents = substr($contents,strpos($contents,"</TotalPages>")+strlen("</TotalPages>"));
		}		
		
		if (stristr($contents,$album) and stristr($contents,"<Artist>". $artist) and !stristr($contents,"There are no exact matches for the search.")){
			$contents = substr($contents,strpos($contents,$artist));			
			// Ok, we got it, let's get the data
			$ReleaseDate="";$ImageUrlLarge="";$Tracks="";$ProductDescription="";$AvgCustomerRating="";$ListPrice="";$BrowseName="";
			$searchVals = "ReleaseDate|ImageUrlLarge|Tracks|ProductDescription|AvgCustomerRating|ListPrice|BrowseName";
			$searchArray = explode("|",$searchVals);
			for ($e=0; $e < count($searchArray); $e++){
				if (stristr($contents,$searchArray[$e])){
					$$searchArray[$e] = str_replace("]]>","",str_replace("<![CDATA[","",substr($contents,strpos($contents,"<". $searchArray[$e]. ">")+ strlen("<". $searchArray[$e]. ">"),strpos($contents,"</". $searchArray[$e]. ">") - (strpos($contents,"<". $searchArray[$e]. ">") + strlen("<". $searchArray[$e]. ">")))));
				}
			}
			// Now let's fix up the tracks	
			$tArray = explode("\n",$Tracks);
			$i=0;
			for ($e=0; $e < count($tArray); $e++){	
				$track = substr($tArray[$e],strpos($tArray[$e],"<Track>")+strlen("<Track>"));
				$track = substr($track,0,strpos($track,"</Track>"));
				$tracks[$i] = $track;
				$i++;
			}
			$genre = $BrowseName;
			$ProductDescription = str_replace("&lt;I>","",$ProductDescription);
			$ProductDescription = str_replace("&lt;i>","",$ProductDescription);
			$ProductDescription = str_replace("&lt;/I>","",$ProductDescription);
			$ProductDescription = str_replace("&lt;/i>","",$ProductDescription);
			
			// Now let's clean up the release date
			$year = trim(substr($ReleaseDate,strpos($ReleaseDate,", ")+2));
			
			// Now let's get the ID number for the Amazon site
			$id = substr($ImageUrlLarge,strlen("http://images.amazon.com/images/P/"));
			$id = substr($id,0,strpos($id,"."));		
			
			// Now let's write this data IF they wanted to
			$image = $ImageUrlLarge;
			$review = $ProductDescription;
			$rating = $AvgCustomerRating;
			if (!$return){
				writeAlbumMetaData($node, $year, $image, $tracks, $review, $rating, $ListPrice, $genre, $displayOutput);
				return true;
			} else {
				if ($return == "array"){
					$retArr['year'] = $year;
					$retArr['image'] = $image;
					$retArr['review'] = $review;
					$retArr['rating'] = $rating;
					$retArr['id'] = $id;					
					$retArr['genre'] = $genre;					
					
					return $retArr;
				} else {
					return $$return;
				}
			}
		} else {
			if ($displayOutput){
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
	function SERVICE_GETARTISTMETADATA_amazon($node = false, $return = false, $artistName = false){
		global $include_path;
		
		// Is this a node or specific artist?
		if (is_object($node)){
			// Ok, now we need to see if we are reading a album or an artist
		 	$album = $node->getName(); 
			$parent = $node->getParent();
			$artist = $parent->getName();
			
			// Now let's clean that up some
			if (stristr($album,"(")){$album = trim(substr($album,0,strpos($album,"(")));}
			if (stristr($album,"[")){$album = trim(substr($album,0,strpos($album,"[")));}
			$album = str_replace("_"," ",$album);
		} else {
			$artist = $node['artist'];
			$album = $node['album'];
		}		
		
		$amazon_key = "19B1FW4R5ABSKBWNV582";
				
		include_once($include_path. "lib/snoopy.class.php");
		$snoopy = new Snoopy;
		$snoopy->fetch("http://xml.amazon.com/onca/xml3?KeywordSearch=". urlencode($album). "&dev-t=". $amazon_key. "&f=xml&locale=us&mode=music&page=1&t=chipdir&type=heavy");
		$contents = $snoopy->results;
		unset($snoopy);

		// Now let's move to the results
		$contents = substr($contents,strpos($contents,"</TotalPages>")+strlen("</TotalPages>"));
		// Ok, now let's make sure the search we did returned the right album
		if (!stristr($contents,$album) or !stristr($contents,"<Artist>". $artist) or stristr($contents,"There are no exact matches for the search.")){
			// Now let's try doing it with adding the artist to the query
			$snoopy = new Snoopy;
			$snoopy->fetch("http://xml.amazon.com/onca/xml3?KeywordSearch=". urlencode($album. " ". $artist). "&dev-t=". $amazon_key. "&f=xml&locale=us&mode=music&page=1&t=chipdir&type=heavy");
			$contents = $snoopy->results;
			unset($snoopy);
	
			// Now let's move to the results
			$contents = substr($contents,strpos($contents,"</TotalPages>")+strlen("</TotalPages>"));
		}		
		
		if (stristr($contents,$album) and stristr($contents,"<Artist>". $artist) and !stristr($contents,"There are no exact matches for the search.")){
			$contents = substr($contents,strpos($contents,$artist));			
			// Ok, we got it, let's get the data
			$ReleaseDate="";$ImageUrlLarge="";$Tracks="";$ProductDescription="";$AvgCustomerRating="";$ListPrice="";$BrowseName="";
			$searchVals = "ReleaseDate|ImageUrlLarge|Tracks|ProductDescription|AvgCustomerRating|ListPrice|BrowseName";
			$searchArray = explode("|",$searchVals);
			for ($e=0; $e < count($searchArray); $e++){
				if (stristr($contents,$searchArray[$e])){
					$$searchArray[$e] = str_replace("]]>","",str_replace("<![CDATA[","",substr($contents,strpos($contents,"<". $searchArray[$e]. ">")+ strlen("<". $searchArray[$e]. ">"),strpos($contents,"</". $searchArray[$e]. ">") - (strpos($contents,"<". $searchArray[$e]. ">") + strlen("<". $searchArray[$e]. ">")))));
				}
			}
			// Now let's fix up the tracks	
			$tArray = explode("\n",$Tracks);
			$i=0;
			for ($e=0; $e < count($tArray); $e++){	
				$track = substr($tArray[$e],strpos($tArray[$e],"<Track>")+strlen("<Track>"));
				$track = substr($track,0,strpos($track,"</Track>"));
				$tracks[$i] = $track;
				$i++;
			}
			$genre = $BrowseName;
			$ProductDescription = str_replace("&lt;I>","",$ProductDescription);
			$ProductDescription = str_replace("&lt;i>","",$ProductDescription);
			$ProductDescription = str_replace("&lt;/I>","",$ProductDescription);
			$ProductDescription = str_replace("&lt;/i>","",$ProductDescription);
			
			// Now let's clean up the release date
			$year = trim(substr($ReleaseDate,strpos($ReleaseDate,", ")+2));
			
			// Now let's get the ID number for the Amazon site
			$id = substr($ImageUrlLarge,strlen("http://images.amazon.com/images/P/"));
			$id = substr($id,0,strpos($id,"."));		
			
			// Now let's write this data IF they wanted to
			$image = $ImageUrlLarge;
			$review = $ProductDescription;
			$rating = $AvgCustomerRating;
			if (!$return){
				writeAlbumMetaData($node, $year, $image, $tracks, $review, $rating, $ListPrice, $genre, $displayOutput);
				return true;
			} else {
				if ($return == "array"){
					$retArr['year'] = $year;
					$retArr['image'] = $image;
					$retArr['review'] = $review;
					$retArr['rating'] = $rating;
					$retArr['id'] = $id;					
					$retArr['genre'] = $genre;					
					
					return $retArr;
				} else {
					return $$return;
				}
			}
		} else {
			if ($displayOutput){
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
	
?>
