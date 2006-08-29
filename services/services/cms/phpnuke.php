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
	* - This is the leo's lyrics service.
	*
	* @since 01.14.05
	* @author Ross Carlson <ross@jinzora.org>
	* @author Ben Dodson <ben@jinzora.org>
	*/
	
	$jzSERVICE_INFO = array();
	$jzSERVICE_INFO['name'] = "PHP-Nuke";
	$jzSERVICE_INFO['url'] = "http://www.phpnuke.org";
	
	define('SERVICE_CMS_phpnuke','true');
	
	
	
	/*
	* Function to open the CMS
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 6/3/05
	* @since 6/3/05
	**/
	function SERVICE_CMSOPEN_phpnuke($authenticate_only) {
		global $this_site, $web_root, $path_to_zip, 
		$root_dir, $media_dir, $audio_types, $video_types, 
		$ext_graphic, $cms_user_access, $default_access,
		$cms_mode, $css, $include_path, $image_dir;
		
		
		// Now let's get the users name IF we need it
		$cookie = cookiedecode($_COOKIE['user']);
		$username = $cookie[1];
		if ($username == ""){
			$username = "anonymous";
		}
	
		userAuthenticate($username);
		
		// Now let's see if we only wanted the user access
		if ($authenticate_only == true){ return; }
		
		include_once("header.php");
	}
	
	/*
	* Function to close the CMS
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 6/3/05
	* @since 6/3/05
	**/
	function SERVICE_CMSCLOSE_phpnuke() {
		CloseTable();
		include_once("footer.php");		
	}
	
	/*
	* Function to get the CSS / set up the styling.
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 6/25/05
	* @since 6/25/05
	**/
	function SERVICE_CMSCSS_phpnuke() {
		global  $include_path,$bgcolor1,$bgcolor2,$bgcolor3,$bgcolor4,$thename,
				$css, $row_colors, $jz_MenuItem, $jz_MenuItemHover, $jz_MenuItemLeft, $jz_MainItemHover, $jz_MenuSplit;
		
		echo "<style type=\"text/css\">" .
			".jz_row1 { background-color:$bgcolor1; }".
			".jz_row2 { background-color:$bgcolor2; }".
			".and_head1 { background-color:$bgcolor2; }".
			".and_head2 { background-color:$bgcolor1; }".
			"</style>";
		OpenTable();
		
		// Now let's set the style sheet for CMS stuff
		$thename = @get_theme();	
		$_SESSION['cms-style'] = "themes/". $thename. "/style/style.css";
		$_SESSION['cms-theme-data'] = urlencode($bgcolor1. "|". $bgcolor2); 
		
		$row_colors = array('jz_row2','jz_row1');
		$jz_MenuItemHover = "jz_row2";
		$jz_MenuItem = "jz_row1";		
		$jz_MenuItemLeft = "jzMenuItemLeft";
		$jz_MenuSplit = "jzMenuSplit";
		$jz_MainItemHover = "jzMainItemHover";
			
			
		// Now let's set the CSS
		$css = $include_path . "style/cms-theme/default.php";
		return $css;	
	}
	
	
	/*
	* Returns the GET vars for the CMS.
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 6/3/05
	* @since 6/3/05
	**/
	function SERVICE_CMSGETVARS_phpnuke() {
		$a = array();
		$a['name'] = $_GET['name'];
		
		return $a;
	}
	
   /*
	* Returns the default database name.
	* 
	* @author Ben Dodson
	* @version 6/26/06
	* @since 6/26/06
	**/
	function SERVICE_CMSDEFAULTDB_phpnuke() {
		return "phpnuke";
	}
?>