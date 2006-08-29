<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	if (isset($_POST['readTags'])) {
		setcookie("jz_read_tags",$_POST['readTags']);
	} else {
		setcookie("jz_read_tags","false");
	}
	
	echo '<body onLoad="setup8.media_path.focus();"></body>';
	
	// Let's figure out the path stuff so we'll know how/where to include from
	$form_action = setThisPage() . "install=step7";
	$form_action2 = setThisPage() . "install=step8";

	// Now let's include the left
	include_once($include_path. 'install/leftnav.php');
?>
<script language="JavaScript">
<!--
function browseMedia(){
	var sw = screen.width;
	var sh = screen.height;
	var winOpt = "width=250,height=300,left=" + ((sw - 300) / 2) + ",top=" + ((sh - 300) / 2) + ",menubar=no,toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no";
	thisWin = window.open('<?php echo $include_path; ?>install/browse.php?lang=<?php echo $_POST["jz_lang_file"]; ?>&readTags=<?php echo $_POST["readTags"]; ?>','AddMedia',winOpt);
}

function importMedia(){
	if (document.setup8.media_path.value !== ""){
		document.setup8.submit();
	} else {
		alert("<?php echo $word_empty_import_error; ?>");
	}
}
function skipImport(){
	document.skipform.submit();
}
// -->
</script>     
<?php	
	function readAllDirs3($dirName, &$readCtr){
		global $audio_types, $video_types, $word_files_analyzed;
		
		// Let's up the max_execution_time
		ini_set('max_execution_time','6000');
		// Let's look at the directory we are in		
		if (@is_dir($dirName)){
			$d = @dir($dirName);
			if (@is_object($d)){
				while($entry = $d->read()) {
					// Let's make sure we are seeing real directories
					if ($entry == "." || $entry == "..") { continue;}
					if ($readCtr % 100 == 0){ 
						?>
						<script language="javascript">
							fc.innerHTML = '<b><?php echo $readCtr. " ". $word_files_analyzed; ?></b>';									
							-->
						</SCRIPT>
						<?php 
						@flush(); @ob_flush();
					}
					// Now let's see if we are looking at a directory or not
					if (filetype($dirName. "/". $entry) <> "file"){
						// Ok, that was a dir, so let's move to the next directory down
						readAllDirs3($dirName. "/". $entry, $readCtr);
					} else {
						if (preg_match("/\.($audio_types|$video_types)$/i", $entry)){
							$readCtr++;
							$_SESSION['jz_full_counter']++;
						}							
					}			
				}
				// Now let's close the directory
				$d->close();
			}
		}		
	}
?>
      
<div id="main">
	<a href="http://www.jinzora.com" target="_blank"><img src="<?php echo $include_path; ?>install/logo.gif" border="0" align="right" vspace="5" hspace="0"></a>
	<h1><?php echo $word_import_media; ?></h1>
	<p>
	<?php echo $word_import_media_note; ?>
	<form action="<?php echo $form_action; ?>" name="setup8" method="post">
		<?php
			if (isset($_POST['media_path'])){
				if (substr($_POST['media_path'],-1) == '/') {
					$_POST['media_path'] = substr($_POST['media_path'],0,-1);
				}
				if (isset($_POST['all_media_paths'])) {
					$_POST['all_media_paths'] .= "|" . $_POST['media_path'];
				}
				else {
					$_POST['all_media_paths'] = $_POST['media_path'];
				}
			}
		
			$PostArray = $_POST;
			foreach ($PostArray as $key => $val) {
			  if (!stristr($key,"submit") and !stristr($key, "media_path")){
			  	echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
			  }
		    }
			// Did they want to import media?
			if (isset($_POST['media_path'])){
				?>
					<div class="go">
						<span class="goToNext">
							<?php echo $word_importing_media; ?>
						</span>
					</div>
					<?php 
						echo $word_wait_import; 
						echo '<br><br>';
						echo '<strong>';
						$media_dir = str_replace("//","/",str_replace("\\","/",$_POST['media_path']));
						echo $word_importing_media_from. " ". $media_dir; 
						echo '</strong><br>';
						
						ob_flush(); flush();
						
						$_POST['media_path'] = stripSlashes($_POST['media_path']);
						// Now we need to track ALL the media paths they enter
						if (!isset($_SESSION['all_media_paths'])){ $_SESSION['all_media_paths'] = "";}
						// Now let's make sure that's a valid path and is readable
						if (is_dir($_POST['media_path']) and !stristr($_SESSION['all_media_paths'],$_POST['media_path'])){
							$_SESSION['all_media_paths'] .= $_POST['media_path']. "|";
						}
						
						// actually import it:						
						// Now let's set the media types for the import
						$default_art = "folder|cover|mainArt";
						$audio_types = "mp3|ogg|wma|wav|midi|mid|flac|aac|mp4|rm|mpc|m4a|wv";
						$video_types = "avi|wmv|mpeg|mov|mpg|rv";
						$ext_graphic = "jpg|gif|png|jpeg";
						
						// First let's get a listing of ALL files so we'll be able to estimate how long this will take
						// Was this set from the popup?
						if ($_POST['media_length'] <> ""){
							$len = $_POST['media_length'];
						} else {
							//echo "<br><strong>". $word_analyzing_import. '</strong>';
							echo '<div id="filecount"></div>';
							?>
							<script language="javascript">
								fc = document.getElementById("filecount");							
								-->
							</SCRIPT>
							<?php
							$readCtr = 0; $_SESSION['jz_full_counter'] = 0;
							readAllDirs3($media_dir, $readCtr);
							// Now let's see how long we think it will take
							$len = $_SESSION['jz_full_counter'];
						}
						if ($len == 0){$len=1;}
						$_SESSION['jz_import_full_ammount'] = $len;
						$_SESSION['jz_import_start_time'] = time();
						$_SESSION['jz_install_timeLeft'] = 0;
						
						?>
						<script language="javascript">
							fc.innerHTML = '&nbsp;';									
							-->
						</SCRIPT>
						<?php 
						
						// Now let's import
						echo '<div id="importProgress"></div>';
						echo '<div id="importStatus"></div>';
						
						// Now let's know when we started
						$startTime = time();
						if ($media_dir == "" || !is_dir($media_dir)){
							echo "<strong>". $word_dir_invalid. "</strong>";
							$error = true;
						} else {
							if (!@include_once($include_path. 'settings.php')) {
								$str = file_get_contents($include_path. 'install/defaults.php');
								/*
								eval(str_replace("<?php","",str_replace("?>","",$str)));
								eval($content);
								*/
							}
							// Let's setup the object for the HTML updates
							?>
							<script language="javascript">
							d = document.getElementById("importStatus");
							p = document.getElementById("importProgress");							
							-->
							</SCRIPT>
							<?php
								
							// Now let's setup the backend
							$backend = $_POST['backend'];
							$hierarchy = $_POST['hierarchy'];
							require_once($include_path. 'backend/backend.php');
							$jzUSER = new jzUser();
							$root = &new jzMediaNode();
							
							set_time_limit(0);
							// Now let's update the cache
							$_SESSION['jz_import_progress'] = 1;
							$_SESSION['jz_import_full_progress'] = 0;
							$readTags = true;
							if ($_POST['readTags'] == "false"){
								$readTags = false;
							}
							updateNodeCache($root, true, true, false, $readTags, $media_dir);
						}
					?>
					<script language="javascript">
						d = document.getElementById("importStatus");
						p = document.getElementById("importProgress");
						p.innerHTML = '<b><?php echo $word_import_complete; ?></b>';						
						d.innerHTML = '&nbsp;';						
						-->
					</SCRIPT>
					<strong><?php echo $word_import_complete; ?></strong> (<?php echo round(((time() - $startTime)/60),2). " ". $word_minutes; ?>)
					<br><br>
					<?php 
						// Now let's show them how much they imported
						$backend = $_POST['backend'];
						$hierarchy = $_POST['hierarchy'];
						$frontend =  $_POST['frontend'];

						// Now let's get the backend data
						require_once($include_path. 'backend/backend.php');
						$root_node = &new jzMediaNode();
						
						if (distanceTo('genre') !== false)
							$genres = $root_node->getSubNodeCount('nodes',distanceTo('genre'));
						if (distanceTo('artist') !== false)
							$artists = $root_node->getSubNodeCount('nodes',distanceTo('artist'));
						if (distanceTo('album') !== false)
							$albums = $root_node->getSubNodeCount('nodes',distanceTo('album'));
						if (distanceTo('track') !== false)
							$disks = $root_node->getSubNodeCount('nodes',distanceTo('track'));
						$tracks = $root_node->getSubNodeCount('tracks',-1);
						$genres = isset($genres) ? $genres : "false";
						$artists = isset($artists) ? $artists : "false";
						$albums = isset($albums) ? $albums : "false";
						$tracks = isset($tracks) ? $tracks : "false";
						$disks = isset($disks) ? $disks : "false";
						
						echo $word_genres. ": ". $genres. "<br>";
						echo $word_artists. ": ". $artists. "<br>";
						echo $word_albums. ": ". $albums. "<br>";						
						echo $word_tracks. ": ". $tracks. "<br><br>";
						
						echo $word_complete_message; 
					?>
				<?php
					echo str_replace("|","<br>",$_SESSION['all_media_paths']);
			}
		
			if (isset($_POST['database_server']) and !isset($_POST['media_path'])){
				?>
				<div class="go">
					<span class="goToNext">
						<?php echo $word_database_connection; ?>
					</span>
				</div>
				<br>
				<table width="100%" cellspacing="0" cellpadding="3" border="0">
					<tr>
						<td class="td" width="40%" align="left" valign="top">
							<?php echo $word_verifying_connection; ?>
						</td>
						<td class="td" width="1">&nbsp;</td>
						<td class="td" width="60%" align="left">
							<font color="green"><?php echo $word_successful; ?></font>
						</td>
					</tr>
					<tr>
						<td class="td" width="40%" align="left" valign="top">
							<?php echo $word_creating_database; ?>
						</td>
						<td class="td" width="1">&nbsp;</td>
						<td class="td" width="60%" align="left">
							<font color="green"><?php echo $word_already_exists; ?></font>
						</td>
					</tr>
					<tr>
						<td class="td" width="40%" align="left" valign="top">
							<?php echo $word_creating_tables; ?>
						</td>
						<td class="td" width="1">&nbsp;</td>
						<td class="td" width="60%" align="left">
							<font color="green"><?php echo $word_already_exists; ?></font>
						</td>
					</tr>
				</table>
				<?php
			}
		?>
		<div class="go">
			<span class="goToNext">
				<?php echo $word_import_media; ?>
			</span>
		</div>
		<br>
		<table width="100%" cellspacing="0" cellpadding="3" border="0">
			<tr>
				<td class="td" width="25%" align="left" valign="top">
					<?php echo $word_server_directory; ?>
				</td>
				<td class="td" width="1">&nbsp;</td>
				<td class="td" width="75%" align="left">
					<input type="text" name="media_path" size="30"> 
					<?php
						// Let's make sure we're not in CPGNuke mode...
						if ($_POST['cms_type'] <> "cpgnuke"){
							echo '<input onClick="browseMedia();" type="button" value="Browse">';
						}
					?>
					<input type="hidden" name="media_length" value="">
					<br>
					<?php echo $word_server_directory_note; ?>
				</td>
			</tr>
		</table>
		<br><br>
		<div class="go">
			<span class="goToNext">
				&nbsp; <input type="button" onClick="importMedia();" name="import_media" class="submit" value="<?php echo $word_import_media; ?>"> 
				</form>
				<form action="<?php echo $form_action2; ?>" name="setup9" method="post">
					<?php
						$PostArray = $_POST;
						foreach ($PostArray as $key => $val) {
						   if (!stristr($key,"submit") and !stristr($key, "media_path")){
							echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
						  }
					   }
					   
					   // Now let's make sure they have imported at least SOME media
					   if (isset($_POST['media_path'])){
					   	echo '<br>&nbsp; <input type="submit" name="submit_step8" class="submit" value="'. $word_proceed_save_config. '">';
					   } else {
						 	?>
							<!--
							<form action="<?php echo $form_action2; ?>" name="skipform" method="post">
								&nbsp; <input type="button" onClick="skipImport();" name="skip_import" class="submit" value="<?php echo $word_skip_import_media; ?>" onmouseover="return overlib('<?php echo $word_skip_import_message; ?>');" onmouseout="return nd();"> 
								<?php
									$PostArray = $_POST;
									foreach ($PostArray as $key => $val) {
										 if (!stristr($key,"submit") and !stristr($key, "media_path")){
										echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
										}
									 }
								?>
							</form>
							-->
							<?php
						 }
					?>	
				</form>
			</span>
		</div>
	</div>
<?php
	// Now let's include the top
	include_once($include_path. 'install/footer.php');
?>
