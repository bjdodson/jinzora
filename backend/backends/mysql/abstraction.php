<?php
if (!defined(JZ_SECURE_ACCESS))
	die('Security breach detected.');
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

	$link = @ mysql_connect($sql_socket, $sql_usr, $sql_pw);
	if (!$link)
		return false;
	if (!@ mysql_select_db($sql_db, $link))
		return false;

	return $link;
}

function jz_db_drop() {
	global $sql_pw, $sql_socket, $sql_db, $sql_usr;

	$link = @ mysql_connect($sql_socket, $sql_usr, $sql_pw);
	if (!$link)
		return false;
	@ mysql_query("DROP DATABASE $sql_db");
	return;
}

function jz_db_create() {
	global $sql_pw, $sql_socket, $sql_db, $sql_usr;

	$link = @ mysql_connect($sql_socket, $sql_usr, $sql_pw);
	if (!$link)
		return false;
	return mysql_query("CREATE DATABASE $sql_db");
}

function jz_db_query($link, $sql) {
	global $sql_pw, $sql_socket, $sql_db, $sql_usr;
	
	if (false !== ($res = jz_db_cache('query',$sql))) {
		return $res;
	}
	
	$results = @ mysql_query($sql, $link);
	if (!$results) {
		return false;
	}
	$res = & new sqlTable();
	while ($row = @ mysql_fetch_array($results, MYSQL_BOTH)) {
		$res->add($row);
	}
	
	jz_db_cache('query',$sql,$res);
	return $res;
}

function jz_db_error($link) {
	return mysql_error($link);
}

function jz_db_close($link) {
	// Hack by Ross to fix things for Postnuke
	return;

	return mysql_close($link);
}

function jz_db_escape($string) {
	return mysql_escape_string($string);
}

function jz_db_unescape($string) {
	return stripSlashes($string);
}

function jz_db_rand_function() {
	return "rand()";
}



function jz_db_simple_query($sql) {
	global $JZLINK;
	
	if (false !== ($res = jz_db_cache('simple',$sql))) {
		return $res;
	}
	
	if (!isset ($JZLINK)) {
		if (!$JZLINK = jz_db_connect())
			die("could not connect to database.");
	}
	$results = mysql_query($sql, $JZLINK);
	$res = @mysql_fetch_array($results, MYSQL_BOTH);
	
	jz_db_cache('simple',$sql,$res);
	return $res;
}
?>
