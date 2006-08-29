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
	* - Creates an ASX compliant playlist
	*
	* @since 02.17.05
	* @author Ben Dodson <ben@jinzora.org>
	* @author Ross Carlson <ross@jinzora.org>
	*/

	/**
	* Returns the mime type for this playlist
	* 
	* @author Ross Carlson
	* @version 2/24/05
	* @since 2/24/05
	* @param $return Returns the playlist mime type
	*/
	function SERVICE_RETURN_MIME_ASX(){
		return "video/x-ms-asx";
	}
	
	/**
	* Creates an ASX compliant playlist and returns it for playing
	* 
	* @author Ross Carlson
	* @version 2/24/05
	* @since 2/24/05
	* @param $list The list of tracks to use when making the playlist
	* @param $return Returns the porperly formated list
	*/
	function SERVICE_CREATE_PLAYLIST_ASX($list){
		global $allow_resample, $this_site, $root_dir, $web_root;
		
		// Let's setup Smarty
		$smarty = smartySetup();
		
		// This playlist type supports the following media types:
		$supported = "asf|wma|wmv|wm|asx|wax|wvx|wpl|dvr-ms|wmd|avi|mpg|mpeg|m1v|mp2|mp3|mpa|mpe|mpv2|m3u|ogg|mid|midi|rmi|aif|aifc|aiff|au|snd|wav|ivf|flac|mpc|wv|m4a|";
		
		// Let's define our variables
		$i=0;
		foreach ($list->getList() as $track) {
			// Should we play this?
			if ((stristr($track->getPath("String"),".lofi.") 
				or stristr($track->getPath("String"),".clip."))
				and $_SESSION['jz_play_all_tracks'] <> true){continue;}
				
			// Now let's get the extension to be sure it can be played
			$pArr = explode("/",$track->getPath("String"));
			$eArr = explode(".",$pArr[count($pArr)-1]);
			$ext = $eArr[count($eArr)-1];
			if (!stristr($supported,$ext. "|")){continue;}
			
			// Let's get the meta
			$meta = $track->getMeta();
			
			// Now let's figure out the full track name
			$trackn = $track->getFileName("user");
			if (!stristr($trackn,"mediabroadcast.php")) {
			  $track->increasePlayCount();
			}
			$tArr[$i]['link'] = $trackn;
			if ($meta['artist'] <> "" and $meta['artist'] <> "-"){
				$tArr[$i]['artist'] = $meta['artist']. " - ";
			} else {
				$tArr[$i]['artist'] = "";
			}
			$tArr[$i]['album'] = $meta['album'];
			$tArr[$i]['genre'] = $meta['genre'];
			$tArr[$i]['track'] = $meta['title'];
			$tArr[$i]['length'] = $meta['length'];
			$tArr[$i]['path'] = rawurlencode($track->getPath("String"));
			$tArr[$i]['url'] = $this_site. $root_dir;
			$i++;
		}

		$smarty->assign('this_site', $this_site);
		$smarty->assign('root_dir', $root_dir);
		$smarty->assign('tracks', $tArr);
		$smarty->assign('totalTracks', $i);
				
		// Now let's include the template
		$smarty->display(SMARTY_ROOT. 'templates/playlists/asx.tpl');
	}
?>