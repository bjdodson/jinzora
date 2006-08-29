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
	$jzSERVICE_INFO['name'] = "Geeklog";
	$jzSERVICE_INFO['url'] = "http://www.geeklog.net";

	
	define('SERVICE_CMS_geeklog','true');
	
	

        /*
         * Function to open the CMS
         * 
         * @author Ross Carlson, Ben Dodson
         * @version 6/3/05
         * @since 6/3/05
         **/
         function SERVICE_CMSOPEN_geeklog($authenticate_only) {
    		global $_USER;
    		
    		// Let's get this users username
    		$username = $_USER['username'];
    		if ($username == ""){
    			$username = "anonymous";
    		}		

    		// Ok, now let's authenticate this user
    		userAuthenticate($username);
			
			// Now let's see if we only wanted the user access
		if ($authenticate_only == true){ return; }
		
		echo COM_siteHeader();
         }

        /*
         * Function to close the CMS
         * 
         * @author Ross Carlson, Ben Dodson
         * @version 6/3/05
         * @since 6/3/05
         **/
         function SERVICE_CMSCLOSE_geeklog() {
		    echo "</table>";
         	echo COM_siteFooter(); 
         }

        /*
         * Function to get the CSS / set up the styling.
         * 
         * @author Ross Carlson, Ben Dodson
         * @version 6/25/05
         * @since 6/25/05
         **/
         function SERVICE_CMSCSS_geeklog() {
		    global $include_path,$bgcolor1,$bgcolor2,$bgcolor3,$bgcolor4,$thename,
		    	   $css, $row_colors, $jz_MenuItem, $jz_MenuItemHover, $jz_MenuItemLeft, $jz_MainItemHover, $jz_MenuSplit;
		
		    $bgcolor2 = $bgcolor4;
		
		    echo "<style type=\"text/css\">" .
		    ".jz_row1 { background-color:$bgcolor1; }".
		    ".jz_row2 { background-color:$bgcolor2; }".
		    ".and_head1 { background-color:$bgcolor2; }".
		    ".and_head2 { background-color:$bgcolor1; }".
		    "</style>";
		
		// Now let's set the style sheet for CMS stuff
		$_SESSION['cms-style'] = "themes/". $thename. "/style/styleNN.css";
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
		function SERVICE_CMSGETVARS_geeklog() {
		$a = array();
    	return $a;
	}
	
   /*
	* Returns the default database name.
	* 
	* @author Ben Dodson
	* @version 6/26/06
	* @since 6/26/06
	**/
	function SERVICE_CMSDEFAULTDB_geeklog() {
		return "geeklog";
	}
?>
