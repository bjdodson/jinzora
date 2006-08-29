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
	* - Creates an RAM compliant playlist
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
	function SERVICE_RETURN_MIME_RAM(){
		return "audio/x-pn-realaudio";
	}
	
	/**
	* Creates an RAM compliant playlist and returns it for playing
	* 
	* @author Ross Carlson
	* @version 2/24/05
	* @since 2/24/05
	* @param $list The list of tracks to use when making the playlist
	* @param $return Returns the porperly formated list
	*/
	function SERVICE_CREATE_PLAYLIST_RAM($list){
		global $allow_resample, $this_site, $root_dir, $web_root;
		
		// Let's start the list off right
		$content = "";
		$list->flatten();
		
		// Let's setup Smarty
		$smarty = smartySetup();
		
		// Let's start the list off right
		$list->flatten();
		
		$i=0;
		foreach ($list->getList() as $track) {
			// Should we play this?
			if ((stristr($track->getPath("String"),".lofi.") 
				or stristr($track->getPath("String"),".clip."))
				and $_SESSION['jz_play_all_tracks'] <> true){continue;}
			
			// Let's get the meta
			$meta = $track->getMeta();
			
			// Now let's figure out the full track name
			$trackn = $track->getFileName("user");
			if (!stristr($trackn,"mediabroadcast.php")) {
			  $track->increasePlayCount();
			}
			$tArr[$i]['link'] = $trackn;
			$tArr[$i]['artist'] = $meta['artist'];
			$tArr[$i]['album'] = $meta['album'];
			$tArr[$i]['genre'] = $meta['genre'];
			$tArr[$i]['track'] = $meta['title'];
			$tArr[$i]['length'] = $meta['length'];
			$tArr[$i]['year'] = $meta['year'];
			$tArr[$i]['i'] = ($i + 1);
			$i++;
		}
		unset($_SESSION['jz_play_all_tracks']);
		
		$smarty->assign('this_site', $this_site);
		$smarty->assign('root_dir', $root_dir);
		$smarty->assign('tracks', $tArr);
		$smarty->assign('total', count($tArr));
		
		// Now let's include the template
		$smarty->display(SMARTY_ROOT. 'templates/playlists/ram.tpl');
		return;
		
		
		// Now let's loop throught the items to create the list
		foreach ($list->getList() as $track) {
			// Should we play this?
			if ((stristr($track->getPath("String"),".lofi.") 
				or stristr($track->getPath("String"),".clip."))
				and $_SESSION['jz_play_all_tracks'] <> true){continue;}
				
			$meta = $track->getMeta();			
			// Now let's figure out the full track name
			$trackn = $track->getFileName("user");
			if (!stristr($trackn,"mediabroadcast.php")) {
			  $track->increasePlayCount();
			}
			// Now let's set the URL
			$content .= $trackn. '&clipinfo="title='. $meta['title']. '|artist name='.$meta['artist']. '|album name='. $meta['album']. '|genre='. $meta['genre']. '|year='. $meta['year']. '"&mode=normal'. "\n";
		}

		unset($_SESSION['jz_play_all_tracks']);

		// Now let's return
		return $content;
	}
?>