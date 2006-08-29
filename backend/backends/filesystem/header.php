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
	* This is the header for the database adaptor.
	*
	* @since 05.10.04
	* @author Ross Carlson <ross@jinzora.org>
	*/

	// include the primitive cache-based backend.
	// We will inherit the classes from here.
	include_once($include_path. 'backend/primitives/cache/header.php');

	// Now create our database adaptor from these components:
	include_once($include_path. 'backend/backends/filesystem/backend.php');
	include_once($include_path. 'backend/backends/filesystem/media.php');
	include_once($include_path. 'backend/backends/filesystem/users.php');

?>
