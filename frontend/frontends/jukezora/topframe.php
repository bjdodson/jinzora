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
	* - Displays the top frame of the slim Jukezora
	*
	* @since 02.17.04 
	* @author Ross Carlson <ross@jinzora.org>
	* @author Ben Dodson <ben@jinzora.org>
	*/
	
	// First let's include the settings for Slick
	@include_once("settings.php");

	// Now let's create our blocks
	$blocks = new jzBlocks();
	$display = new jzDisplay();
	
	// Now let's start displaying stuff
	$display->preheader(false,false);
	
	// Now we have to manually add the right javascript - the hover javascript breaks our pretty progress bar
	global $root_dir;
	echo '<script type="text/javascript" src="'. $root_dir. '/lib/jinzora.js"></script>';
	usleep(100000);
	$blocks->jukeboxBlock();
	 
?>