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
	* - This is the Filesystem tag data service
	* - It reads the meta data from the filesystem ONLY for speed
	*
	* @since 02.17.05
	* @author Ben Dodson <ben@jinzora.org>
	* @author Ross Carlson <ross@jinzora.org>
	*/
	
	$jzSERVICE_INFO = array();
	$jzSERVICE_INFO['name'] = "Filesystem";
	$jzSERVICE_INFO['url'] = "http://www.jinzora.com";
	
	define('SERVICE_TAGDATA_filesystem','true');
	
	
	function SERVICE_SET_TAGDATA_filesystem($fname, $meta){
		// Not supported in the filesystem tag data functions
		return;
	}
	
	function SERVICE_GET_TAGDATA_filesystem($fname, $installer = false) {
		global $include_path,$hierarchy;
		
		// First there are a lot of things this doesn't suppport, let's set those first
		$meta['bitrate'] = "";
		$meta['length'] = "";
		$meta['year'] = "";
		$meta['frequency'] = "";
		$meta['width'] = "";
		$meta['height'] = "";
		
		// Let's take the file name apart and figure this all out
		$sep = "/";
		$nameArr = explode($sep,$fname);
		
		// Now let's figure out the track specefic stuff
		$name = $nameArr[count($nameArr)-1];
		if (is_numeric(substr($name,0,2))){
			$number = substr($name,0,2);
			$name = trim(substr($name,3));
		}
		if (substr($name,0,1) == "-" or substr($name,0,1) == "_"){
			$name = trim(substr($name,1));
		}
		$dot = strrpos($fname,'.');
		if ($dot !== false) {
		  $meta['type'] = $meta['extension'] = $ext = substr($fname,$dot+1);
		}
		$meta['title'] = str_replace(".".$ext,"",$name);
		$meta['number'] = $number;
		
		// Now let's get album, artist, genre
		$hier = explode('/',$hierarchy);
		if (false !== ($key = array_search('album',$hier))) {
		  $meta['album'] = $nameArr[sizeof($nameArr)-sizeof($hier)+$key];
		} else {
		  $meta['album'] = "";
		}

		if (false !== ($key = array_search('artist',$hier))) {
		  $meta['artist'] = $nameArr[sizeof($nameArr)-sizeof($hier)+$key];
		} else {
		  $meta['artist'] = "";
		}

		if (false !== ($key = array_search('genre',$hier))) {
		  $meta['genre'] = $nameArr[sizeof($nameArr)-sizeof($hier)+$key];
		} else {
		  $meta['genre'] = "";
		}
		
		// Now let's get the size
		$meta['size'] = round(((filesize($fname) / 1024) / 1024),2);

		// Now let's return what we go
		return $meta;
	}
?>