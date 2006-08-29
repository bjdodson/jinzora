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
$jzSERVICE_INFO['name'] = "e107";
$jzSERVICE_INFO['url'] = "http://www.e107.org";

/* 
 * Placeholder function
 *
 * @author Ben Dodson
 * 
 **/
function SERVICE_CMS_e107() {
  return;
}

/*
 * Function to open the CMS
 * 
 * @author Ben Dodson
 * @version 7/2/06
 * @since 7/2/06
 **/
function SERVICE_CMSOPEN_e107($authenticate_only) {
  global $sql,$sql2,$aj,$sysprefs,$eTraffic,$tp,$HEADER,$USERNAME;
  
  if (defined('USERNAME')) {
    $user = USERNAME;
  } else {
    $user = "anonymous";
  }
  userAuthenticate($user);
  if ($authenticate_only == true){ return; }

  include_once(HEADERF);
}

/*
 * Function to close the CMS
 * 
 * @author Ben Dodson
 * @version 7/2/06
 * @since 7/2/06
 **/
function SERVICE_CMSCLOSE_e107() {
  global $sql,$sql2,$aj,$sysprefs,$eTraffic,$tp,$FOOTER;
  
  //  include_once(FOOTERF);

}

/*
 * Function to get the CSS / set up the styling.
 * 
 * @author Ross Carlson, Ben Dodson
 * @version 6/25/05
 * @since 6/25/05
 **/
function SERVICE_CMSCSS_e107() {
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
function SERVICE_CMSGETVARS_e107() {
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
function SERVICE_CMSDEFAULTDB_e107() {
  return "e107";
}
?>
