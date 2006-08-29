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
 * Please see http://www.jinzora.org/modules.php?op=modload&name=jz_whois&file=index
 * 
 * - Code Purpose -
 * This page binds the backend to the frontend.
 *
 * @since 10/30/04
 * @author Ross Carlson <ross@jinzora.org>, Ben Dodson <bdodson@seas.upenn.edu>
 */

include_once($include_path. 'playlists/class.php');

// library functions here.

function getListType($id) {
  $pre = substr($id,0,2);
  switch ($pre) {
  case "PL":
    return "static";
  case "DY":
    return "dynamic";
  default:
    return false;
  }
}

function getDynamicFunctions() {
  $f = array();
  $f['random'] = word('Randomly Selected');
  $f['topplayed'] = word('Most Played');
  $f['recentlyadded'] = word('Recently Added');
  $f['similar'] = word('Similar');
  //$f['exact'] = word('All Tracks'); // Available, but dont add to dropdown
  
  return $f;
}

?>
