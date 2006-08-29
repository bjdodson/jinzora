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
  global $sql_pw, $sql_socket, $sql_db, $sql_usr;

  $connect_string = "host=".$sql_socket." dbname=".$sql_db." user=".$sql_usr." password=".$sql_pw;
  $link = pg_connect($connect_string);
  return $link;
}


function jz_db_drop() {
  global $sql_type, $sql_pw, $sql_socket, $sql_db, $sql_usr;

  $connect_string = "host=".$sql_socket." user=".$sql_usr." password=".$sql_pw;
  $link = pg_connect($connect_string);
  if (!$link) return false;
  return pg_query("DROP DATABASE $sql_db");
}


function jz_db_create() {
	global $sql_socket,$sql_pw,$sql_usr,$sql_db;
    
    return false;
    /*
    $connect_string = "host=".$sql_socket." user=".$sql_usr." password=".$sql_pw;
    $link = pg_connect($connect_string);
    if (!$link) return false;
    return pg_query("CREATE DATABASE $sql_db");
    */
}


function jz_db_query($link, $sql) {

  $results = @pg_query($link,$sql);
  if (!$results) { return false; }
  $res = &new sqlTable();
  $len = pg_num_rows($results);
  for ($i = 0; $i < $len; $i++) {
	$row = pg_fetch_array($results,$i,PGSQL_BOTH);
	$res->add($row);
  }
  return $res;
}


function jz_db_error($link) {
	return pg_last_error($link);
}


function jz_db_close($link) {
  // Hack by Ross to fix things for Postnuke
  return;
  
  return pg_close($link);
}


function jz_db_escape($string) {
	return pg_escape_string($string);
}


function jz_db_unescape($string) {
    return stripSlashes($string);
}


function jz_db_rand_function() {
    return "random()";
}

?>
