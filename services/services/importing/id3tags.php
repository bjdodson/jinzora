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
 * - This is google search service.
 *
 * @since 07.27.06
 * @author Ben Dodson <ben@jinzora.org>
 * @author Ross Carlson <ross@jinzora.org>
 */

$jzSERVICE_INFO = array();
$jzSERVICE_INFO['name'] = "Jinzora import by ID3";
$jzSERVICE_INFO['url'] = "http://www.jinzora.org";

define('SERVICE_IMPORTING_id3tags','true');


function SERVICE_IMPORTMEDIA_id3tags($node, $root_path = false, $flags = array()) {
	global $ext_graphic,$default_art,$audio_types,$video_types;
	
	$be = new jzBackend(); 
	$root = new jzMediaNode();
	
	 if ((isset($flags['recursive']) && $flags['recursive'] === false) || $root_path === false) {
	 	/*
	 	if ($node === false) { $node = new jzMediaNode(); }
	 	$tracks = $node->getSubNodes("tracks");
	 	foreach ($tracks as $track) {
	 		$fname = $track->getFileName("host");
	 		$entry = $be->lookupFile($fname);
	 		if (!is_array($entry)) {
	 			echo "Error in SERVICE_IMPORTMEDIA_id3tags. File added but does not exist in registry.";
	 			return;
	 		}
	 		if ($entry['fs_sync'] && !file_exists($fname)) {
	 			$be->unregisterFile($fname);
	 			$root->removeMedia($track);
	 		}
	 		// TODO: CHECK THE ABOVE!!! CHECK UNREGISTERFILE!
	 		// TODO: check modified time and move file if needed.
	 	}
	 	*/
	 	return false;
	 }
	
	 
	 $directory_list = array();
	 $directory_list[] = $root_path;
	 if (isset($flags['showstatus']) && $flags['showstatus'] && !(is_string($flags['showstatus']) && $flags['showstatus'] == "cli")) {
	 	$_SESSION['jz_import_full_progress'] = 0;
	 }
	 
	 
	 while (sizeof($directory_list) > 0) {
	 	$cur_dir = array_shift($directory_list);
	 	if (isset($flags['showstatus']) && is_string($flags['showstatus']) && $flags['showstatus'] == "cli") {
	 		echo word("Scanning: %s", $cur_dir) . "\n";
	 	}
	 	
	 	$bestImage = "";
	 	$albums = array();
	 	
	 	$track_paths = array();
		$track_filenames = array();
		$track_metas = array();
	 	
	 	if (!($handle = opendir($cur_dir))) 
			continue; //die("Could not access directory $dir");
					
			while ($file = readdir($handle)) {
				if ($file == "." || $file == "..") {
					continue;
				}
				
				$fullpath = $cur_dir . "/" . $file;
				
				if (is_dir($fullpath)) {
					$directory_list[] = $fullpath;
				} else  if (preg_match("/\.($ext_graphic)$/i", $file) && !stristr($file,".thumb.")) {
					// An image
					if (@preg_match("/($default_art)/i",$file)) {
						$bestImage = $fullpath;
				  	} else if ($bestImage == "") {
				   		$bestImage = $fullpath;
				  	}
				} else if (preg_match("/\.($audio_types)$/i", $file) || preg_match("/\.($video_types)$/i", $file)) {
					$entry = $be->lookupFile($fullpath);
					
					if (isset($flags['showstatus']) && $flags['showstatus'] && !(is_string($flags['showstatus']) && $flags['showstatus'] == "cli")) {
						if (($_SESSION['jz_import_full_progress'] % 50 == 0) 
							or ($_SESSION['jz_import_full_progress'] == 0)
							or ($_SESSION['jz_import_full_progress'] == 1)){
							showStatus();
						}
	 					$_SESSION['jz_import_full_progress']++;
					}
					
					if (is_array($entry) && $entry['added'] < filemtime($fullpath)) {
						// moved file
						$track = &new jzMediaTrack($fullpath);
						$track->playpath = $fullpath;
						$meta = $track->getMeta("file");
						
						
						
						$arr = array();
						if (isset($meta['genre'])) {
							$arr['genre'] = $meta['genre'];
						}
						if (isset($meta['subgenre'])) {
							$arr['subgenre'] = $meta['subgenre'];
						}
						if (isset($meta['artist'])) {
							$arr['artist'] = $meta['artist'];
						}
						if (isset($meta['album'])) {
							$arr['album'] = $meta['album'];
						}
						if (isset($meta['disk'])) {
							$arr['disk'] = $meta['disk'];
						}
						if (isset($meta['filename'])) {
							$arr['track'] = $meta['filename'];
						}
						
						$old = new jzMediaTrack($entry['path']);
						$child = $root->moveMedia($old, $arr);
						
						if ($child !== false) {
							$album = $child->getAncestor("album");
							if ($album !== false) {
								$albums[$album->getPath("String")] = true;
							}
						}
					}
					else if ($entry === false || (isset($flags['force']) && $flags['force'] === true)) {
						// Set path so when getFileName is called and the filepath was not found,
						// we get the correct path.
						$track = &new jzMediaTrack($fullpath);
						$track->playpath = $fullpath;
						$meta = $track->getMeta("file");
						
						$arr = array();
						if (isset($meta['genre'])) {
							$arr['genre'] = $meta['genre'];
						}
						if (isset($meta['subgenre'])) {
							$arr['subgenre'] = $meta['subgenre'];
						}
						if (isset($meta['artist'])) {
							$arr['artist'] = $meta['artist'];
						}
						if (isset($meta['album'])) {
							$arr['album'] = $meta['album'];
						}
						if (isset($meta['disk'])) {
							$arr['disk'] = $meta['disk'];
						}
						if (isset($meta['filename'])) {
							$arr['track'] = $meta['filename'];
						}
						
						$track_paths[] = $arr;
						$track_filenames[] = $fullpath;
						$track_metas[] = $meta;
						
					}
				}
			} // while reading dir
			
			$art_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR;
			
			if (sizeof($track_paths) > 0) {
				$results = $root->bulkInject($track_paths,$track_filenames,$track_metas);
				for ($i = 0; $i < sizeof($results); $i++) {
					if ($results[$i] !== false) {
						$album = $results[$i]->getAncestor("album");
						if ($album !== false) {
							$albums[$album->getPath("String")] = true;
							$newalbum = new jzMediaNode($album->getPath("String"));
							// If we have album art in the tag, add it.
							if ($track_metas[$i]['pic_data'] <> ""){
								$artloc = realpath( $art_dir ) . DIRECTORY_SEPARATOR .  "art_" . $newalbum->getID() . ".jpg" ;
								
								if($artloc !== false) {
									$filehandle = fopen($artloc, "wb");
									fwrite($filehandle,$track_metas[$i]['pic_data']);				
									fclose($filehandle);
				
									$newalbum->addMainArt("data" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "art_" . $newalbum->getID() .  ".jpg");
								}
							}
						}
					}
				}
			}			
	 }
	 if (isset($flags['showstatus']) && is_string($flags['showstatus']) && $flags['showstatus'] == "cli") {
           echo word("Scanning for removed media.") . "\n";
     }
	 	 
	 $be->removeDeadFiles();
}
	
?>