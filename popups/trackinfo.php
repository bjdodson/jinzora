<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');


/**
* Shows track details
* 
* @author Ross Carlson
* @version 01/25/05
* @since 01/25/05
* @param $node The node we are looking at
*/

global $row_colors, $jzSERVICES, $jzUSER, $lame_opts, $root_dir, $allow_filesystem_modify, $allow_id3_modify, $backend, $short_date;
if (is_string($track)) {
	$track = new jzMediaTrack($track);
}

// Ok, now we need to see if they did something with this form
if (isset ($_POST['justclose'])) {
	$this->closeWindow(false);
}
if (isset ($_POST['closeupdate']) or isset ($_POST['updatedata'])) {
	// Ok, they are updating this track so let's do it
	// Let's create our object so we can get the full path and meta
	$track = new jzMediaTrack($_GET['jz_path']);
	$fname = $track->getDataPath("String");

	if ($allow_id3_modify == "true") {
		$fileAvailable = @ fopen($fname, 'r+');
		if (!$fileAvailable) {
			$writeback_message = word("Could not write to file %s. This track's ID3 tag has not been modified.'", $fname);
		} else {
			$writeback_message = word("Metadata for %s has been stored in Jinzora and this file's ID3 tag.'", $track->getName());
		}
	} else {
		$writeback_message = word("Metadata for %s has been updated in Jinzora. To update ID3 tags, please enable \$allow_id3_modify.", $track->getName());
	}

	$meta = $track->getMeta();

	// Now we need to set the meta we want to rewrite
	$meta['title'] = $_POST['edit_title'];
	$meta['artist'] = $_POST['edit_artist'];
	$meta['album'] = $_POST['edit_album'];
	$meta['number'] = $_POST['edit_number'];
	$meta['genre'] = $_POST['edit_genre'];
	$meta['comment'] = $_POST['edit_comment'];
	$meta['lyrics'] = $_POST['edit_lyrics'];

	// Now let's write this
	$track->setMeta($meta);

	// Now let's write the long description if they had one
	$track->addDescription($_POST['edit_long_desc']);
	$track->addShortDescription($_POST['edit_comment']);

	// Now let's update the play count
	$track->setPlayCount($_POST['edit_plays']);

	// Now let's update the cache
	//$path = $track->getPath();
	//unset($path[count($path)-1]);
	//$path = implode("/",$path);
	//$node = new jzMediaNode($path);
	//$node->updateCache(true, false, false, true);

	// Now do we need to close out?
	if (isset ($_POST['closeupdate'])) {
		$this->closeWindow(true);
	}
}
if (isset ($_POST['createlowfi'])) {
	// First let's display the top of the page and open the main block
	$this->displayPageTop("", "Resampling Track");
	$this->openBlock();

	// Let's get all the data we'll need
	$oArr = explode("/", $track->getDataPath("String"));
	$oldName = $oArr[count($oArr) - 1];
	unset ($oArr[count($oArr) - 1]);
	$path = implode("/", $oArr);
	$oldPath = $track->getDataPath("String");

	if (!stristr($oldName, ".mp3") or stristr($oldName, ".clip.")) {
		continue;
	}
	$error = "Lo-fi file create failed!";
	// Now let's encode
	$newName = substr($oldName, 0, -3) . 'lofi.mp3';
	$newPath = $path . "/" . $newName;
	$command = $lame_opts . ' "' . $oldPath . '" "' . $newPath . '"';
	// Ok, let's give them status
	echo "<center>" . word("Resampling track, please stand by...") . "<br><br>";
	echo '<img src="' . $root_dir . '/style/images/convert.gif?' . time() . '"></center><br>';
	echo '<div id="path"></div>';
	echo '<div id="oldname"></div>';
	echo '<div id="newname"></div>';
	echo '<div id="status"></div>';
	flushdisplay();
?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					p = document.getElementById("path");
					o = document.getElementById("oldname");
					n = document.getElementById("newname");
					s = document.getElementById("status");
					p.innerHTML = '<nobr><?php echo word("Path"); ?>: <?php echo $path; ?></nobr>';
					o.innerHTML = '<nobr><?php echo word("Old Name"); ?>: <?php echo $oldName; ?></nobr>';
					n.innerHTML = '<nobr><?php echo word("New Name"); ?>: <?php echo $newName; ?></nobr>';
					s.innerHTML = '<nobr><?php echo word("Status: creating..."); ?></nobr>';
					-->
				</SCRIPT>
				<?php

	flushdisplay();
	$output = "";
	$returnvalue = "";
	if (exec($command, $output, $returnvalue)) {
?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						s = document.getElementById("status");
						s.innerHTML = '<nobr><?php echo word("Status: updating tags..."); ?></nobr>';
						-->
					</SCRIPT>
					<?php

		flushdisplay();
		// Now we need to get the meta data from the orginal file so we can write it to the new file
		$tMeta = new jzMediaTrack($track->getPath("String"));
		$meta = $tMeta->getMeta();
		// Now let's write this
		$jzSERVICES->setTagData($newPath, $meta);
?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						p = document.getElementById("path");
						o = document.getElementById("oldname");
						n = document.getElementById("newname");
						s = document.getElementById("status");
						p.innerHTML = '<?php echo word("Complete!"); ?>';
						o.innerHTML = '&nbsp;';
						n.innerHTML = '&nbsp;';
						s.innerHTML = '&nbsp;';
						-->
					</SCRIPT>
					<?php

	}
	echo '<br><br><center>';
	$this->closeButton();
	exit ();
}
if (isset ($_POST['createclip'])) {
	exit ();
}

// Ok, now we need to create an object from the path so we can read its data
$fname = $track->getDataPath("String");
//$meta = $jzSERVICES->getTagData($fname);
$meta = $track->getMeta();

// First let's display the top of the page and open the main block
$this->displayPageTop("", word("Track Details") . ": " . $meta['title']);
$this->openBlock();
if (checkPermission($jzUSER, 'admin', $track->getPath("String"))) {
	if ($allow_id3_modify == "false" && !isset ($writeback_message)) {
		echo '<p><i>' . word("Note: You must have allow_id3_modify enabled if you want Jinzora to manage your ID3 tags.") . '</i></p><br>';
	}
	if (isset ($writeback_message)) {
		echo '<p><b>' . $writeback_message . "</b></p><br>";
	}
}
// Now let's display the details
$i = 1;
$arr = array ();
$arr['action'] = "popup";
$arr['ptype'] = "trackinfo";
$arr['jz_path'] = $_GET['jz_path'];
echo '<form action="' . urlize($arr) . '" method="POST">';
?>
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('File Name'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<?php echo $meta['filename']; ?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('Track Number'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<input type="input" class="jz_input" name="edit_number" value="<?php echo $meta['number']; ?>" size="3">
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('Track Name'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<input type="input" class="jz_input" name="edit_title" value="<?php echo $meta['title']; ?>" size="30">
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('Artist'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<input type="input" class="jz_input" name="edit_artist" value="<?php echo $meta['artist']; ?>" size="30">
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('Album'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<input type="input" class="jz_input" name="edit_album" value="<?php echo $meta['album']; ?>" size="30">
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('Genre'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<input type="input" class="jz_input" name="edit_genre" value="<?php echo $meta['genre']; ?>" size="30">
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('Track Length'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<?php echo convertSecMins($meta['length']); ?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('Bit Rate'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<?php echo $meta['bitrate']; ?> kbps
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('Sample Rate'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<?php echo $meta['frequency']; ?> kHz
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('File Size'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<?php echo $meta['size']; ?> Mb
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('File Date'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<?php echo date($short_date,$track->getDateAdded()); ?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('ID3 Description'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<input type="input" class="jz_input" name="edit_comment" value="<?php echo $meta['comment']; ?>" size="30">
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('Thumbnail'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<input type="file" class="jz_input" name="edit_thumbnail" size="22">
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="middle">
					<nobr>
						<?php echo word('Long Description'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<textarea class="jz_input" name="edit_long_desc" style="width: 195px" rows="5"><?php echo $track->getDescription($_POST['edit_long_desc']); ?></textarea>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('Plays'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<input type="input" class="jz_input" name="edit_plays" value="<?php echo $track->getPlayCount(); ?>" size="3">
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="100%" valign="top" colspan="2" align="center">
					<?php

if ($meta['lyrics']) {
	echo '<div align="left">' . word('Lyrics') . ':</div><textarea name="edit_lyrics" class="jz_input" rows="20" cols="45" class="jz_input">' . $meta['lyrics'] . '</textarea>';
} else {
	$lyrics = $jzSERVICES->getLyrics($track);
	if (!(($lyrics === false) || ($lyrics == ""))) {
		$meta2 = array ();
		$meta2['lyrics'] = $lyrics;
		$track->setMeta($meta2);
		echo '<div align="left">' . word('Lyrics') . ':</div><textarea name="edit_lyrics" class="jz_input" rows="20" cols="45" class="jz_input">' . $lyrics . '</textarea>';
	} else
		if (checkPermission($jzUSER, "admin", $track->getPath("String"))) {
			echo '<div align="left">' . word('Lyrics') . ':</div><textarea name="edit_lyrics" class="jz_input" rows="20" cols="45" class="jz_input"></textarea>';
		}

}
?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="100%" valign="top" colspan="2" align="center">
				    <?php if (checkPermission($jzUSER,"admin",$track->getPath("String"))) {?>
						<input type=submit class="jz_submit" name="<?php echo jz_encode('closeupdate'); ?>" value="<?php echo word('Update & Close'); ?>">
						<input type=submit class="jz_submit" name="<?php echo jz_encode('updatedata'); ?>" value="<?php echo word('Update'); ?>">
						<br><br>
						<?php

// We can only do this if they allow filesystem modify
if ($allow_filesystem_modify == "true") {
?>
						<input type=submit class="jz_submit" name="<?php echo jz_encode('createlowfi'); ?>" value="<?php echo word('Create Lo-Fi'); ?>">  
						<input type=submit class="jz_submit" name="<?php echo jz_encode('createclip'); ?>" value="<?php echo word('Create Clip'); ?>"> 
						<br><br>
						<?php } ?>
					<?php } ?>
					<input type=submit class="jz_submit" name="<?php echo jz_encode('justclose'); ?>" value="<?php echo word('Close'); ?>">
					<br><br>
				</td>
			</tr>
		</table>
		</form>
		<?php


	$this->closeBlock();
?>
