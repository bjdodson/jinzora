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
$jzSERVICE_INFO['name'] = "XooPS";
$jzSERVICE_INFO['url'] = "http://www.xoops.org";

define('SERVICE_CMS_xoops','true');



/*
 * Function to open the CMS
 * 
 * @author Ben Dodson
 * @version 7/2/06
 * @since 7/2/06
 **/
function SERVICE_CMSOPEN_xoops($authenticate_only) {
  global $xoopsUser,$xoopsOption;
  // Let's get this users username
  
  if (is_object($xoopsUser)) {
    $username = $xoopsUser->getVar('uname');
  } else {
    $username = "anonymous";
  }	

  // Ok, now let's authenticate this user
  userAuthenticate($username);
  
  // Now let's see if we only wanted the user access
  if ($authenticate_only == true){ return; }
   
  ob_start();
}

/*
 * Function to close the CMS
 * 
 * @author Ben Dodson
 * @version 7/2/06
 * @since 7/2/06
 **/
function SERVICE_CMSCLOSE_xoops() {
  global $xoopsOption;


  $content = ob_get_contents();
  ob_end_clean();
  
  include(XOOPS_ROOT_PATH.'/header.php');
  echo $content;
  include(XOOPS_ROOT_PATH.'/footer.php');
}

/*
 * Function to get the CSS / set up the styling.
 * 
 * @author Ross Carlson, Ben Dodson
 * @version 6/25/05
 * @since 6/25/05
 **/
function SERVICE_CMSCSS_xoops() {
   global $include_path,$bgcolor1,$bgcolor2,$bgcolor3,$bgcolor4,$thename,
    $css, $row_colors, $jz_MenuItem, $jz_MenuItemHover, $jz_MenuItemLeft, $jz_MainItemHover, $jz_MenuSplit;
  
  $row_colors = array('even','odd');
  $jz_MenuItemHover = "odd";
  $jz_MenuItem = "even";            
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
function SERVICE_CMSGETVARS_xoops() {
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
function SERVICE_CMSDEFAULTDB_xoops() {
  return "xoops";
}
?>
