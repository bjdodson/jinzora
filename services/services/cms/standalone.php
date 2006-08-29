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
	$jzSERVICE_INFO['name'] = "Jinzora";
	$jzSERVICE_INFO['url'] = "http://www.jinzora.org";

	define('SERVICE_CMS_standalone','true');
	

	/*
	 * Function to open the CMS
	 * 
	 * @author Ross Carlson, Ben Dodson
	 * @version 6/3/05
	 * @since 6/3/05
	 **/
	 function SERVICE_CMSOPEN_standalone($authenticate_only) {
		 	global $embedded_header,$css;
			
			// Are we embedding?
			if ($embedded_header <> ""){
				// Let's include their header
				include_once($embedded_header);
			}
	 }

	/*
	 * Function to close the CMS
	 * 
	 * @author Ross Carlson, Ben Dodson
	 * @version 6/3/05
	 * @since 6/3/05
	 **/
	 function SERVICE_CMSCLOSE_standalone() {
		 	global $embedded_footer;

		 	if ($embedded_footer <> ""){
				// Let's include their header
				include_once($embedded_footer);
			}
	 }

		/*
		 * Function to get the CSS / set up the styling.
		 * 
		 * @author Ross Carlson, Ben Dodson
		 * @version 6/25/05
		 * @since 6/25/05
		 **/
		 function SERVICE_CMSCSS_standalone() {
		 	global $css, $row_colors, $jz_MenuItem, $jz_MenuItemHover, $jz_MenuItemLeft, $jz_MainItemHover, $jz_MenuSplit;
		 	
		 	$row_colors = array('jz_row1','jz_row2');
		 	$jz_MenuItem = "jzMenuItem";
		 	$jz_MenuItemHover = "jzMenuItemHover";
		 	$jz_MenuItemLeft = "jzMenuItemLeft";
		 	$jz_MainItemHover = "jzMainItemHover";			
			$jz_MenuSplit = "jzMenuSplit";
					 	
		 	return $css;
	 }
         
         
		/*
		 * Returns the GET vars for the CMS.
		 * 
		 * @author Ross Carlson, Ben Dodson
		 * @version 6/3/05
		 * @since 6/3/05
		 **/
		 function SERVICE_CMSGETVARS_standalone() {
			 return array();
		 }
		 
   /*
	* Returns the default database name.
	* 
	* @author Ben Dodson
	* @version 6/26/06
	* @since 6/26/06
	**/
	function SERVICE_CMSDEFAULTDB_standalone() {
		return "jinzora2";
	}
?>
