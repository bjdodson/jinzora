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
  global $sql_type, $sql_pw, $sql_socket, $sql_db, $sql_usr;

  $link = mssql_connect ($sql_socket,$sql_usr,$sql_pw);
  if (!$link) return false;
  if(!@mssql_select_db($sql_db, $link)) return false;
      
  return $link;
}


function jz_db_drop() {
  global $sql_type, $sql_pw, $sql_socket, $sql_db, $sql_usr;

  $link = @mssql_connect($sql_socket,$sql_usr,$sql_pw);
  if (!$link) return false;
  @mssql_query("DROP DATABASE $sql_db");

  return;
}


function jz_db_create() {
  global $sql_type, $sql_pw, $sql_socket, $sql_db, $sql_usr;
  
  $link = @mssql_connect($sql_socket,$sql_usr,$sql_pw);
  if (!$link) return false;
  return mssql_query("CREATE DATABASE $sql_db");
}


function jz_db_query($link, $sql) {
  global $sql_type, $sql_pw, $sql_socket, $sql_db, $sql_usr;

  $results = @mssql_query($sql, $link);
  if (!$results) return false;
  $res = &new sqlTable();
  while ($row = @mssql_fetch_array($results, MSSQL_BOTH)) {
  	$res->add($row);
  }
  
  return $res;
}


function jz_db_error($link) {
  return mssql_get_last_message();
}


function jz_db_close($link) {
  global $sql_type;
  
  // Hack by Ross to fix things for Postnuke
  return;

  return mssql_close($link);
}


function jz_db_escape($string) {
	return str_replace("'","''",$string);
}


function jz_db_unescape($string) {
  return str_replace("''", "'", $string);
}


function jz_db_rand_function() {
  return "newid()";
}

?>
