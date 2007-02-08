<?php define('JZ_SECURE_ACCESS','true');
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *        
	* JINZORA | Web-based Media Streamer   
	*
	* Jinzora is a Web-based media streamer, primarily desgined to stream MP3s 
	* (but can be used for any media file that can stream from HTTP). 
	* Jinzora can be integrated into a CMS site, run as a standalone application, 
	* or integrated into any PHP website.  It is released under the GNU GPL.
	* 
	* Jinzora Author:
	* Ross Carlson: ross@jasbone.com 
	* http://www.jinzora.org
	* Documentation: http://www.jinzora.org/docs	
	* Support: http://www.jinzora.org/forum
	* Downloads: http://www.jinzora.org/downloads
	* License: GNU GPL <http://www.gnu.org/copyleft/gpl.html>
	* 
	* Contributors:
	* Please see http://www.jinzora.org/modules.php?op=modload&name=jz_whois&file=index
	*
	* Code Purpose: This page contains all the album display related functions
	* Created: 9.24.03 by Ross Carlson
	*
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	$include_path = '';
	$var = explode("/",$_SERVER['PHP_SELF']);
	unset($var[sizeof($var)-1]);
	$web_path = implode("/",$var) . "/";

	require_once('lib/general.lib.php');
	//writeLogData("messages","AJAXRequest: Starting up");
	require_once('jzBackend.php');
	require_once('lib/Sajax.php');
	
	if ($jukebox == "true") {
		include_once("jukebox/ajax.php");
	}
	include_once('frontend/ajax.php');
	@include_once("frontend/frontends/${my_frontend}/ajax.php");
	
	//writeLogData("messages","AJAXRequest: Creating jzUSER object");
	$jzUSER = new jzUser();
	
	for ($i = 0; $i < sizeof($ajax_list); $i++) {
		sajax_export($ajax_list[$i]);
	}
	
	sajax_handle_client_request();
	exit;
?>