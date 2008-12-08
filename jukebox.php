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
	* - This page handles the jukebox display and controls
	*
	* @since 01.11.05
	* @author Ross Carlson <ross@jinzora.org>
	* @author Ben Dodson <ben@jinzora.org>
	* - Todo -
	* Implement detach window:
	* - use frameElement to determine whether stand-alone or iframe
	* - find a way to determine if detached window is still onscreen
	*   THAT is the real hard part as there is no knowledge of all open windows in Javascript
	*   SO we must use a variable, but it is erased each time we reload the iframe ...
	* - do not display anything in the frame is detached window is still on screen
	* - disable timer on height=0 if we are detached window still on screen
	* - attach the redock function to body.onunload event w/ knowledge of closed in Mozilla
	*
	* @since 07/04/04
	* @author Ross Carlson <ross@jinzora.org>
	*/

	// Let's setup the classes
	$blocks = new jzBlocks();
	$display = new jzDisplay();
	
	// Now let's start displaying stuff
	$display->preheader(false,false,"left",true,true,false);
	
	// Now we have to manually add the right javascript - the hover javascript breaks our pretty progress bar
	echo '<script type="text/javascript" src="'. $root_dir. '/lib/jinzora.js"></script>';
	
	// Now let's include the jukebox settings
	$blocks->jukeboxBlock($node);
?>