<?php 
	define('JZ_SECURE_ACCESS','true');
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
	* - This page handles all API requests for add-on services
	*
	* @since 04.14.05
	* @author Ross Carlson <ross@jinzora.org>
	* @author Ben Dodson <ben@jinzora.org>
	*/
	// Let's set the error reporting level
	//@error_reporting(E_ERROR);
	
	// Let's include ALL the functions we'll need
	// Now we'll need to figure out the path stuff for our includes
	// This is critical for CMS modes
	$include_path = ""; $link_root = ""; $cms_type = ""; $cms_mode = "false";
    $backend = ""; $jz_lang_file = ""; $skin = ""; $my_frontend = "";
	
	include_once('system.php');		
	include_once('settings.php');	
	include_once('backend/backend.php');	
	include_once('playlists/playlists.php');
	include_once('lib/general.lib.php');
	include_once('lib/jzcomp.lib.php');
	include_once('services/class.php');
	
	$skin = "slick";
	$image_dir = $root_dir. "/style/$skin/";
	include_once('frontend/display.php');
	include_once('frontend/blocks.php');
	include_once('frontend/icons.lib.php');
	include_once('frontend/frontends/slick/blocks.php');
	include_once('frontend/frontends/slick/settings.php');
	
	
	$this_page = setThisPage();
	$enable_page_caching = "false";
	
	
	// Let's create our user object for later
	$jzUSER = new jzUser();
	
	// Let's make sure this user has the right permissions
	if ($jzUSER->getSetting("view") === false || isset($_GET['user'])) {
		if (isset($_GET['user'])) {
			$store_cookie = true;
			// Are they ok?
			if ($jzUSER->login($_GET['user'],$_GET['pass'],$store_cookie, false) === false) {
				echoXMLHeader();
				echo "<login>false</login>";
				echoXMLFooter();
				exit();
			}
		} else {
			// Nope, error...
			echoXMLHeader();
			echo "<login>false</login>";
			echoXMLFooter();
			exit();
		}
	}
	
	// Let's create our services object
	// This object lets us do things like get metadata, resize images, get lyrics, etc
	$jzSERVICES = new jzServices();
	$jzSERVICES->loadStandardServices();
	
	// if isset GET['page']
	// $func = 'jzApi_' . GET['page']
	// else
	$args = array();
	$func = 'jzApi_main';
	
	print_r($func($args));
	
	
	// make playlists work; handle login
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/* Ex: 
	 * 
	 * Playlists
	 *   Sleepy
	 *   Party
	 * Genres
	 *   Alternative
	 *   Rock
	 *   Punk
	 *   Soft
	 * Random
	 *   New Album
	 *   Played Album
	 *   Random Album
	 */
	function jzApi_main($argv) {
		$ret = array();
		
		$ret[] = E("Playlists", null, "playlists");
		$ret[] = E("Charts & Random", null, "random");
		$ret[] = E("Genres", null, "nodes");
		
		return $ret;
	}
	
	
	function jzApi_playlists($argv) {
		
	}
	
	function jzApi_random($argv) {
		
	}
	
	function jzApi_nodes($argv) {
		$ret = array();
		if (isset($argv["id"])) {
			$root = new jzMediaNode($argv["id"], "id");
		} else {
			$root = new jzMediaNode();
		}
		
		foreach ($root->getSubNodes("both") as $node) {
			if ($node instanceof jzMediaNode) {
				$ret[] = E($node->getName(),$node->getPlayLink(),"nodes",array("id" => $node->getID()));
			} else {
				$ret[] = E($node->getName(),$node->getPlayLink());
			}
		}
		
		return $ret;
	}
	
	
	function E($display_name, $playlink, $method = null, $args = null) {
		return array($display_name,$playlink,$method,$args);
	}
?>