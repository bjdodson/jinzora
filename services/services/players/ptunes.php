<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *      
	* JINZORA | Web-based Media Streamer 
	*
	* Jinzora is a Web-based media streamer, primarily desgined to stream MP3s 
	* (but can be used for any media file that can stream from HTTP). 
	* Jinzora can be integrated into a CMS site, run as a standalone application, 
	* or integrated into any PHP website.  It is released under the GNU GPL. 
	* 
	* Jinzora Author:
	* Ross Carlson: ross@jasbone.com
	* http://www.jinzora.org
	* Documentation: http://www.jinzora.org/docs	
	* Support: http://www.jinzora.org/forum
	* Downloads: http://www.jinzora.org/downloads
	* License: GNU GPL <http://www.gnu.org/copyleft/gpl.html>
	* 
	* Contributors:
	* Please see http://www.jinzora.org/modules.php?op=modload&name=jz_whois&file=index
	*
	* Code Purpose: Creates and sends an M3U playlist to the Pocket Tunes player for PalmOS
	* Created: 03.03.05 by Ross Carlson
	*
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	define('SERVICE_PLAYERS_ptunes','true');

	/**
	* Returns the player width
	* 
	* @author Ben Dodson
	* @version 8/23/05
	* @since 8/23/05
	*/
	function SERVICE_RETURN_PLAYER_WIDTH_ptunes(){
	  return 0;
	}

	/**
	* Returns the players height.
	* 
	* @author Ben Dodson
	* @version 8/23/05
	* @since 8/23/05
	*/
	function SERVICE_RETURN_PLAYER_HEIGHT_ptunes(){
	  return 0;
	}

	
	/**
	* Returns the data for the form posts for the player
	* 
	* @author Ross Carlson
	* @version 06/05/05
	* @since 06/05/05
	* @param $formname The name of the form that is being created
	*/
	function SERVICE_RETURN_PLAYER_FORM_LINK_ptunes($formname){
		return "document.". $formname. ".target='embeddedPlayer'; openMediaPlayer('', 100, 100);";
	}
	
	
	/**
	* Returns the data for the href's to open the popup player
	* 
	* @author Ross Carlson
	* @version 06/05/05
	* @since 06/05/05
	*/
	function SERVICE_RETURN_PLAYER_HREF_ptunes(){
		return ' target="embeddedPlayer" onclick="openMediaPlayer(this.href, 300, 150); return false;"';
	}
	

	/**
	* Actually displays this embedded player
	* 
	* @author Ross Carlson
	* @version 3/03/05
	* @since 3/03/05
	* @param $list an array containing the tracks to be played
	*/
	function SERVICE_DISPLAY_PLAYER_ptunes($asx){
		global $root_dir, $this_site, $css;
		
		if ($asx){			
			echo '<META HTTP-EQUIV=Refresh CONTENT="0; URL='. $this_site. $root_dir. '/plist.asx">&nbsp;';
		} else {
			header("Location: ". $this_site. $root_dir. "/temp/ptunes-". $_SESSION['jz_playlist_number']. ".m3u");
		}
	}
	
	/**
	* Processes data for the jlGui embedded player
	* 
	* @author Ross Carlson
	* @version 3/03/05
	* @since 3/03/05
	* @param $list an array containing the tracks to be played
	*/
	function SERVICE_OPEN_PLAYER_ptunes($list){
		global $include_path, $root_dir;
		
		// Let's start the list off right
		$content = "#EXTM3U\n";
		$list->flatten();
		
		// First we need to make sure there aren't any WMV files here meaning they really want an ASX playlist
		$asx=false;
		foreach ($list->getList() as $track) {
			if (stristr($track->getPath("string"),".wmv")){
				$asx=true;
				$content = '<ASX version="3">'. "\n";
				$content .= '   <TITLE>Jinzora Playlist</Title>'. "\n";
			}
		}
		
		// Now let's loop throught the items to create the list
		foreach ($list->getList() as $track) {
			// Should we play this?
			if ((stristr($track->getPath("String"),".lofi.") 
				or stristr($track->getPath("String"),".clip."))
				and $_SESSION['jz_play_all_tracks'] <> true){continue;}
				
			$meta = $track->getMeta();
			
			if ($asx){
				// Now let's get the extension to be sure it can be played
				$pArr = explode("/",$track->getPath("String"));
				$eArr = explode(".",$pArr[count($pArr)-1]);
				$ext = $eArr[count($eArr)-1];
				
				$meta = $track->getMeta();
				$content .= "   <ENTRY>". "\n".
							"      <TITLE>". $meta['artist'] . " - " . $meta['title']. "</TITLE>". "\n";
				
				// Now let's figure out the full track name
				$trackn = $track->getFileName("user");
				if (!stristr($trackn,"mediabroadcast.php")) {
					$track->increasePlayCount();
				}
				$content .= '      <REF HREF="'. $trackn. '"/>'. "\n".
										'   </ENTRY>'. "\n";
			}	else {
				$content .= "#EXTINF:";
				$content .= $meta['length'] . ",";
				$content .= $meta['artist'] . " - " . $meta['title'] . "\n";
				
				// Now let's figure out the full track name
				$trackn = $track->getFileName("user");

				//echo $track; exit();
				$content .= $trackn. "\n";
			}
			if (!stristr($trackn,"mediabroadcast.php")) {
				$track->increasePlayCount();
			}
		}		
		
		// Now let's generate a random number for the playlist
		$_SESSION['jz_playlist_number'] = rand(1,99999);
		
		if ($asx){
			$content .= '</ASX>';
			// Now that we've got the playlist, let's write it out to the disk
			$plFile = $include_path. "temp/windowsmobile-". $_SESSION['jz_playlist_number']. ".asx";
			@unlink($plFile);
			$handle = fopen ($plFile, "w");
			fwrite($handle,$content);				
			fclose($handle);
		} else {		
			// Now that we've got the playlist, let's write it out to the disk
			$plFile = $include_path. "temp/ptunes-". $_SESSION['jz_playlist_number']. ".m3u";
			@unlink($plFile);
			$handle = fopen ($plFile, "w");
			fwrite($handle,$content);				
			fclose($handle);
		}

		SERVICE_DISPLAY_PLAYER_ptunes($asx);
	}	
?>
