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
  
  switch ($sql_type) {
  case "DBX_MSSQL":
    $sqlt = DBX_MSSQL;
    break;
  case "DBX_ODBC":
    $sqlt = DBX_ODBC;
    break;
  case "DBX_FBSQL":
    $sqlt = DBX_FBSQL;
    break;
  case "DBX_SYBASECT":
    $sqlt = DBX_SYBASECT;
    break;
  case "DBX_OCI8":
    $sqlt = DBX_OCI8;
    break;
  case "DBX_SQLITE":
    $sqlt = DBX_SQLITE;
    break;
  }
  return dbx_connect($sqlt, $sql_socket, $sql_db, $sql_usr, $sql_pw);
}


function jz_db_drop() {
  return;
}


function jz_db_create() {
  return false;
}


function jz_db_query($link, $sql) {
  return @dbx_query($link, $sql);  
}


function jz_db_error($link) {
	return dbx_error($link);
}

function jz_db_close($link) {
	// Hack by Ross to fix things for Postnuke
	return;
	
	return @dbx_close($link);
}


function jz_db_escape($string) {
  global $sql_type;
  switch ($sql_type) {
  case "DBX_MYSQL":
  	return mysql_escape_string($string);
  	break;
  case "DBX_PGSQL":
  	return pg_escape_string($string);
  	break;
  case "DBX_SQLITE":
  	return sqlite_escape_string($string);
  	break;
  case "DBX_MSSQL":
    return str_replace("'","''",$string);
    break;
  default:
    return addSlashes($string);
    break;
  }
}

function jz_db_unescape($string) {
  global $sql_type;
  switch ($sql_type) {
  case "DBX_SQLITE":
    return $string;
    break;
  case "DBX_MSSQL":
  	return str_replace("''", "'", $string);
  	break;
  default:
    return stripSlashes($string);
    break;
  }
}

function jz_db_rand_function() {
  global $sql_type;
  switch ($sql_type) {
  case "DBX_MSSQL":
  	return "newid()";
  	break;
  case "DBX_PGSQL":
  case "DBX_SQLITE":
    return "random()";
    break;
  default:
    return "rand()";
  }
}

?>
