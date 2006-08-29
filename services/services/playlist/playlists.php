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
	* - Creates an M3U compliant playlist
	*
	* @since 02.17.05
	* @author Ben Dodson <ben@jinzora.org>
	* @author Ross Carlson <ross@jinzora.org>
	*/
	
	$jzSERVICE_INFO = array();
	$jzSERVICE_INFO['name'] = "Playlist Generator";
	$jzSERVICE_INFO['url'] = "http://www.jinzora.org";

	define('SERVICE_PLAYLIST_playlists','true');


	
	/**
	* Returns the mime type for this playlist
	* 
	* @author Ben Dodson
	* @version 2/24/05
	* @since 2/24/05
	* @param $return Returns the playlist mime type
	*/
	function SERVICE_RETURN_MIME($type=false){
	  global $jzUSER,$include_path;
	  if ($type === false) {
	    $type = $jzUSER->getSetting('playlist_type');
	  }
	  require_once($include_path."services/services/playlist/".$type.".php");
	  $FUNC_NAME = "SERVICE_RETURN_MIME_" . strtoupper($type);
	  return $FUNC_NAME();
	}
	
	/**
	* Router for the playlist generation.
	* 
	* @author Ben Dodson
	* @version 2/24/05
	* @since 2/24/05
	* @param $list The list of tracks to use when making the playlist
	* @param $return Returns the porperly formated list
	*/
	function SERVICE_CREATE_PLAYLIST($list,$type=false){
	  global $jzUSER, $include_path;

	  if ($type === false) {
	    $type = $jzUSER->getSetting('playlist_type');
	  }
	  require_once($include_path."services/services/playlist/".$type.".php");
	  $FUNC_NAME = "SERVICE_CREATE_PLAYLIST_" . strtoupper($type);
	  return $FUNC_NAME($list);
	}

	function SERVICE_GET_PLAYLIST_TYPES() {
	  // Just hard code them for now:
	  $playlist_types = array();
	  $playlist_types['m3u'] = "M3U (Standard)";
		$playlist_types['pls'] = "PLS (Winamp - Linux Players)"; 
	  $playlist_types['asx'] = "ASX (Windows Media Player)";
	  $playlist_types['ram'] = "RAM (Real Player)";
	
	  return $playlist_types;
	}

?>