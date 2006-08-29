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
 * - This class bundles all of the external services for Jinzora
 *
 * @since 01.14.05
 * @author Ross Carlson <ross@jinzora.org>
 * @author Ben Dodson <ben@jinzora.org>
 */

class jzServices {
  var $services;
  
  function loadService($type, $service) {
    global $include_path;

	if (strpos($type,"..") !== false) {
		die('Security breach detected.');
	}
	
	if (strpos($service,"..") !== false) {
		die('Security breach detected.');
	}

    switch ($type) {
    case "lyrics":
      $function_name = "SERVICE_LYRICS";
      break;
    case "similar":
      $function_name = "SERVICE_SIMILAR";
      break;
		case "metadata":
      $function_name = "SERVICE_METADATA";
      break;
    case "link":
	  	$function_name = "SERVICE_LINK";
      break;
	 case "tagdata":
	  $function_name = "SERVICE_TAGDATA";
      break;
		case "playlist":
			$function_name = "SERVICE_PLAYLIST";
			break;
		case "players":
			$function_name = "SERVICE_PLAYERS";
			break;
		case "images":
			$function_name = "SERVICE_IMAGES";
			break;
		case "resample":
			$function_name = "SERVICE_RESAMPLE";
			break;
		case "cdburning":
			$function_name = "SERVICE_CDBURNING";
			break;
		case "cms":
			$function_name = "SERVICE_CMS";
			break;
		case "reporting":
			$function_name = "SERVICE_REPORTING";
			break;
		case "shopping":
			$function_name = "SERVICE_SHOPPING";
			break;
		case "importing":
			$function_name = "SERVICE_IMPORTING";
			break;
    default:
      return;
    }

    if (!file_exists($include_path."services/services/${type}/${service}.php")) {
      return false;
    }
		
		writeLogData("messages","Services: Loading service: ". $service. " type: ". $type);
    $this->loaded[$type] = $service;

	if (!defined($function_name . "_" . $service)) {
		require_once($include_path."services/services/${type}/${service}.php");
		if (isset($jzSERVICE_INFO)){
			$this->service_info[$type] = $jzSERVICE_INFO;
			$this->cached[$type][$service] = $jzSERVICE_INFO;
			unset($jzSERVICE_INFO);
		} else {
			$arr = array();
			$arr['name'] = $service;
			$this->service_info[$type][$service] = $arr;
		}
	} else { // already loaded.
		if (isset($this->cached[$type][$service])){
			$this->service_info[$type] = $this->cached[$type][$service];
		}
	}
  }
  
  function jzServices() {
    $this->_constructor();
  }

  function _constructor() {
    global $include_path;
    $this->loaded = array();
    $this->service_info = array();
    $this->cached = array();
    // Also load auxilary functions:
    require_once($include_path.'services/services.php');
  }

  function loadStandardServices() {
    global 	$service_lyrics, $service_similar,
      		$service_link, $service_metadata, 
      		$service_tagdata, $include_path,
      		$cms_mode,$cms_type,
      		$service_shopping, $service_images,
      		$service_cdburning, $backend;

    require_once($include_path.'services/settings.php');
    $this->loadService("lyrics", $service_lyrics);
	$this->loadService("shopping", $service_shopping);
    $this->loadService("similar", $service_similar);
    $this->loadService("link", $service_link);
    $this->loadService("metadata", $service_metadata);
    $this->loadService("tagdata", $service_tagdata);
    $this->loadService("images", $service_images);
    $this->loadService("resample", "resample");
    $this->loadService("cdburning", $service_cdburning);
    $this->loadService("playlist", "playlists"); // playlists are a bit different.
    if ($cms_mode == "true") {
        $this->loadService("cms",$cms_type);
    } else {
        $this->loadService("cms","standalone");
    }
    // Importer
    if (false !== stristr($backend,"id3")) {
    	$this->loadService("importing","id3tags"); 
    } else {
    	$this->loadService("importing","filesystem");
    }
  }

  function loadUserServices() {
		global $jzUSER, $embedded_player;
	
		if (!isNothing($jzUSER->getSetting('player'))) {
			$this->loadService("players",$jzUSER->getSetting('player'));
		} else if ($embedded_player != "" && $embedded_player != "false") {
			$this->loadService("players",$embedded_player);
		}
  }

  // Wrappers for our services
  function burnTracks($node, $artist, $album){
  	return SERVICE_BURN_TRACKS($node, $artist, $album);
  }  
  
  function createResampledTrack($file, $format, $bitrate, $meta, $destination = false){
    return SERVICE_CREATE_RESAMPLED_TRACK($file, $format, $bitrate, $meta, $destination);
  }  
  
  function isResampleable($file){
    return SERVICE_IS_RESAMPLABLE($file);
  }
  
  function resampleFile($file,$name,$resample){
  	SERVICE_RESAMPLE($file,$name,$resample);
  }
  
  function rotateImage($image, $node){
  	$func = "SERVICE_ROTATE_IMAGE_" . $this->loaded['images'];
    return $func($image, $node);
  }
  function createImage($image, $dimensions, $text, $imageType, $forceCreate = "false"){
  	$func = "SERVICE_CREATE_IMAGE_" . $this->loaded['images'];
    return $func($image, $dimensions, $text, $imageType, $forceCreate);
  }
  
  function resizeImage($image, $dimensions, $dest = false, $imageType = "audio"){
  	$func = "SERVICE_RESIZE_IMAGE_" . $this->loaded['images'];
    return $func($image, $dimensions, $dest, $imageType);
  }
  
  function returnPlayerHref(){
		if ($this->loaded['players'] == ""){
			$this->loadUserServices();
		}
    $func = "SERVICE_RETURN_PLAYER_HREF_" . $this->loaded['players'];
    return $func();
  }
  
  function returnPlayerFormLink($formname){
    $func = "SERVICE_RETURN_PLAYER_FORM_LINK_" . $this->loaded['players'];
    return $func($formname);
  }

  function returnPlayerWidth() {
    $func = "SERVICE_RETURN_PLAYER_WIDTH_" . $this->loaded['players'];
    return $func();
  }

  function returnPlayerHeight() {
    $func = "SERVICE_RETURN_PLAYER_HEIGHT_" . $this->loaded['players'];
    return $func();
  }
  
  function displayPlayer(){
    $func = "SERVICE_DISPLAY_PLAYER_" . $this->loaded['players'];
    return $func();
  }
  
  function openPlayer($list){
    $func = "SERVICE_OPEN_PLAYER_" . $this->loaded['players'];
    return $func($list);
  }
  
  function createPlaylist($list, $type = false){
    return SERVICE_CREATE_PLAYLIST($list, $type);
  }
  
  function getPLMimeType($type = false){
    return SERVICE_RETURN_MIME($type);
  }

  function getPLTypes() {
    return SERVICE_GET_PLAYLIST_TYPES();
  }
  
  function getTagData($track, $installer = false) {
    $func = "SERVICE_GET_TAGDATA_" . $this->loaded['tagdata'];
    return $func($track, $installer);
  }
  
  function setTagData($track, $meta){
    $func = "SERVICE_SET_TAGDATA_" . $this->loaded['tagdata'];
    return $func($track, $meta);
  }
  
  function getLyrics($track) {
    $func = "SERVICE_GETLYRICS_" . $this->loaded['lyrics'];
    return $func($track);
  }
  
  function getArtistMetadata($node, $return = false, $artistName = false) {
    $func = "SERVICE_GETARTISTMETADATA_" . $this->loaded['metadata'];
    return $func($node, $return, $artistName);
  }
  
  function getAlbumMetadata($node, $displayOutput = false, $return = false) {
    $func = "SERVICE_GETALBUMMETADATA_" . $this->loaded['metadata'];
    return $func($node, $displayOutput, $return);
  }

  function getSimilar($element, $limit = false) {
    $func = "SERVICE_SIMILAR_" . $this->loaded['similar'];
    return $func($element, $limit);
  }

  function link($type, $term) {
    $func = "SERVICE_LINK_" . $this->loaded['link'];
    return $func($type, $term);
  }

  function cmsOpen($authenticate_only = false) {
    $func = "SERVICE_CMSOPEN_" . $this->loaded['cms'];
    return $func($authenticate_only);
  }

  function cmsClose() {
    $func = "SERVICE_CMSCLOSE_" . $this->loaded['cms'];
    return $func();
  }

  function cmsCSS() {
    $func = "SERVICE_CMSCSS_" . $this->loaded['cms'];
    return $func();
  }

  function cmsGETVars() {
    $func = "SERVICE_CMSGETVARS_" . $this->loaded['cms'];
    return $func();
  }

  function cmsDefaultDatabase() {
    $func = "SERVICE_CMSDEFAULTDB_" . $this->loaded['cms'];
    return $func();
  }
	
	function updatePlayCountReporting($node) {
    $func = "SERVICE_REPORTING_" . $this->loaded['reporting'];
    return $func($node);
  }
	
	function createShoppingLink($node){
		$func = "SERVICE_CREATE_SHOPPING_LINK_" . $this->loaded['shopping'];
    return $func($node);
	}
	
	function importMedia($node, $media_path, $flags) {
		$func = "SERVICE_IMPORTMEDIA_" . $this->loaded['importing'];
		return $func($node,$media_path,$flags);
	}

}
?>
