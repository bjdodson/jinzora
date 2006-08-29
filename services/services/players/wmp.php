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

	define('SERVICE_PLAYERS_wmp','true');

	/**
	* Returns the player width
	* 
	* @author Ben Dodson
	* @version 8/23/05
	* @since 8/23/05
	*/
	function SERVICE_RETURN_PLAYER_WIDTH_wmp(){
	  return 300;
	}

	/**
	* Returns the players height.
	* 
	* @author Ben Dodson
	* @version 8/23/05
	* @since 8/23/05
	*/
	function SERVICE_RETURN_PLAYER_HEIGHT_wmp(){
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
	function SERVICE_RETURN_PLAYER_FORM_LINK_wmp($formname){
		return "document.". $formname. ".target='embeddedPlayer'; openMediaPlayer('', 300, 150);";
	}
	
	
	/**
	* Returns the data for the href's to open the popup player
	* 
	* @author Ross Carlson
	* @version 06/05/05
	* @since 06/05/05
	*/
	function SERVICE_RETURN_PLAYER_HREF_wmp(){
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
	function SERVICE_DISPLAY_PLAYER_wmp($width, $height){
		global $root_dir, $this_site, $css;;
		
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT"><!--\
			window.resizeTo(<?php echo $width; ?>,<?php echo $height; ?>)
		-->
		</SCRIPT>
		<?php	
		
		// Let's setup the page
		echo '<title>Jinzora WMP Media Player</title>';
		echo '<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#000000">';
		echo '<center>';
		
		$height = $height - 50;
		$width = $width - 20;
	
		$playlist = $this_site. $root_dir. "/temp/Playlist.wpl?". time();
		?>
		<OBJECT ID="MediaPlayer" WIDTH=<?php echo $width; ?> HEIGHT=<?php echo $height; ?>
			CLASSID="CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95"
			STANDBY="Loading Jinzora Media Stream..." 
			TYPE="application/x-oleobject"
			CODEBASE="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,7,1112">
            <PARAM name="autoStart" value="True">
			<PARAM NAME="AutoSize" VALUE="1">
			<PARAM NAME="ShowStatusBar" VALUE="1">
			<PARAM NAME="Displaysize" VALUE="1">
			<PARAM NAME="EnableContextMenu" VALUE="0">
			<PARAM NAME="ShowControls" VALUE="1">
			<PARAM NAME="Volume" VALUE="100%">
            <PARAM name="filename" value="<?php echo $playlist; ?>">
            <param name="wmode" value="transparent">
            <EMBED TYPE="application/x-mplayer2" 
				PLUGINSPAGE="http://www.microsoft.com/windows/mediaplayer/download/default.asp"
				SRC="<?php echo $playlist; ?>"
				NAME="MediaPlayer"
				SHOWCONTROLS="1"
				SHOWSTATUSBAR="1"
				SHOWPOSITIONCONTROLS="1"
				AUTOSIZE="1"
				VOLUME="100%";
				WIDTH=<?php echo $width; ?>
				HEIGHT=<?php echo $height; ?>> 
			</EMBED> 
		</object>
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
	function SERVICE_OPEN_PLAYER_wmp($list){
		global $include_path, $root_dir, $jzSERVICES;

		// Let's set the name of this player for later
		$player_type = "wmp";		
		
		// Now what media types can this player play?
		$playExt = "mpg|mpeg|avi|wmv|wma|mp3|wav";
		$playArray = explode("|",$playExt);
		
		// Let's start our Wimpy playlist
		$output_content = '<?wpl version="1.0"?>'. "\n";
		$output_content .= '<smil>'. "\n";
		$output_content .= '   <head>'. "\n";
		$output_content .= '      <meta name="Generator" content="Jinzora"/>'. "\n";
		$output_content .= '      <title>playlist</title>'. "\n";
		$output_content .= '   </head>'. "\n";
		$output_content .= '   <body>'. "\n";
		$output_content .= '      <seq>'. "\n";
		
		// Let's set the default height/width
		$width = "350";
		$height = "120";	
		
		// Now let's loop through each file
		$list->flatten();
		// Now let's loop throught the items to create the list
		foreach ($list->getList() as $track) {
			// Should we play this?
			if ((stristr($track->getPath("String"),".lofi.") 
				or stristr($track->getPath("String"),".clip."))
				and $_SESSION['jz_play_all_tracks'] <> true){continue;}

				$output_content .= '         <media src="'. str_replace("&","&amp;",$track->getFileName("user")). '"/>'. "\n";

				// Now let's get the height/width of this clip
				$tagdata = $jzSERVICES->getTagData($track->getDataPath("String"));
				$curHeight = $tagdata['height'];
				$curWidth = $tagdata['width'];
				
				if ($curHeight <> ""){
					// Now is that the biggest?
					if ($curHeight > $height){
						$height = $curHeight;
					}
					if ($curWidth > $width){
						$width = $curWidth;
					}
				}
		}
		
		// Now let's finish
		$output_content .= "      </seq>". "\n";
		$output_content .= "   </body>". "\n";
		$output_content .= "</smil>". "\n";
		
		// Now that we've got the playlist, let's write it out to the disk
		$plFile = $include_path. "temp/Playlist.wpl";
		@unlink($plFile);
		$handle = fopen ($plFile, "w");
		fwrite($handle,$output_content);				
		fclose($handle);
		
		// Now let's set the width and height and display
		SERVICE_DISPLAY_PLAYER_wmp($width, $height);
		
		exit();
	}	
?>