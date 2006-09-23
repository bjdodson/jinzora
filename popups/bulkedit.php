<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');

/**
* Let's us bulk edit an entire album
*
* @author Ross Carlson
* @since 03/07/05
* @version 03/07/05
* @param $node The node we are looking at
*
**/
global $row_colors, $allow_filesystem_modify, $clip_length, $clip_start, $root_dir, $lame_opts, $jzSERVICES, $node;

$this->displayPageTop("", word("Bulk edit") . ": " . $node->getName());
$this->openBlock();

if ($allow_filesystem_modify == "false") {
	echo 'You do not allow your filesystem to be modified.<br><br>' .
	'Please see <a target="_blank" href="http://www.jinzora.com/pages.php?pn=support&sub=faq#10">our FAQ</a> on this issue.';
	$this->closeBlock();
	exit ();
}

// Now let's get 1 track to show as a sample
$tracks = $node->getSubNodes("tracks", -1);

// Did they bulk edit and do a replace?
if (isset ($_POST['edit_replace_close']) or isset ($_POST['edit_replace']) or isset ($_POST['edit_create_clips']) or isset ($_POST['edit_delete_clips']) or isset ($_POST['edit_create_lofi']) or isset ($_POST['edit_delete_lofi']) or isset ($_POST['edit_fix_case'])) {

	if (isset ($_POST['edit_create_lofi'])) {
		// Ok, let's give them status
		echo "<center>" . word('Resampling files, please stand by...') . "<br><br>";
		echo '<img src="' . $root_dir . '/style/images/convert.gif?' . time() . '"></center><br>';
		echo '<div id="path"></div>';
		echo '<div id="oldname"></div>';
		echo '<div id="newname"></div>';
		echo '<div id="status"></div>';
		flushdisplay();
	} else {
		// Ok, let's give them status
		echo word("Modifying files, please stand by...") . "<br><br>";
		echo '<div id="path"></div>';
		echo '<div id="oldname"></div>';
		echo '<div id="newname"></div>';
		echo '<div id="status"></div>';
	}
?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				p = document.getElementById("path");
				o = document.getElementById("oldname");
				n = document.getElementById("newname");
				s = document.getElementById("status");
				-->
			</SCRIPT>
			<?php

	$updateNode = true;
	// First let's get the list of actual files
	for ($i = 0; $i < count($tracks); $i++) {
		// Let's get all the data we'll need
		$oArr = explode("/", $tracks[$i]->getDataPath("String"));
		$oldName = $oArr[count($oArr) - 1];
		unset ($oArr[count($oArr) - 1]);
		$path = implode("/", $oArr);
		$oldPath = $tracks[$i]->getDataPath("String");

		// Now let's set this based on the tool
		if (isset ($_POST['edit_replace_close']) or isset ($_POST['edit_replace'])) {
			$newName = trim(str_replace($_POST['edit_file_search'], $_POST['edit_file_replace'], $oldName));
			$newPath = $path . "/" . $newName;
			// Ok, let's copy then kill
			$error = word("Failed!");
			if (@ rename($oldPath, $newPath)) {
				$error = word("Success!");
			}
			$meta['title'] = $newName;
			$tracks[$i]->setMeta($meta);
			$updateNode = true;
		}
		// Fixing case?
		if (isset ($_POST['edit_fix_case'])) {
			$newName = ucwords($oldName);
			$newPath = $path . "/" . $newName;
			// Ok, let's copy then kill
			$error = word("Failed!");
			if (copy($oldPath, $newPath . ".tmp")) {
				if (unlink($oldPath)) {
					if (rename($newPath . ".tmp", $newPath)) {
						$error = word("Success!");
					}
				}
			}
		}
		// Creating clips?
		if (isset ($_POST['edit_create_clips'])) {
			if (substr($oldName, -4) == ".mp3" and is_file($oldPath) and !stristr($oldName, ".clip.")) {
				$newName = substr($oldName, 0, -3) . 'clip.mp3';
				$newPath = $path . "/" . $newName;
				// Now let's write out the new clip track
				$handle = fopen($newPath, "w");
				fwrite($handle, substr(file_get_contents($oldPath), ($clip_start * 25000), ($clip_length * 25000)));
				fclose($handle);

				// Now let's write the meta to this track
				$tMeta = new jzMediaTrack($tracks[$i]->getPath("String"));
				$meta = $tMeta->getMeta();
				// Now let's write this
				$jzSERVICES->setTagData($newPath, $meta);

				$error = word("Success!");
			} else {
				$error = word("Failed - not an MP3 file!");
			}
		}
		// Deleting clips?
		if (isset ($_POST['edit_delete_clips'])) {
			// Ok, let's unlink .clip.mp3 tracks
			if (substr($oldPath, -9) == ".clip.mp3") {
				$error = "Failed!";
				if (unlink($oldPath)) {
					$newName = word("...deleted...");
					$error = word("Success!");
				}
			} else {
				$error = word("Skipping, not a clip track...");
			}
		}
		// Creating a lofi resample?
		if (isset ($_POST['edit_create_lofi'])) {
			if (!stristr($oldName, ".mp3") or stristr($oldName, ".clip.")) {
				continue;
			}
			$error = "Lo-fi file create failed!";
			// Now let's encode
			$newName = substr($oldName, 0, -3) . 'lofi.mp3';
			$newPath = $path . "/" . $newName;
			$command = $lame_opts . ' "' . $oldPath . '" "' . $newPath . '"';
?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						p.innerHTML = '<nobr><?php echo word("Path"); ?>: <?php echo $path; ?></nobr>';
						o.innerHTML = '<nobr><?php echo word("Old Name"); ?>: <?php echo $oldName; ?></nobr>';
						n.innerHTML = '<nobr><?php echo word("New Name"); ?>: <?php echo $newName; ?></nobr>';
						s.innerHTML = '<nobr><?php echo word("Status"); ?>: creating...</nobr>';
						-->
					</SCRIPT>
					<?php

			flushdisplay();
			$output = "";
			$returnvalue = "";
			if (exec($command, $output, $returnvalue)) {
				$error = word("Lo-fi file created successfully!");
				// Now we need to get the meta data from the orginal file so we can write it to the new file
				$tMeta = new jzMediaTrack($tracks[$i]->getPath("String"));
				$meta = $tMeta->getMeta();
				// Now let's write this
				$jzSERVICES->setTagData($newPath, $meta);
			}
		}
		// Are we deleting clips?
		if (isset ($_POST['edit_delete_lofi'])) {
			// Ok, let's unlink .clip.mp3 tracks
			if (substr($oldPath, -9) == ".lofi.mp3") {
				$error = word("Failed!");
				if (unlink($oldPath)) {
					$newName = word("...deleted...");
					$error = word("Success!");
				}
			} else {
				$error = word("Skipping, not a lofi track...");
			}
		}

		// Now let's status
?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					p.innerHTML = '<nobr><?php echo word("Path"); ?>: <?php echo $path; ?></nobr>';
					o.innerHTML = '<nobr><?php echo word("Old Name"); ?>: <?php echo $oldName; ?></nobr>';
					n.innerHTML = '<nobr><?php echo word("New Name"); ?>: <?php echo $newName; ?></nobr>';
					s.innerHTML = '<nobr><?php echo word("Status"); ?>: <?php echo $error; ?></nobr>';
					-->
				</SCRIPT>
				<?php

		flushdisplay();
	}

	// Now we need to update the node			
?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				p.innerHTML = '<center><?php echo word("Updating the node cache..."); ?></center>';
				o.innerHTML = '&nbsp;';
				n.innerHTML = '&nbsp;';
				s.innerHTML = '&nbsp;';
				-->
			</SCRIPT>
			<?php

	flushdisplay();
	if ($updateNode) {
		//$node->updateCache(true, false, false, true);
		updateNodeCache($node, true, false, true);
	}

	// Now we need to update the node			
?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				p.innerHTML = '<center><?php echo word("Complete!"); ?></center>';
				o.innerHTML = '&nbsp;';
				n.innerHTML = '&nbsp;';
				s.innerHTML = '&nbsp;';
				-->
			</SCRIPT>
			<?php

	flushdisplay();

	if (isset ($_POST['edit_replace_close'])) {
		$this->closeWindow(true);
	}
	echo '<center>';
	$this->closeButton(true);
	exit ();
}

// This is the display for this tool

// Let's give them the options to update
$arr = array ();
$arr['action'] = "popup";
$arr['ptype'] = "bulkedit";
$arr['jz_path'] = $_GET['jz_path'];
echo '<form action="' . urlize($arr) . '" method="POST">';
$i = 0;
?>
		<table width="100%" cellpadding="3" cellspacing="0">
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td>
					<nobr>
					<?php echo word("Sample Track"); ?>:
					</nobr>
				</td>
				<td>
					<?php

$pArr = explode("/", $tracks[0]->getFilePath());
echo $pArr[count($pArr) - 1];
?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td>
					<nobr>
					<?php echo word("String Replace"); ?>:
					</nobr>
				</td>
				<td>
					<?php echo word("Search"); ?><br>
					<input type="text" name="edit_file_search" size="30" class="jz_input"><br>
					<?php echo word("Replace"); ?><br>
					<input type="text" name="edit_file_replace" size="30" class="jz_input"><br><br>
					<input type="submit" name="edit_replace" value="<?php echo word("Replace"); ?>:" class="jz_submit">
					<input type="submit" name="edit_replace_close" value="<?php echo word("Replace & Close"); ?>:" class="jz_submit"><br><br>
					</form>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td>

				</td>
				<td>
					<?php

$tarr = array ();
$tarr['action'] = "popup";
$tarr['ptype'] = "retagger";
$tarr['jz_path'] = $node->getPath("String");
echo '<form action="' . urlize($tarr) . '" method="POST">';
?>
					<input type="submit" name="edit_retag_tracks" value="<?php echo word("Retag All Tracks"); ?>" class="jz_submit">
					</form>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td>

				</td>
				<td>
					<?php

$arr = array ();
$arr['action'] = "popup";
$arr['ptype'] = "autorenumber";
$arr['jz_path'] = $_GET['jz_path'];
echo '<form action="' . urlize($arr) . '" method="POST">';
echo '<input type="submit" name="edit_show_renumber" value="' . word('Renumber Tracks') . '" class="jz_submit">';
echo "</form>";
?>
				</td>
			</tr>
			<?php

// Let's give them the options to update
$arr = array ();
$arr['action'] = "popup";
$arr['ptype'] = "bulkedit";
$arr['jz_path'] = $_GET['jz_path'];
echo '<form action="' . urlize($arr) . '" method="POST">';
?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td>

				</td>
				<td>
					<input type="submit" name="edit_fix_case" value="<?php echo word("Fix Filename Case"); ?>" class="jz_submit">
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td>

				</td>
				<td>
					<input type="submit" name="edit_create_clips" value="<?php echo word("Create Clips"); ?>" class="jz_submit">
					<input type="submit" name="edit_delete_clips" value="<?php echo word("Delete Clips"); ?>" class="jz_submit">
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td>

				</td>
				<td>
					<input type="submit" name="edit_create_lofi" value="<?php echo word("Create LoFi Tracks"); ?>" class="jz_submit">
					<input type="submit" name="edit_delete_lofi" value="<?php echo word("Delete LoFi Tracks"); ?>" class="jz_submit">
				</td>
			</tr>
		</table>
		</form>
		<br><br><center>
		<?php

$this->closeButton();
$this->closeBlock();
?>
