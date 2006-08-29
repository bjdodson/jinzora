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
	* Contains the settings for all the different jukeboxes
	*
	* @since 2/9/05
	* @author Ross Carlson <ross@jinzora.org>
	*/
	$jbArr = array();
	
	// To setup a jukebox copy one of the sections below and edit it per your setup
	// Be sure that if you have more than one jukebox that they are numbered in order, starting with 0 (the $jbArr[0])
	// Inset Jukebox settings below this line



	// Inset Jukebox settings above this line

  /*
  // Sample Entries:
  // Winamp 3/5 Jukebox (httpq3)
	$jbArr[0]['server'] = "localhost";
	$jbArr[0]['port'] = "4800";
	$jbArr[0]['password'] = "pass";
	$jbArr[0]['description'] = "Laptop";
	$jbArr[0]['type'] = "winamp3";
	
	// Slimserver Jukebox
	$jbArr[0]['server'] = "localhost";
	$jbArr[0]['port'] = "9090";
	$jbArr[0]['description'] = "SqueezeBox";
	$jbArr[0]['type'] = "slimserver";
	
	// Shoutcast Jukebox
  global $root_dir;
	$jbArr[0]['server'] = "localhost";
	$jbArr[0]['port'] = "8000";
	$jbArr[0]['password'] = "jinzora";
	$jbArr[0]['description'] = "Shoutcast";
	$jbArr[0]['type'] = "shoutcast";
	$jbArr[0]['sc_trans_linux_path'] = $_SERVER['DOCUMENT_ROOT'].$root_dir."/jukebox/jukeboxes/shoutcast/sc_trans_linux"; // THIS IS THE FULL PATH INCLUDING THE EXE
	$jbArr[0]['sc_trans_linux_conf'] = $_SERVER['DOCUMENT_ROOT'].$root_dir."/jukebox/jukeboxes/shoutcast/sc_trans.conf"; // THIS IS THE FULL PATH INCLUDING THE CONF FILE
	
	// Winamp Jukebox
	$jbArr[0]['server'] = "localhost";
	$jbArr[0]['port'] = "4800";
	$jbArr[0]['password'] = "jinzora";
	$jbArr[0]['description'] = "Laptop";
	$jbArr[0]['type'] = "winamp";  // IF YOU ARE USING WINAMP 5.0 AND HTTPQ 3 MAKE SURE THIS IS SET TO winamp3

	// MPD Jukebox/
	$jbArr[0]['server'] = "localhost";
	$jbArr[0]['port'] = "6600";
	$jbArr[0]['description'] = "MPD Jukebox";
	$jbArr[0]['password'] = "";
	$jbArr[0]['type'] = "mpd";

	// Audiotron Jukebox
	$jbArr[0]['server'] = "172.25.0.100";
	$jbArr[0]['username'] = "admin";
	$jbArr[0]['password'] = "admin";
	$jbArr[0]['description'] = "Audiotron";
	$jbArr[0]['type'] = "audiotron";
	$jbArr[0]['mediaserver'] = "JASBONE";
	$jbArr[0]['mediashare'] = "MUSIC";
	$jbArr[0]['localpath'] = "H:\Music";
	$jbArr[0]['playlistname'] = "jinzora.m3u";
	*/
?>
