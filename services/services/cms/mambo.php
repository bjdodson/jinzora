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
	$jzSERVICE_INFO['name'] = "Mambo";
	$jzSERVICE_INFO['url'] = "http://www.mamboserver.com";

	define('SERVICE_CMS_mambo','true');
	
	

        /*
         * Function to open the CMS
         * 
         * @author Ross Carlson, Ben Dodson
         * @version 6/3/05
         * @since 6/3/05
         **/
         function SERVICE_CMSOPEN_mambo($authenticate_only) {
    		global $mainframe, $my, $include_path,$thename;
  		
    		defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
    		
    		// Let's get this users username
    		$username = $my->username;
    		if ($username == ""){
    			$username = "anonymous";
    		}		

    		// Ok, now let's authenticate this user
    		userAuthenticate($username);
         }

        /*
         * Function to close the CMS
         * 
         * @author Ross Carlson, Ben Dodson
         * @version 6/3/05
         * @since 6/3/05
         **/
         function SERVICE_CMSCLOSE_mambo() {
         	return; 
         }

        /*
         * Function to get the CSS / set up the styling.
         * 
         * @author Ross Carlson, Ben Dodson
         * @version 6/25/05
         * @since 6/25/05
         **/
         function SERVICE_CMSCSS_mambo() {
			global 	$mosConfig_host, $mosConfig_user, 
					$mosConfig_password, $mosConfig_db, $mosConfig_dbprefix, $include_path, $thename,
					$bgcolor1, $bgcolor2,
					$css, $row_colors, $jz_MenuItem, $jz_MenuItemHover, $jz_MenuItemLeft, $jz_MainItemHover, $jz_MenuSplit;
    				
    		$option = trim( strtolower( mosGetParam( $_REQUEST, 'option' ) ) );
    		$Itemid = intval( mosGetParam( $_REQUEST, 'Itemid', null ) );
    		$database = new database( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix );
    		$mainframe = new mosMainFrame( $database, $option, '.' );
    		$mainframe->initSession();
    		$thename = $mainframe->getTemplate();
	
			// Now let's set the style sheet for CMS stuff
			$_SESSION['cms-style'] = "templates/". $thename. "/css/template_css.css";
			$_SESSION['cms-theme-data'] = urlencode($bgcolor1. "|". $bgcolor2); 
			
			$row_colors = array('sectiontableentry2','tabheading');
			$jz_MenuItemHover = "tabheading";
			$jz_MenuItem = "sectiontableentry2";
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
		function SERVICE_CMSGETVARS_mambo() {
			$a = array();
			
			$a['option'] = $_GET['option'];
			$a['Itemid'] = $_GET['Itemid'];
			return $a;
		}
		
   /*
	* Returns the default database name.
	* 
	* @author Ben Dodson
	* @version 6/26/06
	* @since 6/26/06
	**/
	function SERVICE_CMSDEFAULTDB_mambo() {
		return "mambo";
	}
?>
