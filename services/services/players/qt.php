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
	
	define('SERVICE_PLAYERS_qt','true');
	
	/**
	* Returns the player width
	* 
	* @author Ben Dodson
	* @version 8/23/05
	* @since 8/23/05
	*/
	function SERVICE_RETURN_PLAYER_WIDTH_qt(){
	  return 640;
	}

	/**
	* Returns the players height.
	* 
	* @author Ben Dodson
	* @version 8/23/05
	* @since 8/23/05
	*/
	function SERVICE_RETURN_PLAYER_HEIGHT_qt(){
	  return 480;
	}
	
	/**
	* Returns the data for the form posts for the player
	* 
	* @author Ross Carlson
	* @version 06/05/05
	* @since 06/05/05
	* @param $formname The name of the form that is being created
	*/
	function SERVICE_RETURN_PLAYER_FORM_LINK_qt($formname){
		return "document.". $formname. ".target='embeddedPlayer'; openMediaPlayer('', 300, 150);";
	}
	
	
	/**
	* Returns the data for the href's to open the popup player
	* 
	* @author Ross Carlson
	* @version 06/05/05
	* @since 06/05/05
	*/
	function SERVICE_RETURN_PLAYER_HREF_qt(){
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
	function SERVICE_DISPLAY_PLAYER_qt($width, $height, $track){
		global $root_dir, $this_site, $css;;
		
		// Let's setup the page
		echo '<title>Jinzora Quicktime Media Player</title>';
		echo '<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#000000">';
		echo '<center>';
		
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT"><!--\
			window.resizeTo(<?php echo ($width + 25); ?>,<?php echo ($height + 50); ?>)
		-->
		</SCRIPT>
		<embed 
			width="<?php echo ($width - 10); ?>" 
			bgcolor="black" 
			src="<?php echo $track; ?>" 
			height="<?php echo ($height); ?>" 
			controler="true" 
			autoplay="true" 
			type="video/quicktime" 
			pluginspage="http://www.aple.com/quicktime/download/">
		</embed>
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
	function SERVICE_OPEN_PLAYER_qt($list){
		global $include_path, $root_dir, $jzSERVICES;
		
		$list->flatten();
		$data = $list->getList();
		$track = $data[0]->getFileName("user");
		
		// Let's get the meta so we'll have the height/width
		$meta = $jzSERVICES->getTagData($data[0]->getDataPath());
		if ($meta['width'] <> ""){
			$width = $meta['width'] + 10;
			$height = $meta['height'] + 25;
		} else {
			// Ok, let's pick the defaults
			$width = "640";
			$height = "480";
		}		
		
		// Now let's set the width and height and display
		SERVICE_DISPLAY_PLAYER_qt($width, $height, $track);
	}	
?>