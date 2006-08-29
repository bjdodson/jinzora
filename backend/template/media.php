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
	* @author Ross Carlson <ross@jinzora.org>
	*/
	// Most classes should be included from header.php
	
	
	// The music root is $media_dir.
	class jzMediaNode extends jzMediaNodeClass {

		/**
		* Constructor wrapper for jzMediaNode.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 5/14/04
		* @since 5/13/04
		*/
		function jzMediaNode($par = array()) {
			$this->_constructor($par);
		}


		/**
		* Updates the cache using $this as the base node.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 5/13/04
		* @since 5/13/04
		*/
		function updateCache() {
		}
		
		
		
		/**
		* Counts the number of subnodes $distance steps down.
		* $distance = -1 does a recursive count.
		*
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 5/14/2004
		* @since 5/14/2004
		*/
		function getSubNodeCount($type='both', $distance=false) {
			if ($distance === false) {
				$distance = $this->getNaturalDepth();	
			}
		}



		/**
		* Returns the subnodes as an array.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 5/15/2004
		* @since 5/14/2004
		*/
		function getSubNodes($type='nodes', $distance=false, $random=false, $limit=0) {
			if ($distance === false) {
				$distance = $this->getNaturalDepth();	
			}
		}
	

		/**
		* Alphabetical listing of a node.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 5/14/2004
		* @since 5/14/2004
		*/	
		function getAlphabetical($letter, $distance = false) {
			if ($distance === false) {
				$distance = $this->getNaturalDepth();	
			}
		}
	
	
		// begin global_include: overrides.php
	
		// end global_include: overrides.php
		
	}

	class jzMediaTrack extends jzMediaTrackClass {
	
		/**
		* Constructor wrapper for jzMediaTrack.
		* 
		* @author 
		* @version 
		* @since 
		*/
		function jzMediaTrack($par = array()) {
			$this->_constructor($par);
		}
		
		// begin global_include: overrides.php
		
		// end global_include: overrides.php
	}
?>
