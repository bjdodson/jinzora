<?php 
	if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
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
	* Sets up much of the things that are needed but are not user definable
	*
	* @since 01.11.05
	* @author Ross Carlson <ross@jinzora.org>
	*/
	 
	//  * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	// These settings are NOT user editable
	// Edit at your own risk!
	//  * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

	if (!isset($root_dir)) {
		$root_dir = "";
	}
	if (!isset($cms_mode)) {
		$cms_mode = "false";
	} 
	// First let's set the web_root variable for when we need the whole path
	$web_root = str_replace($root_dir,"",str_replace("\\","/",getcwd()));
	// Now let's see if that's right, but looking to see if they are in virt directories
	if (stristr(str_replace("\\","/",getcwd()),$_SERVER['DOCUMENT_ROOT'])){
		$web_root = $_SERVER['DOCUMENT_ROOT'];
	}
	
	$web_root = str_replace("//","/",$web_root);
	if (substr($web_root,strlen($web_root)-1,1) == "/"){ $web_root = substr($web_root,0,strlen($web_root)-1); } 
	
	// Now let's make sure the web_root doesn't contain the root_dir
	if ($root_dir != "" && $web_root != "") {
		if (($pos = strpos($web_root,$root_dir)) !== false &&
		     $pos == (strlen($web_root) - strlen($root_dir))) {
		     	$web_root = substr($web_root,0,$pos);
		}
	}
	
	// Clear some vars.
	$backend = $skin = $my_frontend = $jz_language = $jz_lang_file = "";
	// Let's set some other system wide variables
	$this_pgm = "Jinzora";
	$version = "2.6.1";
	$jinzora_url = "http://www.jinzora.com";
	$show_jinzora_footer = true;
	$hide_pgm_name = false;
	$this_page = @$HTTP_SERVER_VARS["PHP_SELF"];
        $bad_chars = array("/","\\",":", "*","?","<",">","|");
	if ($cms_mode == "true"){
		$url_seperator = "&";	
	} else {
		$url_seperator = "?";	
	}
	if (isset($_SERVER["PHP_AUTH_USER"])){
		$this_auth = $_SERVER["PHP_AUTH_USER"]. ":". $_SERVER["PHP_AUTH_PW"]. "@";
	} else {
		$this_auth = "";
	}
	if (isset($_SERVER['HTTPS'])){
		if ($_SERVER['HTTPS']  == "on"){
			$this_site = "https://" . $this_auth. $_SERVER["HTTP_HOST"];
		} else {
			$this_site = "http://" . $this_auth. $_SERVER["HTTP_HOST"];
		}
	} else {
		$this_site = "http://" . $this_auth. $_SERVER["HTTP_HOST"];
	}
	// Let's fix the REQUEST_URI bug
	if (!isset($_SERVER['REQUEST_URI']) and isset($_SERVER['SCRIPT_NAME'])){
		$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
		if (isset($_SERVER['QUERY_STRING']) and !empty($_SERVER['QUERY_STRING']))
			$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
	}
	

	// This would probably be better defined somewhere else
 	$scrobble_server = "post.audioscrobbler.com";
	$scrobble_client_id = "jza";
 	$scrobble_plugin_version = "0.1";
?>
