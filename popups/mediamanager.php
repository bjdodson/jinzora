<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');

/**
* Displays the media management tools - useful for MANY media functions
*
* @author Ross Carlson
* @since 7/01/05
* @version 7/01/05
* @param $node The node that we are viewing
**/
global $include_path, $jz_lang_file, $root_dir, $backend, $media_dirs, $default_art, $audio_types, $video_types, $ext_graphic;

// Let's start the page header
$this->displayPageTop("", word("Media Manager"));
$this->openBlock();

// Now let's see if they wanted to do something
if (isset ($_GET['sub_action'])) {
	// Ok, now what did they want to do?
	switch ($_GET['sub_action']) {
		case "delmediadir" :
			// Ok, now they wanted to wack this node manually
			echo "To Do :-)";
			break;
		case "addmediadir" :
			// Ok, did they already want to add a directory?
			if (isset ($_POST['edit_media_path'])) {
				// Ok, they want to import so let's do it...
				// First let's resize so we've got some space
?>
						<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT"><!--\
							window.resizeTo(600,400)
						-->
						</SCRIPT>
						<?php

				echo word("Please wait while we import your media...");
				echo '<br><br>';
				echo '<strong>';
				$media_dir = str_replace("//", "/", str_replace("\\", "/", $_POST['edit_media_path']));
				echo word("Importing media from:") . " " . $media_dir;
				echo '</strong><br>';

				flushdisplay();

				$_POST['media_path'] = stripSlashes($_POST['edit_media_path']);
				// Now we need to track ALL the media paths they enter
				if (!isset ($_SESSION['all_media_paths'])) {
					$_SESSION['all_media_paths'] = "";
				}
				// Now let's make sure that's a valid path and is readable
				if (is_dir($_POST['media_path']) and !stristr($_SESSION['all_media_paths'], $_POST['media_path'])) {
					$_SESSION['all_media_paths'] .= $_POST['edit_media_path'] . "|";
				}

				// actually import it:						

				// First let's get a listing of ALL files so we'll be able to estimate how long this will take
				// Was this set from the popup?
				if ($_POST['edit_media_length'] <> "") {
					$len = $_POST['edit_media_length'];
				} else {
					//echo "<br><strong>". $word_analyzing_import. '</strong>';
					echo '<div id="filecount"></div>';
?>
							<script language="javascript">
								fc = document.getElementById("filecount");							
								-->
							</SCRIPT>
							<?php

					$readCtr = 0;
					$_SESSION['jz_full_counter'] = 0;
					readAllDirs2($media_dir, $readCtr);
					// Now let's see how long we think it will take
					$len = $_SESSION['jz_full_counter'];
				}
				if ($len == 0) {
					$len = 1;
				}
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
				if ($media_dir == "" || !is_dir($media_dir)) {
					echo "<strong>" . word("Invalid Directory!") . "</strong>";
					$error = true;
				} else {
					// Let's setup the object for the HTML updates
?>
							<script language="javascript">
							d = document.getElementById("importStatus");
							p = document.getElementById("importProgress");							
							-->
							</SCRIPT>
							<?php

					set_time_limit(0);
					// Now let's update the cache
					$_SESSION['jz_import_progress'] = 1;
					$_SESSION['jz_import_full_progress'] = 0;

					// Now do they want to read the tags?
					$readTags = false;
					if ($backend <> "database") {
						$readTags = true;
					}

					// Now let's set the new media_dirs variable
					$newMediaDirs = $media_dirs . "|" . $media_dir;

					// Now we need to write this to the settings file
					if (writeSetting("media_dirs", $newMediaDirs, $include_path . "settings.php")) {
						// Let's create an empty root node
						$root = & new jzMediaNode();
						//$root->updateCache(true,$media_dir,true,false,$readTags);
						updateNodeCache($root, true, true, false, $readTags, $media_dir);
					} else {
?>
								<script language="javascript">
									alert("There was an error writing to your settings file at <?php echo $include_path; ?>settings.php");
									-->
								</SCRIPT>
								<?php

						exit ();
					}
				}
?>
					<script language="javascript">
						d = document.getElementById("importStatus");
						p = document.getElementById("importProgress");
						p.innerHTML = '<?php echo word("Import Complete!"); ?></strong> (<?php echo round(((time() - $startTime)/60),2). " ". word("minutes"); ?>)';						
						d.innerHTML = '<br><?php echo "<strong>". word("Import Stats"). "</strong><br>"; ?>';						
						-->
					</SCRIPT>
					<?php

				// Now let's show them how much they imported
				$root_node = & new jzMediaNode();

				if (distanceTo('genre') !== false)
					$genres = $root_node->getSubNodeCount('nodes', distanceTo('genre'));
				if (distanceTo('artist') !== false)
					$artists = $root_node->getSubNodeCount('nodes', distanceTo('artist'));
				if (distanceTo('album') !== false)
					$albums = $root_node->getSubNodeCount('nodes', distanceTo('album'));

				$tracks = $root_node->getSubNodeCount('tracks', -1);
				$genres = isset ($genres) ? $genres : "false";
				$artists = isset ($artists) ? $artists : "false";
				$albums = isset ($albums) ? $albums : "false";
				$tracks = isset ($tracks) ? $tracks : "false";

				echo word("Genres") . ": " . $genres . "<br>";
				echo word("Artists") . ": " . $artists . "<br>";
				echo word("Albums") . ": " . $albums . "<br>";
				echo word("Tracks") . ": " . $tracks . "<br><br>";
?>
						<script language="javascript">
							opener.location.reload(true);
							-->
						</SCRIPT>
						<?php

			} else {
				// Ok, they wanted to add media, let's show them the form
				// Let's setup the form for the user to choose from
				$arr = array ();
				$arr['action'] = "popup";
				$arr['ptype'] = "mediamanager";
				$arr['sub_action'] = "addmediadir";
				$arr['jz_path'] = $node->getPath('String');
?>
						<script language="JavaScript">
						<!--
							function browseMedia(){
								var sw = screen.width;
								var sh = screen.height;
								var winOpt = "width=250,height=300,left=" + ((sw - 300) / 2) + ",top=" + ((sh - 300) / 2) + ",menubar=no,toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no";
								thisWin = window.open('<?php echo $root_dir; ?>/install/browse.php?lang=<?php echo $jz_lang_file; ?>&prefix=edit	','AddMedia',winOpt);
							}
						// -->
						</script>
						<form action="<?php echo urlize($arr); ?>" method="POST" name="setup8">
							<center>
								<input type="text" name="edit_media_path" size="30"> 
								<input type="hidden" name="edit_media_length" value="">
								<input onClick="browseMedia();" type="button" value="Browse">
								<br><br>
								<input type="submit" name="edit_import_media_dir" value="<?php echo word("Import Directory"); ?>" class="jz_submit">
							</center>
						</form>					
						<?php

			}
			break;
	}
}

$arr = array ();
$arr['action'] = "popup";
$arr['ptype'] = "mediamanager";
$arr['jz_path'] = $node->getPath("string");

$arr['sub_action'] = "addmediadir";
echo "<center>";
echo '<a href="' . urlize($arr) . '">' . word("Add Media Directory") . '</a>';

$arr['sub_action'] = "delmediadir";
//echo " | ";
//echo '<a href="'. urlize($arr). '">'. word("Delete Media"). '</a>';

echo "<br><br><br>";
$this->closeButton();
echo "</center>";

$this->openBlock();
?>
