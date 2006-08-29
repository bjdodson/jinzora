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
	* - This page directs 'traffic' to the proper Jinzora component.
	*
	* @since 01.11.05
	* @author Ross Carlson <ross@jinzora.org>
	* @author Ben Dodson <ben@jinzora.org>
	*/

	session_name('jinzora-session');
	session_start();
	
	// Override to use index.php as web-prefix. You may need to change this.
	//define("JZ_URL_OVERRIDE",'/jinzora/index.php');
	
	// This page is intended to be a one-include file to create a full Jinzora backend.
	if (!isset($include_path)) {
		$include_path = "";
	}
	
	@include($include_path."settings.php");
	require_once($include_path."system.php");
	@include($include_path."settings.php");
	
	require_once($include_path."backend/backend.php");
	
	// We'll need to use some general functions, like urlize:
	require_once($include_path."lib/general.lib.php");
	
	$this_page = setThisPage();
	
	// And playlists:
	require_once($include_path."playlists/class.php");
	// We need services for the URL stuff:
	require_once($include_path."services/class.php");
	$jzSERVICES = new jzServices();
	$jzSERVICES->loadStandardServices();
	// Make a fake user, just in case:
	if (defined('JZ_NO_USER')) {
	  $jzUSER = new jzUser(false);
	} else {
	  $jzUSER = new jzUser();

	}
	$jzSERVICES->loadUserServices();	
	handleUserInit();
	include_once($include_path. "frontend/icons.lib.php");
	include_once($include_path. "frontend/frontends/${my_frontend}/header.php");
	
	// Let's build a display class:
	require_once($include_path."frontend/display.php");
	
	$display = new jzDisplay(); // This is a bundle of useful display functions.
	$blocks = new jzBlocks(); // Another useful bundle of functions.

$css = $jzSERVICES->cmsCSS();
$define_only = true;
include($css);
unset($define_only);
?>
