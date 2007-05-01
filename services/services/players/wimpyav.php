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
	* Code Purpose: Processes data for the jlGui embedded Java Player
	* Created: 03.03.05 by Ross Carlson
	*
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	define('SERVICE_PLAYERS_wimpyav','true');

	/**
	* Returns the player width
	* 
	* @author Ben Dodson
	* @version 8/23/05
	* @since 8/23/05
	*/
	function SERVICE_RETURN_PLAYER_WIDTH_wimpyav(){
	  return 300;
	}

	/**
	* Returns the players height.
	* 
	* @author Ben Dodson
	* @version 8/23/05
	* @since 8/23/05
	*/
	function SERVICE_RETURN_PLAYER_HEIGHT_wimpyav(){
	  return 150;
	}


	/**
	* Returns the data for the form posts for the player
	* 
	* @author Ross Carlson
	* @version 06/05/05
	* @since 06/05/05
	* @param $formname The name of the form that is being created
	*/
	function SERVICE_RETURN_PLAYER_FORM_LINK_wimpyav($formname){
		return "document.". $formname. ".target='embeddedPlayer'; openMediaPlayer('', 300, 150);";
	}
	
	
	/**
	* Returns the data for the href's to open the popup player
	* 
	* @author Ross Carlson
	* @version 06/05/05
	* @since 06/05/05
	*/
	function SERVICE_RETURN_PLAYER_HREF_wimpyav(){
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
	function SERVICE_DISPLAY_PLAYER_wimpyav($width, $height){
		global $root_dir, $this_site, $css;;
		
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT"><!--\
			window.resizeTo(<?php echo $width; ?>,<?php echo $height; ?>)
		-->
		</SCRIPT>
		<?php	
		// Let's setup the page
		echo '<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#000000">';
		echo '<title>Jinzora Wimpy Media Player</title>';
		echo '<center>';
	
		$playlist = $this_site. $root_dir. "/temp/Playlist.wpl?". time();
		$height = "260";
		
		$urlData = "";
		$urlData .= "wimpyApp=". $this_site. $root_dir. "/temp/Playlist.xml&";
		$urlData .= "background_color=#000000&";
		$urlData .= "startPlayingOnload=yes&";
		$urlData .= "autoAdvance=yes";
		?>
		<center>
		<!-- Wimpy Player Code -->
		<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="400" height="<?php echo $height; ?>">
			<param name="movie" value="<?php echo $this_site. $root_dir; ?>/services/services/players/wimpyav/wimpy.swf?<?php echo $urlData; ?>">
			<param name="quality" value="high">
			<param name="bgcolor" value=#000000>
			 <param name="loop" value="false" />
			 <param name="menu" value="false" />
			 <param name="scale" value="noscale" />
			 <param name="salign" value="lt" />
		<embed src="<?php echo $this_site. $root_dir; ?>/services/services/players/wimpyav/wimpy.swf?<?php echo $urlData; ?>" scale="noscale" salign="lt" width="400" height="<?php echo $height; ?>" quality="high" bgcolor=#000000 pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed></object>
		<!-- End Wimpy Player Code -->
		</center>
		<?php
		exit();
	}
	
	/**
	* Processes data for the jlGui embedded player
	* 
	* @author Ross Carlson
	* @version 3/03/05
	* @since 3/03/05
	* @param $list an array containing the tracks to be played
	*/
	function SERVICE_OPEN_PLAYER_wimpyav($list){
		global $include_path, $root_dir, $this_site;
	
		$display = new jzDisplay();

		// Let's set the name of this player for later
		$player_type = "wimpy";		
				
		// Now let's loop through each file
		$list->flatten(); 
		
		$output_content = '<?xml version="1.0"?>'. "\n". '<playlist>'. "\n";
		// Now let's loop throught the items to create the list
		foreach ($list->getList() as $track) {
			// Should we play this?
			if ((stristr($track->getPath("String"),".lofi.") 
				or stristr($track->getPath("String"),".clip."))
				and $_SESSION['jz_play_all_tracks'] <> true){continue;}
				
			// Let's get the meta
			$meta = $track->getMeta();
			
			// Let's get the art
			$parent = $track->getParent();
			if (($art = $parent->getMainArt("150x150")) !== false) {
			  $image = jzCreateLink($art,"image");
			} else {
			  $image = $this_site. $root_dir. "/style/images/default.jpg";
			}
			
			// Now let's fix the track URL & image
			$tUrl = urlencode($track->getFileName("user"));
			$tUrl = str_replace("http%3A%2F%2F","http://",$tUrl);
			$tUrl = str_replace("https%3A%2F%2F","https://",$tUrl);
			$tUrl = str_replace("%2F","/",$tUrl);
			$image = urlencode($image);
			$image = str_replace("http%3A%2F%2F","http://",$image);
			$image = str_replace("https%3A%2F%2F","https://",$image);
			$image = str_replace("%2F","/",$image);
			
			$output_content .= '     <item>'. "\n".
								'          <filename>'. $tUrl. '</filename>'. "\n".
								'          <artist>'. urlencode($meta['artist']). '</artist>'. "\n".
								'          <album>'. urlencode($meta['album']). '</album>'. "\n".
								'          <title>'. urlencode($meta['title']). '</title>'. "\n".
								'          <track>'. urlencode($meta['number']). '</track>'. "\n".
								'          <comments>'. urlencode($track->getDescription). '</comments>'. "\n".
								'          <genre>'. urlencode($meta['genre']). '</genre>'. "\n".
								'          <seconds>'. $meta['length']. '</seconds>'. "\n".
								'          <filesize>'. $meta['size']. '</filesize>'. "\n".
								'          <bitrate>'. $meta['bitrate']. '</bitrate>'. "\n".
								'          <visual>'. $image. '</visual>'. "\n".
								'          <url>'. $this_site. $root_dir. '</url>'. "\n".
								'     </item>'. "\n";
		}

		// Now let's finish up the content
		$output_content .= '</playlist>';
		
		// Now that we've got the playlist, let's write it out to the disk
		$plFile = $include_path. "temp/Playlist.xml";
		@unlink($plFile);
		$handle = fopen ($plFile, "w");
		fwrite($handle,$output_content);				
		fclose($handle);
		
		// Ok, now we need to pop open the Wimpy player
		$width = "400";
		$height = "305";
		SERVICE_DISPLAY_PLAYER_wimpyav($width, $height);
		exit();
	}	
?>