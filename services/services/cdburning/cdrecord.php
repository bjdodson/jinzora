<?php
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
	* - Burns a CD using the CDRecord app
	*
	* @since 06.20.05
	* @author Ross Carlson <ross@jinzora.org>
	* @author Ben Dodson <ben@jinzora.org>
	*/
	
	define('SERVICE_CDBURNING_cdrecord','true');
	
	/**
	* Burns a CD from a list of tracks (MUST be WAV files)
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 06.20.05
	* @since 06.20.05
	* @param $trackArray An array of the tracks to burn
	*/
	function SERVICE_BURN_TRACS($node, $artist, $album){
		global $include_path;
		
		// Let's setup our CUE file
		$data =  'PERFORMER "'. $artist. '"'. "\r\n";
		$data .= 'TITLE "'. $album. '"'. "\r\n";
		
		// now let's add the tracks
		$ctr=1;
		$tracks = $node->getSubNodes("tracks",-1);
		foreach ($tracks as $track){			
			$path =  $track->getDataPath();
			
			// Now let's fix up the paths for Windows
			if (stristr($_ENV['OS'],"win")){
				$path = str_replace("/","\\",$path);
			}
			
						
			$data .= 'FILE "'. $path. '" WAVE'. "\r\n";
			$data .= '  TRACK '. $ctr. ' AUDIO'. "\r\n";
			$data .= '    PERFORMER "'. $artist. '"'. "\r\n";
			$data .= '    TITLE "'. $track->getName(). '"'. "\r\n";
	
			$ctr++;
		}
		
		// Now let's create the file
		$fileName = $include_path. "temp/burnlist.cue";
		$handle = fopen($fileName, "w");
		fwrite($handle,$data);				
		fclose($handle);		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
?>