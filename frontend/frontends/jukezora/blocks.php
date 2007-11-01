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
	* Creates many of the different blocks that are used by the Slick interface
	*
	* @since 01.11.05
	* @author Ross Carlson <ross@jinzora.org>
	*/
	
	class jzBlocks extends jzBlockClass {
	  
		/**
		* Constructor for the class.
		* 
		* @author Ben Dodson
		* @version 12/22/04
		* @since 12/22/04
		*/
		function jzBlocks() {
		
		}
		
		/**
		* Draws the play controller for the Jukezora interface
		* 
		* @author Ross Carlson
		* @version 01/21/05
		* @since 01/21/05
		*/
		function jukeboxBlock(){
			global $this_page, $media_dirs, $jbArr, $root_dir,$include_path;
		
			$display = new jzDisplay();
			include_once($include_path. "jukebox/class.php");
			
			if (!isset($_SESSION['jb_playwhere'])){$_SESSION['jb_playwhere'] = "stream";}
			$jb_playwhere = $_SESSION['jb_playwhere'];
			?>
			<table width="100%" cellpadding="2" cellspacing="0" border="0" class="jz_block_td" height="100%">
				<tr>
					<td width="50%" valign="top" height="100%">
						<nobr>
						<?php
							// Now let's create our Jukebox class and connect to it to make sure it works
							$jb = new jzJukebox();
							if (!$jb->connect()){
								echo "We had a problem connecting to the player, sorry this is a fatal error!<br><br>";
								echo "Player Settings:<br>";
								for ($i=0; $i < count($jbArr); $i++){
									if ($jbArr[$i]['description'] == $_SESSION['jb_playwhere']){
										foreach ($jbArr[$i] as $setting=>$value) {
											echo $setting. " - ". $value."<br>";
										}
									}
								}
								echo "<br>Please check these with your player's settings";
								echo "<br>";
								?>
								Playback to:<br>
								<?php
									$arr = array();
									$arr['action'] = "jukebox";
									$arr['subaction'] = "jukebox-command";
									$arr['command'] = "playwhere";
								?>
								<form action="<?php echo urlize($arr); ?>" method="post">
									<select name="jbplaywhere" class="jz_select" style="width:142;" onChange="submit()">
										<option <?php if ($jb_playwhere == "stream"){ echo " selected "; } ?>value="stream">Stream</option>
										<?php
											// Now let's get a list of all the jukeboxes that are installed
											for ($i=0; $i < count($jbArr); $i++){
												echo '<option ';
												if ($jb_playwhere == $jbArr[$i]['description']){ echo " selected "; }
												echo 'value="'. $jbArr[$i]['description']. '">'. $jbArr[$i]['description']. '</option>';
											}
										?>
									</select>
								</form>
								<?php
								return;
							}
							
							// Let's figure out where they are playing
							if (isset($_SESSION['jb_playwhere'])){
								$jb_playwhere = $_SESSION['jb_playwhere'];
							} else {
								$jb_playwhere = "";
							}
						
							$remain = $jb->getCurrentTrackRemaining() + 1;
							$jz_jbstatus = $jb->getPlayerStatus();
							if ($jz_jbstatus <> "playing"){
								$remain = 0;
							}
							if ($remain == 1){$remain = 0; }
							
							if ($jb_playwhere <> "stream"){
								// Ok, now we need to make sure we can do things
								$func = $jb->jbAbilities();
								
								if ($func['playbutton']){
									$display->displayJukeboxButton("play");
								}
								if ($func['pausebutton']){
									$display->displayJukeboxButton("pause");
								}
								if ($func['stopbutton']){
									$display->displayJukeboxButton("stop");
								}
								if ($func['shufflebutton']){
									$display->displayJukeboxButton("random_play");
								}
								echo "<br>";
								if ($func['nextbutton']){
									$display->displayJukeboxButton("previous");
								}
								if ($func['prevbutton']){
									$display->displayJukeboxButton("next");
								}
								if ($func['clearbutton']){
									$display->displayJukeboxButton("clear");
								}
							?>
							<br>
							<?php
								if ($func['status']){
							?>
								Status: 
								<?php
									echo ucwords($jz_jbstatus);
								?>
								<br>
							<?php
								}
							?>
							<?php
								if ($func['stats']){
									$jb->returnJBStats();
									echo '<br>';
								}
							?>						
							
							<?php
								if ($func['progress']){
							?>
								Progress:
								<span id="timer"></span>
								<script> 
								<!--// 
								var loadedcolor='green' ;            // PROGRESS BAR COLOR
								var unloadedcolor='lightgrey';      // BGCOLOR OF UNLOADED AREA
								var barheight=10;                   // HEIGHT OF PROGRESS BAR IN PIXELS
								var barwidth=120;                   // WIDTH OF THE BAR IN PIXELS
								var bordercolor='black';            // COLOR OF THE BORDER
								var waitTime=<?php echo $remain; ?>;                   // NUMBER OF SECONDS FOR PROGRESSBAR
								--> 
								</script>
								<script language="javascript" src="<?php echo $root_dir; ?>/jukebox/pbar.js"></script>
								<?php
								if ($jz_jbstatus == 'playing'){
								?>
								<script> 
								<!--// 
								var seconds = <?php echo $jb->getCurrentTrackLocation(); ?>;
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
									if (sec < 10){
										sec = "0" + sec;
									}
								
									return ctr + ":" + sec;
								}
								
								function display(){ 
									seconds++
									
									t.innerHTML = converTime(seconds) + "/" + "<?php echo convertSecMins($jb->getCurrentTrackLength()); ?>";
									ctr=0;
									setTimeout("display()",1000) 
								} 
								display() 
								--> 
								</script> 
								<?php
								}
								?>
							<?php
								}
							?>
							<?php
								if ($func['volume']){
							?>
								<?php
									$arr = array();
									$arr['action'] = "jukebox";
									$arr['subaction'] = "jukebox-command";
									$arr['command'] = "volume";
									$arr['frame'] = $_GET['frame'];
								?>
								<form action="<?php echo urlize($arr); ?>" method="post">
									<input type="hidden" name="action" value="jukebox">
									<input type="hidden" name="subaction" value="jukebox-command">
									<input type="hidden" name="command" value="volume">
									<input type="hidden" name="frame" value="top">
									<select name="jbvol" class="jz_select" style="width:120px;" onChange="submit()">
										<?php
											$vol = "";
											if (isset($_SESSION['jz_jbvol-'. $_SESSION['jb_id']])){
												$vol = $_SESSION['jz_jbvol-'. $_SESSION['jb_id']];
											}
											
											$c=100;
											while($c > 0){
												echo '<option ';
												if ($c == $vol){ echo ' selected '; }
												echo 'value="'. $c. '">Volume '. $c. '%</option>';
												$c = $c-10;
											}
										?>
										<option value="0">Mute</option>
									</select>
								</form>
								<br>
							<?php
								}
							?>
						<?php
							// This closes our if to see if we are streaming or not
							}
						?>
						Playback to:<br>
						<?php
							$arr = array();
							$arr['action'] = "jukebox";
							$arr['subaction'] = "jukebox-command";
							$arr['command'] = "playwhere";
							$arr['frame'] = $_GET['frame'];
						?>
						<form action="<?php echo urlize($arr); ?>" method="post">
							<input type="hidden" name="action" value="jukebox">
							<input type="hidden" name="subaction" value="jukebox-command">
							<input type="hidden" name="command" value="playwhere">
							<input type="hidden" name="frame" value="top">
							<select name="jbplaywhere" class="jz_select" style="width:120px;" onChange="submit()">
								<option <?php if ($jb_playwhere == "stream"){ echo " selected "; } ?>value="stream">Stream</option>
								<?php
									// Now let's get a list of all the jukeboxes that are installed
									for ($i=0; $i < count($jbArr); $i++){
										echo '<option ';
										if ($jb_playwhere == $jbArr[$i]['description']){ echo " selected "; }
										echo 'value="'. $jbArr[$i]['description']. '">'. $jbArr[$i]['description']. '</option>';
									}
								?>
							</select>
						</form>
						<?php
							if ($jb_playwhere <> "stream" and $func['addtype']){
						?>
						<br>
						Add type:<br>
						<?php
							// Now let's set the add type IF it hasn't been set
							if (!isset($_SESSION['jb-addtype'])){
								$_SESSION['jb-addtype'] = "current";
							}
						?>
						<?php
							$arr = array();
							$arr['action'] = "jukebox";
							$arr['subaction'] = "jukebox-command";
							$arr['command'] = "addwhere";
							$arr['frame'] = $_GET['frame'];
						?>
						<form action="<?php echo urlize($arr); ?>" method="post">
						<input type="hidden" name="action" value="jukebox">
						<input type="hidden" name="subaction" value="jukebox-command">
						<input type="hidden" name="command" value="addwhere">
						<select name="addplat" class="jz_select" style="width:142;" onChange="submit()">
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
					<td width="50%" valign="top">
						<?php
							// Let's make sure they aren't streaming
							if ($jb_playwhere == "stream"){return;}
						?>
						<?php
							if ($func['nowplaying']){
						?>
							<nobr><?php echo word("Now Playing:"); ?><br> 
							<?php
								$curTrack = $jb->getCurrentTrackName();
								if (strlen($curTrack)>20){
									$curTrack = substr($curTrack,0,20). "...";
								}
								echo '&nbsp;' . $curTrack. "</nobr>";
							?>
							<br>
						<?php
							}
						?>
						<?php
							if ($func['nexttrack']){
						?>
							<nobr><?php echo word("Next Track:");?> <br>
							<?php
								// Now let's figure out the next track and clean it up
								$fullList = $jb->getCurrentPlaylist();
							if (getCurPlayingTrack()+1 < sizeof($fullList)) {
								$nextTrack = $fullList[getCurPlayingTrack()+1];
							} else {
							  $nextTrack = '-';
							}
								if (stristr($nextTrack,"/")){
									$nArr = explode("/",$nextTrack);
									$nextTrack = $nArr[count($nArr)-1];
								}
								$nextTrack = str_replace(".mp3","",$nextTrack);
								if (strlen($nextTrack)>20){
									$nextTrack = substr($nextTrack,0,20). "...";
								}					
								echo '&nbsp;' . $nextTrack. "</nobr>";
							?>
							<br><br>
						<?php
							}
						?>
						
						<?php
							if ($func['fullplaylist']){
						?>
							Complete Playlist
							<br>
							<?php
								// Now let's get the full playlist back
								$curTrackNum = $jb->getCurrentPlayingTrack();
							?>
							<?php
								$arr = array();
								$arr['action'] = "jukebox";
								$arr['subaction'] = "jukebox-command";
								$arr['command'] = "jumpto";
							?>
							<form action="<?php echo urlize($arr); ?>" method="post">
								<input type="hidden" name="action" value="jukebox">
								<input type="hidden" name="subaction" value="jukebox-command">
								<input type="hidden" name="command" value="jumpto">
								<input type="hidden" name="frame" value="top">
								<select name="jbjumpto" class="jz_select" size="5" style="width:165px;"<?php if ($func['jump']){ echo 'onChange="submit()"'; }?>>
									<?php
										for ($i=0; $i < count($fullList); $i++){
											echo '<option value="'. $i. '"';
											if ($curTrackNum == $i){ echo " selected "; }
											echo '>'. $fullList[$i]. '</option>';
										}
									?>
								</select>
							</form>
						<?php
							}
						?>
					</td>
				</tr>
			</table>
			<?php
		}
		
		/**
		* Draws the block that displays all tracks from an artist on the artist page
		* 
		* @author Ross Carlson
		* @version 01/13/05
		* @since 01/13/05
		* @param $node The node of the item we are viewing
		*/
		function displayAllTracks($node){		
			$arr = array();
			$arr['jz_path'] = $node->getPath("String");
			$viewAll = '<a href="'. urlize($arr). '">View Sampler</a>';
			
			$blocks = new jzBlocks();	
			$blocks->blockHeader($node->getName(). " Sampler", $viewAll);
			$blocks->blockBodyOpen();
			?>
			<table width="100%" cellpadding="2" cellspacing="0" border="0">
				<tr>
					<td width="100%">
						<?php
							$tracks = $node->getSubNodes("tracks",-1,true);
							$blocks->trackTable($tracks, "sample-all");
						?>
					</td>
				</tr>
			</table>
			<?php
			$blocks->blockBodyClose();
		}
		  
	  /**
		* Draws the header for the blocks
		* 
		* @author Ross Carlson
		* @version 01/11/05
		* @since 01/11/05
		* @param string $title The title for the block
		* @param string $right The data that should go in the top right of the block
		*/
		function blockHeader($title = "", $right = ""){
			global $root_dir;
			?>
			<table width="100%" cellspacing="0">
				<tr>
					<td width="5" height="23" class="jz_main_block_topl"><font style="font-size:1px">&nbsp;</font></td>
					<td width="50%" height="23" class="jz_main_block_topm">
						<nobr>
						<strong><?php echo $title; ?></strong>
						</nobr>
					</td>
					<td width="50%" height="23" align="right" class="jz_main_block_topm">
						<nobr>
						<?php echo $right; ?>
						</nobr>
					</td>
					<td width="5" height="23" class="jz_main_block_topr"><font style="font-size:1px">&nbsp;&nbsp;</font></td>
				</tr>
			</table>
			<?php
		}
		
		/**
		* Draws the opening of the table in a block, comes right after the header
		* 
		* @author Ross Carlson
		* @version 01/11/05
		* @since 01/11/05
		*/
		function blockBodyOpen(){
			?>
			<table width="100%" cellspacing="0"><tr><td colspan="4" width="100%" valign="top" class="jz_block_td">
			<table width="100%" cellpadding="2" cellspacing="0" border="0"><tr><td width="100%">
			<?php
		}
		
		/**
		* Draws the close of the table for a block
		* 
		* @author Ross Carlson
		* @version 01/11/05
		* @since 01/11/05
		*/
		function blockBodyclose(){
			?>
			</td></tr></table>
			</td></tr></table>
			<?php
		}
		
		/**
		* Draws a small spacer row between blocks
		* 
		* @author Ross Carlson
		* @version 01/11/05
		* @since 01/11/05
		*/
		function blockSpacer(){
			?><table width="100%" cellpadding="2" cellspacing="0" border="0"><tr><td width="100%" height="10"></td></tr></table><?php
		}
		
	/**
	 * Displays a table of the given tracks.
	 *
	 * @author Ross Carlson
	 * @version 11/30/04
	 * @since 01/11/05
	 * @param array $tracks The array of objects of each track
	 * @param $purpose The type of this track table. One of:
	 * generic|album|search|sample|sample-all
	 */
	  function trackTable($tracks, $purpose = false){
		global $media_dir, $jinzora_skin, $hierarchy, $album_name_truncate, $row_colors, 
		  $img_more, $img_email, $img_rate, $img_discuss, $num_other_albums, $enable_ratings, $this_site, $root_dir;					
	
		if (sizeof($tracks) == 0) return;
		// Let's setup the new display object
		$display = &new jzDisplay();
		
		// Let's figure out our settings:
		// First, set the defaults.
		$showNumbers = true;
		$showArtist = false;
		$showAlbum = false;
		$showCheck = false;
		$showInfo = false;
		$showEmail = false;
		$showRate = false;
		$showDisc = false;
		$albumArtThumbs = false;
		$showAlbumNames = false;
		$trackTruncate = false;
		$showPlayCount = false;
		
		// Now adjust as needed:
		switch ($purpose) {
		case "generic":
		  $showNumbers = false;
		  break;
		case "album":
		  $showCheck = true;
		  $showNumbers = false;
		  $showInfo = true;
		  $showPlayCount = true;
		  $showEmail = true;
		  break;
		case "search":
		  $showArtist = true;
		  break;
		case "sample":
			// We only want to show album thumbs IF this artist has more than 1 album
			$parent = $tracks[0]->getParent(); 
			$gParent = $parent->getParent();
			$nodes = $gParent->getSubNodes("nodes");
			if (count($nodes) > 1){
				$albumArtThumbs = true;
			}
			$showNumbers = false;
			$showAlbum = true;
		  break;
		case "sample-all":
		  $showNumbers = false;
		  $showCheck = true;
		  $showAlbumNames = true;
		  $trackTruncate = 20;
		  break;
		}



		// Do we need to start the form
		if ($showCheck){
			$node = $tracks[0]->getParent();
			?>
			<form name="albumForm" action="<?php echo urlize(); ?>" method="POST">
			<input type="hidden" name="<?php echo jz_encode("action"); ?>" value="<?php echo jz_encode("mediaAction"); ?>">
			<input type="hidden" name="<?php echo jz_encode("jz_path"); ?>" value="<?php echo htmlentities(jz_encode($node->getPath("String"))); ?>">
			<input type="hidden" name="<?php echo jz_encode("jz_list_type"); ?>" value="<?php echo jz_encode("tracks"); ?>">
			<?php
		}
		
		// Now let's setup the big table to display everything
		$i=0;
		  ?>
		  <table class="jz_track_table" width="100%" cellpadding="3" cellspacing="0" border="0">
		 <?php
		 foreach ($tracks as $child) {
		   // First let's grab all the tracks meta data
		   $metaData = $child->getMeta();
		   $album = $child->getParent();
		   if (findPType($album) == "disk") {
		     $album = $album->getParent();
		   }
		   $gParent = $album->getParent();
		   $artist = getInformation($album,"artist");
		   ?>
		   <tr class="<?php echo $row_colors[$i]; ?>">
		   <?php
		   if ($showCheck){
		   ?>
		   <td width="1%" valign="top" class="jz_track_table_songs_td">
		   <input type="checkbox" name="jz_list[]" value="<?php echo jz_encode($child->getPath("String")); ?>">
		   </td>
		   
		   
		   <td width="1%" valign="top" class="jz_track_table_songs_td">
		   <?php echo $display->playButton($child); ?>
		   </td>
		   <td width="1%" valign="top" class="jz_track_table_songs_td">
		   <?php echo $display->downloadButton($child); ?>
		   </td>
		   
		   
		   <?php
		   }
		   // Do they want ratings?
		   if ($enable_ratings == "true"){
		   		echo '<td width="1%" valign="top" class="jz_track_table_songs_td">';
				$display->rateButton($child);
				echo '</td>';
		   }
		   ?>
		  
		   <?php
			if ($showInfo){
				$arr = array();
				$arr['action'] = "popup";
				$arr['ptype'] = "trackinfo";
				$arr['jz_path'] = $child->getPath("String");
				$link = urlize($arr);
		   ?>
		   <td width="1%" valign="top" class="jz_track_table_songs_td">
		   <a href="<?php echo $link; ?>" target="_blank" onclick="openPopup(this, 375, 650); return false;"><?php echo $img_more; ?></a>
		   </td>
		   <?php
			}
		   ?>
		   <?php
		   if ($showEmail){
		   ?>
		   <td width="1%" valign="top" class="jz_track_table_songs_td">
		   <?php
		   	$arr = array();
			$arr['action'] = "playlist";
			$arr['jz_path'] = $child->getPath("String");
			$arr['type'] = "track";
			$link = $this_site. $root_dir. "/". str_replace("&","%26",urlize($arr));

		   	$mailLink = "mailto:?subject=". $artist. " - ". $album->getName(). "&body=Click to play ". 
						$artist. " - ". $album->getName(). ":%0D%0A%0D%0A".
						$link. "%0D%0A%0D%0APowered%20by%20Jinzora%20%0D%0AJinzora%20::%20Free%20Your%20Media%0D%0Ahttp://www.jinzora.org";
		   ?>
		   <a class="jz_track_table_songs_href" href="<?php echo $mailLink; ?>"><?php echo $img_email; ?></a>
		   </td>
		   <?php
		   }
		   ?>
		   <?php
		   if ($showDisc){
		   ?>
		   <td width="1%" valign="top" class="jz_track_table_songs_td">
		   <a class="jz_track_table_songs_href" href=""><?php echo $img_discuss; ?></a>
		   </td>
		   <?php
		   }
		   ?>
		   <td nowrap width="100%" valign="top" class="jz_track_table_songs_td">
		   <?php 
		   // Do they want tiny thumbnails?
		   if ($albumArtThumbs){
			if (($art = $album->getMainArt()) !== false) {		
				$display->link($album,$display->returnImage($art,$album->getName(),25,25,"limit",false,false,"left","2","2"),$album->getName(),"jz_track_table_songs_href");
			}
		   }
		   if ($showArtist !== false) {
			 $j = 0;
			 while ($j < sizeof($hierarchy) && $hierarchy[$j] != 'artist') {
			   $j++;
			 }
			 if ($j < sizeof($hierarchy)) {
			   $parent = $child;
			   while ($parent->getLevel() > $j+1) {
			 $parent = $parent->getParent();
			   }
			   $display->link($parent,$parent->getName(),$parent->getName(),"jz_track_table_songs_href");
			   echo " / ";
			 }
		   }
		   if (!$trackTruncate){
			$tName = $child->getName();
		   } else {
			$tName = returnItemShortName($child->getName(), $trackTruncate);
		   }
		   $display->link($child, $tName, $child->getName(), "jz_track_table_songs_href"); 
		   if ($showAlbum){
			   echo "<br>From: ";
			   $display->link($album, returnItemShortName($album->getName(),20),$album->getName(),"jz_track_table_songs_href"); 
		   }

		   // Do they want ratings?
		   if ($enable_ratings == "true"){
		   		$rating = $display->displayRating($child,true);
				if ($rating){
					echo "<br>". $rating;
				}
				unset($rating);
		   }
		   ?>
		   </td>
		   
		   <?php
		   $lyricsSearch = false;
		   if (isset($_POST['search_type'])){
			if ($_POST['search_type'] == "lyrics"){
				$lyricsSearch = $_POST['search_query'];
			}
		   }
			if (isset($_GET['search_type'])){
			if ($_GET['search_type'] == "lyrics"){
				$lyricsSearch = $_GET['search_query'];
			}
		   }
			if ($lyricsSearch){
				// Now let's get the lyrics back
				$lyrics = $child->getLyrics();
				// Now let's parse it out
				$lyrics = str_replace("Lyrics Provided by: Leo's Lyrics\nhttp://www.leoslyrics.com","",$lyrics);
				$start = strpos(strtolower($lyrics),strtolower($lyricsSearch))-20;
				if ($start < 0){$start=0;}
				$lyrics = "&nbsp; &nbsp; &nbsp; &nbsp;(...". substr($lyrics,$start,strlen($lyricsSearch)+40). "...)";
				$lyrics = highlight($lyrics,$lyricsSearch);
				echo '<td width="6%" align="center" valign="top" class="jz_track_table_songs_td">';
				echo "<nobr>". $lyrics. "</nobr>";
				echo '</td>';
			}
		   ?>
		   
		   
		   <?php   	
			if ($showAlbumNames){
				echo '<td width="1%" align="center" valign="top" class="jz_track_table_songs_td"><nobr>';
				$display->link($gParent, returnItemShortName($gParent->getName(),20),$gParent->getName(),"jz_track_table_songs_href");
				echo '</nobr></td>';
			}
		   ?>
		   
			<?php
				if ($showPlayCount){
					echo '<td width="1%" align="center" valign="top" class="jz_track_table_songs_td">';
					echo $child->getPlayCount();
					echo '</td>';
				}
			?>
		   
		   
		   <td width="6%" align="center" valign="top" class="jz_track_table_songs_td">
		   <nobr> &nbsp; <?php echo convertSecMins($metaData['length']); ?> &nbsp; </nobr>
		   </td>
		   </tr>
		   <?php		
		   $i = 1 - $i;
		   unset($gParent);unset($album);
		 }
		
		// Now let's set a field with the number of checkboxes that were here
		echo "</table>";
		
		// Now let's show the playlist bar if we should
		if ($showCheck){
			$blocks = new jzBlocks();
			$blocks->blockSpacer();
			$blocks->playlistBar();
			echo "</form>";
		}
	  }
	}
?>
