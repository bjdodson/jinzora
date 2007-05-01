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
	
	define('SERVICE_PLAYERS_fsmp3','true');
	

	/**
	* Returns the player width
	* 
	* @author Ben Dodson
	* @version 8/23/05
	* @since 8/23/05
	*/
	function SERVICE_RETURN_PLAYER_WIDTH_fsmp3(){
	  return 300;
	}

	/**
	* Returns the players height.
	* 
	* @author Ben Dodson
	* @version 8/23/05
	* @since 8/23/05
	*/
	function SERVICE_RETURN_PLAYER_HEIGHT_fsmp3(){
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
	function SERVICE_RETURN_PLAYER_FORM_LINK_fsmp3($formname){
		return "document.". $formname. ".target='embeddedPlayer'; openMediaPlayer('', 300, 150);";
	}
	
	
	/**
	* Returns the data for the href's to open the popup player
	* 
	* @author Ross Carlson
	* @version 06/05/05
	* @since 06/05/05
	*/
	function SERVICE_RETURN_PLAYER_HREF_fsmp3(){
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
	function SERVICE_DISPLAY_PLAYER_fsmp3($width, $height){
		global $root_dir, $this_site, $css;
		
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT"><!--\
			window.resizeTo(<?php echo $width; ?>,<?php echo $height; ?>)
		-->
		</SCRIPT>
		<?php	
		// Let's setup the page
		echo '<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#242424">';
		echo '<title>Jinzora FSmp3 Media Player</title>';
		echo '<center>';
		$playlist = $this_site. $root_dir. "/temp/audiolist.xml?". time();
		$height = $height-40;
		echo '</center>';

		?>
		<center>
		<object 
			classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" 
			codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,47,0" 
			width="<?php echo $width; ?>" 
			height="<?php echo $height; ?>" 
			id="fsmp3" 
			align="middle">
			<param name="salign" value="lt" /> 
			<param name="allowScriptAccess" value="sameDomain" />
			<param name="movie" value="<?php echo $root_dir; ?>/services/services/players/fsmp3_player.swf?autoplay=true&autoload=true&playlist_url=<?php echo $this_site. $root_dir; ?>/temp/audiolist.xml" />
			<param name="loop" value="true" />
			<param name="menu" value="false" />
			<param name="quality" value="high" />
			<param name="bgcolor" value="<?php echo $bg_c; ?>" />
			<param name="scale" value="noscale" />
			<embed src="<?php echo $this_site. $root_dir; ?>/services/services/players/fsmp3_player.swf?autoplay=true&autoload=true&playlist_url=<?php echo $this_site. $root_dir; ?>/temp/audiolist.xml"
			loop="true" 
			menu="false" 
			quality="high" 
			bgcolor="<?php echo $bg_c; ?>" 
			width="<?php echo $width; ?>" 
			height="<?php echo $height; ?>" 
			name="Jinzora fsmp3 Player" 
			align="middle" 
			allowScriptAccess="sameDomain" 
			type="application/x-shockwave-flash" 
			scale="noscale" salign="lt" 
			pluginspage="http://www.macromedia.com/go/getflashplayer" />
		</object>
		<!-- End fsmp3 Player Code -->
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
	function SERVICE_OPEN_PLAYER_fsmp3($list){
		global $include_path, $root_dir, $this_site;
	
		$display = new jzDisplay();

		// Let's set the name of this player for later
		$player_type = "fsmp3";		
				
		// Now let's loop through each file
		$list->flatten(); 
		
		$output_content = '<?xml version="1.0"?>'. "\n". '<songs>'. "\n";		
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
			
			$output_content .= '<song path="'. $track->getFileName("user"). '" bild="'. $image. '" artist="'. $meta['artist']. '" title="'. $meta['title']. '"/>'. "\n";
		}

		// Now let's finish up the content
		$output_content .= '</songs>';
		
		// Now that we've got the playlist, let's write it out to the disk
		$plFile = $include_path. "temp/audiolist.xml";
		@unlink($plFile);
		$handle = fopen ($plFile, "w");
		fwrite($handle,$output_content);				
		fclose($handle);
		
		// Ok, now we need to pop open the fsmp3 Player
		$width = "400";
		$height = "360";
		SERVICE_DISPLAY_PLAYER_fsmp3($width, $height);
		exit();
	}	
?>