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
	* This is the media backend for the default XML cache adaptor.
	*
	* @since 05.10.04
	* @author Ross Carlson <ross@jinzora.org>
	*/
	
	// all the work is done in classes.php.
	class jzBackend extends jzRawBackend { 
		function jzBackend() {
			// Set variables from parent class.
			$this->_constructor();
			// Overrrides here
			$this->details = "This is a cache-based backend that uses your filesystem hierarchy to determine the music layout.";
			$this->details .= " When music is added, it will show up directly in Jinzora, however it will not show up when";
			$this->details .= " using advanced features like searching and random playlist generation.";
		}
	}
?>
