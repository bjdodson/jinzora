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
 * This is the media backend for the database adaptor.
 *
 * @since 05.10.04
 * @author Ben Dodson <bdodson@seas.upenn.edu>
 */


function jz_db_connect() {
  global $sql_db;

  return sqlite_open($sql_db);
}


function jz_db_drop() {
  global $sql_db;

  @unlink($sql_db);
  return;
}


function jz_db_create() {
  global $sql_db;
  
	$link = sqlite_open($sql_db);
    sqlite_close($link);
    return true;
}


function jz_db_query($link, $sql) {
  global $sql_type, $sql_pw, $sql_socket, $sql_db, $sql_usr;

	$results = @sqlite_query($link,$sql);
      if (!$results) { return false; }
      $res = sqlite_fetch_all($results,SQLITE_BOTH);
      $ret = &new sqlTable();
      $ret->data = $res;
      $ret->rows = sizeof($res);
      return $ret;
}


function jz_db_error($link) {
	return sqlite_last_error($link);
}


function jz_db_close($link) {
	// Hack by Ross to fix things for Postnuke
  return;
  
	return sqlite_close($link);
}


function jz_db_escape($string) {
	return sqlite_escape_string($string);
}


function jz_db_unescape($string) {
    return $string;
}


function jz_db_rand_function() {
	return "random()";
}

function jz_db_simple_query($sql) {
	global $JZLINK;
	if (!isset ($JZLINK)) {
		if (!$JZLINK = jz_db_connect())
			die("could not connect to database.");
	}
	$results = sqlite_query($JZLINK,$sql);
	$res = @sqlite_fetch_array($results, SQLITE_BOTH);
	return $res;
}

?>
