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
	* This updates code in order to fake dual inheritance.
	* It is used to override functions of the class jzMediaElement
	* for a specific adaptor.
	*
	* @since 05.12.04
	* @author Ben Dodson <bdodson@seas.upenn.edu>
	*/
	//global_include("backends/filesystem/media.php", "backend/filesystem/overrides.php", "overrides.php");
	global_include("primitives/database/media.php", "primitives/database/overrides.php", "overrides.php");

	function global_include($file, $include, $marker) {
	
		// get the contents to include in a variable.
		if (!$inc = file_get_contents($include)) die ("Could not open '$include'");

		// insert it into the file.		
		if (!$lines = file($file)) die("Could not open '$file' for reading.");
	
		$result = "";
	
		for ($i = 0; $i < sizeof($lines); $i++) {
			if (preg_match("(\/\/ begin global_include: $marker)", $lines[$i])) {
				$result .= $lines[$i];
				$result .= $inc;
				while (!(preg_match("(\/\/ end global_include: $marker)", $lines[$i]))) {
					if ($i >= sizeof($lines)) die ("error: no end for global_include found.");
					$i++;
				}

				$result .= $lines[$i];
			}
			else {
				$result .= $lines[$i];
			}
		}
	
		// now put the contents back into $file.
		$fw = fopen("$file", "w");
		if (fwrite($fw, $result) === FALSE) {
			die("Could not write to $file.");
		}
		fclose($fw);
	}
?>
