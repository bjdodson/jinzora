<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
* This tools lets us automatically renumber the tracks for an album
*
* @author Ross Carlson
* @since 03/07/05
* @version 03/07/05
* @param $node The node we are looking at
*
**/
global $allow_filesystem_modify, $jzSERVICES, $node;

$this->displayPageTop("", word("Renumbering tracks for") . ": " . $node->getName());
$this->openBlock();

if (!isset ($_GET['renumber_type'])) {
	$arr = array ();
	$arr['action'] = "popup";
	$arr['ptype'] = "autorenumber";
	$arr['jz_path'] = $_GET['jz_path'];

	echo '<table><tr><td>';
	$arr['renumber_type'] = "mb";
	echo '<a href="' . urlize($arr) . '">' . word("From Musicbrainz") . '</a>';
	echo '</td></tr><tr><td>';
	$arr['renumber_type'] = "fn";
	echo '<a href="' . urlize($arr) . '">' . word("From filenames") . '</a>';
	echo '</td></tr></table>';
}
if ($_GET['renumber_type'] == "fn") {
	$albums = array ();
	if (sizeof($albums = $node->getSubNodes("nodes")) == 0) {
		$albums[] = $node;
	}
	foreach ($albums as $album) {
		$tracks = $album->getSubNodes("tracks");
		sortElements($tracks, "filename");
		for ($i = 0; $i < sizeof($tracks); $i++) {
			$meta = $tracks[$i]->getMeta();
			$meta['number'] = $i +1;
			$tracks[$i]->setMeta($meta);
		}
	}
	echo 'Done!';
	echo '<br><br><center>';
	$this->closeButton(true);
	exit ();
} else
	if ($_GET['renumber_type'] == "mb") {
		$jzSERVICES->loadService("metadata", "musicbrainz");
		// Did they submit the form?
		if (isset ($_POST['edit_renumber'])) {
			echo word('Renumbing tracks, please stand by...') . "<br><br>";
			echo '<div id="status"></div>';
			echo '<div id="oldname"></div>';
			echo '<div id="newname"></div>';
?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				o = document.getElementById("oldname");
				n = document.getElementById("newname");
				s = document.getElementById("status");
				s.innerHTML = '<nobr><?php echo word("Status: Getting track information"); ?>...</nobr>';
				-->
			</SCRIPT>
			<?php

			flushdisplay();

			// Now let's get the tracks
			$tracks = $jzSERVICES->getAlbumMetadata($node, false, "tracks");
			$aTracks = $node->getSubNodes("tracks");
			$c = 1;
			for ($i = 0; $i < count($tracks); $i++) {
				if ($tracks[$i] <> "") {
					// Ok, let's see if we can match this to one of the tracks we have
					foreach ($aTracks as $track) {
						if (stristr($tracks[$i], $track->getName()) or stristr($track->getName(), $tracks[$i])) {
							// Ok, let's make the number 2 chars
							if ($c < 10) {
								$num = "0" . $c;
							} else {
								$num = $c;
							}
							// Now we need to get the meta on this track
							$meta = $track->getMeta();
							// Now let's set the track number
							$meta['number'] = $num;
							// Now let's write that to the meta on the file
							$track->setMeta($meta);
?>
							<SCRIPT LANGUAGE=JAVASCRIPT><!--\
								o.innerHTML = '<nobr><?php echo word("Old Name"); ?>: <?php echo $track->getName(); ?></nobr>';
								n.innerHTML = '<nobr><?php echo word("New Name"); ?>: <?php echo $num. " - ". $tracks[$i]; ?></nobr>';
								s.innerHTML = '<nobr><?php echo word("Status: Renumbering"); ?></nobr>';
								-->
							</SCRIPT>
							<?php

							flushdisplay();
							sleep(1);
							// Now do they want to update the filename?
							if ($allow_filesystem_modify == "true") {
								$oldFile = $track->getDataPath();
								$tArr = explode("/", $track->getDataPath());
								$file = $tArr[count($tArr) - 1];
								unset ($tArr[count($tArr) - 1]);
								$newPath = implode("/", $tArr);
								$newFile = $newPath . "/" . $num . " - " . $file;
								$success = "Failed";
								if (@ rename($oldFile, $newFile)) {
									$success = "Success";
								}
?>
								<SCRIPT LANGUAGE=JAVASCRIPT><!--\
									o.innerHTML = '<nobr><?php echo word("Old Name"); ?>: <?php echo $oldFile; ?></nobr>';
									n.innerHTML = '<nobr><?php echo word("New Name"); ?>: <?php echo $newFile; ?></nobr>';
									s.innerHTML = '<nobr><?php echo word("Status: Renaming"); ?> - <?php echo $success; ?></nobr>';
									-->
								</SCRIPT>
								<?php

								flushdisplay();
							}
						}
					}
					$c++;
				}
			}
?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				o.innerHTML = '&nbsp;';
				n.innerHTML = '&nbsp;';
				s.innerHTML = '<nobr><?php echo word("Complete!"); ?></nobr>';
				-->
			</SCRIPT>
			<?php

			flushdisplay();
			echo '<br><br><center>';
			$this->closeButton(true);
			exit ();
		}

		$this->displayPageTop("", word("Searching for data for: ") . $node->getName());
		$this->openBlock();
		flushdisplay();

		// Now let's get the tracks
		$tracks = $jzSERVICES->getAlbumMetadata($node, false, "tracks");
		if (count($tracks) > 1) {
			// Ok, we got tracks, let's try to match them up...
			$c = 1;
			$aTracks = $node->getSubNodes("tracks");
			for ($i = 0; $i < count($tracks); $i++) {
				if ($tracks[$i] <> "") {
					// Ok, let's see if we can match this to one of the tracks we have
					$found = false;
					foreach ($aTracks as $track) {
						if (stristr($tracks[$i], $track->getName()) or stristr($track->getName(), $tracks[$i])) {
							echo '<font color="green"><nobr>' . $track->getName() . " --- " . $c . " - " . $tracks[$i] . "</nobr></font><br>";
							$found = true;
						}
					}
					if (!$found) {
						echo '<font color="red">' . $c . " - " . $tracks[$i] . " " . word('not matches') . "</font><br>";
					}
					$c++;
				}
			}
			$arr = array ();
			$arr['action'] = "popup";
			$arr['ptype'] = "autorenumber";
			$arr['jz_path'] = $_GET['jz_path'];
			$arr['renumber_type'] = "mb";
			echo '<form action="' . urlize($arr) . '" method="POST">';
			echo "<br><br>";
			echo '<input type="submit" name="edit_renumber" value="' . word('Renumber Tracks') . '" class="jz_submit"> &nbsp; ';
			$this->closeButton();
			echo "</form><br><br>";
		} else {
			echo word("Sorry, we didn't get good data back for this album...");
			echo '<br><br><center>';
			$this->closeButton();
		}
	}
?>