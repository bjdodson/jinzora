<?php

/* Displays the Jukebox Block
* 
* @author Ben Dodson
* @version 12/22/04
* @since 12/22/04
*/
global $this_page, $media_dirs, $jbArr, $root_dir, $include_path, $jzUSER, $img_delete, $img_jb_clear, $img_arrow_up, $img_arrow_down;

$display = new jzDisplay();
include_once ($include_path . "jukebox/class.php");

// let's default to stream
if (!isset ($_SESSION['jb_playwhere'])) {
	if (checkPermission($jzUSER, "stream")) {
		$_SESSION['jb_playwhere'] = "stream";
	} else {
		$_SESSION['jb_playwhere'] = $jbArr[0]['description'];
	}
}
$jb_playwhere = $_SESSION['jb_playwhere'];
?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jz_block_td" height="100%">
			<tr>
				<td width="5%" valign="top" height="100%">
					<nobr>
					<?php

// Now let's create our Jukebox class and connect to it to make sure it works
$jb = new jzJukebox();
if (!$jb->connect()) {
	echo "We had a problem connecting to the player, sorry this is a fatal error!<br><br>";
	echo "Player Settings:<br>";
	for ($i = 0; $i < count($jbArr); $i++) {
		if ($jbArr[$i]['description'] == $_SESSION['jb_playwhere']) {
			foreach ($jbArr[$i] as $setting => $value) {
				echo $setting . " - " . $value . "<br>";
			}
		}
	}
	echo "<br>Please check these with your player's settings";
	echo "<br>";
?>
							Playback to:<br>
							<?php

	$arr = array ();
	$arr['action'] = "jukebox";
	$arr['subaction'] = "jukebox-command";
	$arr['command'] = "playwhere";
?>
							<form action="<?php echo urlize($arr); ?>" method="post">
								<select name="jbplaywhere" class="jz_select" id="jukeboxSelect" style="width:142;" onChange="updateJukebox(true); return false;">
							   <?php if (checkPermission($jzUSER,'stream')) { ?>
									<option <?php if ($jb_playwhere == "stream"){ echo " selected "; } ?>value="stream">Stream</option>
									<?php

}
// Now let's get a list of all the jukeboxes that are installed
for ($i = 0; $i < count($jbArr); $i++) {
	echo '<option ';
	if ($jb_playwhere == $jbArr[$i]['description']) {
		echo " selected ";
	}
	echo 'value="' . $jbArr[$i]['description'] . '">' . $jbArr[$i]['description'] . '</option>';
}
?>
								</select>
							</form></nobr></td></tr></table>
							<?php

return;
}

// Let's figure out where they are playing
if (isset ($_SESSION['jb_playwhere'])) {
	$jb_playwhere = $_SESSION['jb_playwhere'];
} else {
	$jb_playwhere = "";
}

$remain = $jb->getCurrentTrackRemaining();
$jz_jbstatus = $jb->getPlayerStatus();
if ($jz_jbstatus <> "playing") {
	$remain = 0;
}
if ($remain == 1) {
	$remain = 0;
}
if ($remain > 1) {
	$remain = $remain -1;
}

if ($jb_playwhere <> "stream" && checkPermission($jzUSER, "jukebox_admin")) {
	// Ok, now we need to make sure we can do things
	$func = $jb->jbAbilities();

	if ($func['playbutton']) {
		$display->displayJukeboxButton("play");
	}
	if ($func['pausebutton']) {
		$display->displayJukeboxButton("pause");
	}
	if ($func['stopbutton']) {
		$display->displayJukeboxButton("stop");
	}
	if ($func['nextbutton']) {
		$display->displayJukeboxButton("previous");
	}
	if ($func['prevbutton']) {
		$display->displayJukeboxButton("next");
	}
	if ($func['shufflebutton']) {
		$display->displayJukeboxButton("random_play");
	}
	if ($func['clearbutton']) {
		$display->displayJukeboxButton("clear");
	}
	/*
	if ($func['repeatbutton']) {
	  $status = $jb->getPlayerStatus("repeat");
	  if ($status) {
	    $display->displayJukeboxButton("no_repeat");
	  } else {
	    $display->displayJukeboxButton("repeat");
	  }
	}
	*/
	echo '<br><br>';
	if ($func['status']) {
		echo 'Status: ';
		echo ucwords($jz_jbstatus);
		echo '<br>';
	}
	if ($func['stats']) {
		$jb->returnJBStats();
		echo '<br>';
	}
	$on = false;
	if ($func['progress'] and $on) {
?>
								Progress:
								<span id="timer">&nbsp;<br></span><br>
								<?php

	}

	if ($func['volume']) {
		$arr = array ();
		$arr['action'] = "jukebox";
		$arr['subaction'] = "jukebox-command";
		$arr['command'] = "volume";
?>
								<form action="<?php echo urlize($arr); ?>" method="post">
									<input type="hidden" name="action" value="jukebox">
									<input type="hidden" name="subaction" value="jukebox-command">
									<input type="hidden" name="command" value="volume">
									<select name="jbvol" id="jukeboxVolumeSelect" class="jz_select" style="width:142;" onChange="sendJukeboxVol(); return false">
										<?php

		$vol = "";
		if (isset ($_SESSION['jz_jbvol-' . $_SESSION['jb_id']])) {
			$vol = $_SESSION['jz_jbvol-' . $_SESSION['jb_id']];
		}

		$c = 100;
		while ($c > 0) {
			echo '<option ';
			if ($c == $vol) {
				echo ' selected ';
			}
			echo 'value="' . $c . '">Volume ' . $c . '%</option>';
			$c = $c -10;
		}
?>
										<option value="0">Mute</option>
									</select>
								</form>
								<br>
								<?php

	}

	// This closes our if to see if we are streaming or not
}
echo 'Playback to:<br>';
$arr = array ();
$arr['action'] = "jukebox";
$arr['subaction'] = "jukebox-command";
$arr['command'] = "playwhere";
?>
						<form action="<?php echo urlize($arr); ?>" method="post">
							<select name="jbplaywhere" class="jz_select" id="jukeboxSelect" style="width:142;" onChange="updateJukebox(true); return false;">
						   <?php if (checkPermission($jzUSER,'stream')) { ?>
								<option <?php if ($jb_playwhere == "stream"){ echo " selected "; } ?>value="stream">Stream</option>
								<?php

}
// Now let's get a list of all the jukeboxes that are installed
for ($i = 0; $i < count($jbArr); $i++) {
	echo '<option ';
	if ($jb_playwhere == $jbArr[$i]['description']) {
		echo " selected ";
	}
	echo 'value="' . $jbArr[$i]['description'] . '">' . $jbArr[$i]['description'] . '</option>';
}
?>
							</select>
						</form>
						<?php

if ($jb_playwhere <> "stream" and $func['addtype']) {
	echo '<br>';
	echo 'Add type:<br>';
	// Now let's set the add type IF it hasn't been set
	if (!isset ($_SESSION['jb-addtype'])) {
		$_SESSION['jb-addtype'] = "current";
	}

	$arr = array ();
	$arr['action'] = "jukebox";
	$arr['subaction'] = "jukebox-command";
	$arr['command'] = "addwhere";
?>
						<form action="<?php echo urlize($arr); ?>" method="post">
							<input type="hidden" name="action" value="jukebox">
							<input type="hidden" name="subaction" value="jukebox-command">
							<input type="hidden" name="command" value="addwhere">
							<select name="addplat" class="jz_select" id="jukeboxAddTypeSelect" style="width:142;" onChange="sendJukeboxAddType(); return false;">
								<option <?php if ($_SESSION['jb-addtype'] == "current"){echo " selected ";} ?> value="current">At Current</option>
								<option <?php if ($_SESSION['jb-addtype'] == "end"){echo " selected ";} ?>value="end">At End</option>
								<option <?php if ($_SESSION['jb-addtype'] == "begin"){echo " selected ";} ?>value="begin">At Beginning</option>
								<option <?php if ($_SESSION['jb-addtype'] == "replace"){echo " selected ";} ?>value="replace">Replace</option>
							</select>
					</form>
					</nobr>
					<?php

}
?>
				</td>
				<td width="5%" valign="top">
					<?php

// Let's make sure they aren't streaming
if ($jb_playwhere == "stream") {
	echo '</td></tr></table>';
	return;
}
?>
					<?php

if ($func['nowplaying']) {
	$curTrack = $jb->getCurrentTrackName();
	$fullname = $curTrack;
	$curTrack = $display->returnShortName($curTrack, 25);
?>
						<?php echo word("Now Playing:"). ' <a href="javascript:;" title="'. $fullname. '">'. $curTrack. "</a><br>"; ?>
						<!--
						<span ID="CurTicker" STYLE="overflow:hidden; width:275px;"  onmouseover="CurTicker_PAUSED=true" onmouseout="CurTicker_PAUSED=false">
							
						</span>
						-->
						<?php

	if ($func['nexttrack']) {
		$fullList = $jb->getCurrentPlaylist();
			if ($fullList != array ()) {
			$nextTrack = $fullList[getCurPlayingTrack() + 1];
			$fullname = $nextTrack;
			if (stristr($nextTrack, "/")) {
				$nArr = explode("/", $nextTrack);
				$nextTrack = $nArr[count($nArr) - 1];
			}
			$nextTrack = str_replace(".mp3", "", $nextTrack);
			$nextTrack = $display->returnShortName($nextTrack, 30);
		}
?>
								<?php echo word("Next Track:"). ' <a href="javascript:;" title="'. $fullname. '">'. $nextTrack. "</a><br>"; ?>
								<!--
								<DIV ID="NextTicker" STYLE="overflow:hidden; width:275px;"  onmouseover="NextTicker_PAUSED=true" onmouseout="NextTicker_PAUSED=false">
									
								</DIV>
								-->
								<?php

	}
?>
						<script language="javascript" src="<?php echo $root_dir; ?>/jukebox/ticker.js"></script>
					<?php

}

if ($func['fullplaylist']) {
	if (!is_array($fullList)) {
		$fullList = $jb->getCurrentPlaylist();
	}
?>
					
						Complete Playlist
						<?php

	// Did they need any addon tools
	echo $jb->getAddOnTools();
?>
						<br>
						<?php

	// Now let's get the full playlist back
	$curTrackNum = $jb->getCurrentPlayingTrack();
?>
						<?php

	$arr = array ();
	$arr['action'] = "jukebox";
	$arr['subaction'] = "jukebox-command";
?>
						<form action="<?php echo urlize($arr); ?>" method="post" name="jbPlaylistForm" id="jbPlaylistForm">
						<input type="hidden" name="action" value="jukebox">
						<input type="hidden" name="subaction" value="jukebox-command">
						<input type="hidden" id="jbCommand" name="command" value="jumpto">
						<?php

	if (isset ($_SESSION['jbSelectedItems'])) {
		$selected = $_SESSION['jbSelectedItems'];
		unset ($_SESSION['jbSelectedItems']);
	} else {
		$selected = array ();
	}
?>
							<select multiple name="jbjumpto[]" id="jukeboxJumpToSelect" class="jz_select" size="6" style="width:275px;"<?php if ($func['jump']){ echo 'ondblclick="setJbFormCommand(\'jumpto\'); sendJukeboxForm(); return false;"'; }?>>
								<?php

	for ($i = 0; $i < count($fullList); $i++) {
		echo '<option value="' . $i . '"';
		if (false !== array_search($i, $selected)) {
			echo " selected ";
		}
		if ($curTrackNum == $i) {
			echo " style=\"font-weight:bold;\" ";
			echo '> * ' . $fullList[$i] . '</option>';
		} else {
			echo '>' . $fullList[$i] . '</option>';
		}

	}
?>
							</select>
							<div id="jbPlaylistButtons" style="text-align:right;">
							<?php

	if ($func['move']) {
?>
								<a href="#" 
                            	   onclick="setJbFormCommand('moveup'); sendJukeboxForm(); return false;">
                            		<?php echo $img_arrow_up; ?>
                                </a>
                                <a href="#" 
                            	   onclick="setJbFormCommand('movedown'); sendJukeboxForm(); return false;">
                            		<?php echo $img_arrow_down; ?>
                                </a>
								<?php

	}
	if ($func['delonebutton']) {
?>
								<a href="#" 
                            	   onclick="setJbFormCommand('delone'); sendJukeboxForm(); return false;">
                            		<?php echo $img_jb_clear; ?>
                                </a>
								<?php

	}
?>
                                </div>
						</form>
					<?php

}
?>
				</td>
				<td width="90%" valign="top">					
					<?php

if ($jz_jbstatus == 'playing') {
	$curTrackLength = $jb->getCurrentTrackLength();
	$curTrackLoc = $jb->getCurrentTrackLocation();
?>
							<script> 
								<!--// 
								var seconds = '<?php echo $curTrackLoc; ?>';
								var time = '';
								t = document.getElementById("timer");	
								
								function converTime(sec){
									ctr=0;
									while (sec >= 60){
										sec = sec - 60;
										ctr++;
									}
									if (ctr<0){ctr=0}
									if (sec<0){sec=0}
									if (sec < 10){sec = "0" + sec;}							
									return ctr + ":" + sec;
								}
								
								function displayCountdown(){ 
								  return;
									// Update the counter
									seconds++	
										
									// Now let's not go over
									if (seconds < <?php echo $curTrackLength; ?>){
										t.innerHTML = converTime(seconds) + "/<?php echo convertSecMins($curTrackLength); ?>";
									} else {
										t.innerHTML = "<?php echo convertSecMins($curTrackLength); ?>/<?php echo convertSecMins($curTrackLength); ?>";
										<?php writeLogData("messages","Jukebox block: Refreshing the jukebox display"); ?>
										seconds = 1;
										updateJukebox(true);
									}
									setTimeout("displayCountdown()",1000);
								} 
								displayCountdown();
								--> 
							</script> 
							<?php

}
// Now we need to return the path to the track that is playing so we can get the art and description for it
$filePath = $jb->getCurrentTrackPath();
$track = new jzMediaNode($filePath, "filename");

// Now let's make sure we are looking at a track for real
if (false !== $track && $track->getPath() != "") {

	$node = $track->getAncestor("album");

	if ($node) {
		// Now let's set what we'll need
		$album = ucwords($node->getName());
		$parent = $node->getAncestor("artist");
		if ($parent) {
			$artist = ucwords($parent->getName());
		} else {
			$artist = "";
		}
		// Now let's display the art
		if (($art = $node->getMainArt("130x130")) == false) {
			$art = "style/images/default.jpg";
		}
		$display->link($parent, $artist, $artist, false, false, false, false, false, "_top");
		echo " - ";
		$display->link($node, $album, $album, false, false, false, false, false, "_top");
		echo "<br>";
		echo $display->returnImage($art, $node->getName(), "130", "130", "fit", false, false, "left", "5", "5");

		// Now let's get the review
		$desc = $node->getDescription();
		$desc_truncate = 375;
		echo $display->returnShortName($desc, $desc_truncate);
		if (strlen($desc) > $desc_truncate) {
			$url_array = array ();
			$url_array['jz_path'] = $node->getPath("String");
			$url_array['action'] = "popup";
			$url_array['ptype'] = "readmore";
			echo ' <a href="' . urlize($url_array) . '" onclick="openPopup(this, 450, 450); return false;">...read more</a>';
		}
	}
}
?>
				</td>
			</tr>
		</table>
		<SCRIPT><!--			
			NextTicker_start();
			CurTicker_start();
		//-->
		</script>
		    <script>setTimeout('jukeboxUpdater()',10*1000);</script>