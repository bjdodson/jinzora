<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
global $jzUSER, $row_colors, $raw_img_play_clear, $random_play_amounts, $default_random_count, $jzSERVICES;
// First we need to know if they deleted a list or not
if (isset ($_POST['deletePlaylist'])) {
	if ($_SESSION['jz_playlist'] == "session") {
		$pl = $jzUSER->loadPlaylist();
		$pl->truncate(0);
		$jzUSER->storePlaylist($pl);
	} else {
		$jzUSER->removePlaylist($_SESSION['jz_playlist']);
		unset ($_SESSION['jz_playlist']);
	}
	//$this->closeWindow(true);
	//exit();
}
/*
// Now let's make sure the playlist session ID is set and if not set it to the first one
if (!isset($_SESSION['jz_playlist'])){
  $lists = $jzUSER->listPlaylists();
  foreach ($lists as $id=>$pname) {
    $_SESSION['jz_playlist'] = $id;
    break;
  }
}
        */
// Did they want to edit a different playlist?
if (isset ($_POST['plToEdit'])) {
	$_SESSION['jz_playlist'] = $_POST['plToEdit'];
}

$display = new jzDisplay();
$pl = new jzPlaylist();

// Let's setup the form data
$arr = array ();
$arr['action'] = "popup";
$arr['ptype'] = "playlistedit";

if (isset ($_GET['createpl'])) {
	if (isset ($_POST['createpl2']) && $_POST['query'] != "") {
		// HERE: Make list and set the session appropriately.
		$pl = new jzPlaylist(array (), $_POST['query'], $_POST['pltype']);
		$jzUSER->storePlaylist($pl);
		$_SESSION['jz_playlist'] = $pl->getID();
	}
}

$title = word("Playlist Editor");

if (isset ($_SESSION['jz_playlist'])) {
	// Ok, let's show them the playlist dropdown

	$lists = $jzUSER->listPlaylists();
	//$title .= ": ". $lists[$_SESSION['jz_playlist']];
	$plName = $lists[$_SESSION['jz_playlist']];
} else {
	$plName = false;
}

$this->displayPageTop("", $title, false);
$this->openBlock();


echo  '<table cellspacing="0" border="0" width="100%"><tr><td align="left">';
echo  '<form action="' . urlize($arr) . '" method="POST">';
echo  ' <select onChange="submit()" style="width:150;" name="plToEdit" class="jz_select">';
// Now we need to get all the lists
$lists = $jzUSER->listPlaylists("all");
echo  '<option value= "session">' . word(" - Session Playlist - ") . '</option>' . "\n";
foreach ($lists as $id => $pname) {
	echo  '<option value="' . $id . '"';
	if ($_SESSION['jz_playlist'] == $id) {
		echo  ' selected';
	}
	echo  '>' . $pname . '</option>' . "\n";
}
echo  '</select></form>';
echo  '&nbsp;&nbsp;&nbsp;';

echo  '</td><td align="right">';
$arr['createpl'] = "true";
echo  '<a href="' . urlize($arr) . '">' . word('Create Playlist') . '</a>';
echo "</td></tr></table><br/>";
unset ($arr['createpl']);


// * * * * * * * * *
// NEW PLAYLIST:
// * * * * * * * * *
if (isset ($_GET['createpl'])) {
	if (isset ($_POST['createpl2']) && $_POST['query'] != "") {
		// handled up above.
	} else {
		$arr = array ();
		$arr['action'] = "popup";
		$arr['ptype'] = "playlistedit";
		$arr['createpl'] = "true";

		echo '<form method="POST" action="' . urlize($arr) . '">';
		echo '<table width="40%" align="left" border="0"><tr><td>';
		echo word('Name:') . '<input name="query" class="jz_input"></td></tr><tr><td>';
		echo '<input type="radio" class="jz_radio" name="' . jz_encode('pltype') . '" value="' . jz_encode('static') . '" CHECKED>Static';
		echo '<input type="radio" class="jz_radio" name="' . jz_encode('pltype') . '" value="' . jz_encode('dynamic') . '">Dynamic</td></tr><tr><td>';
		echo '<input type="submit" class="jz_submit" name="' . jz_encode('createpl2') . '" value="' . word('Go') . '"></td></tr>';
		echo '</table>';
		$this->closeBlock();
		return;
	}
}

// * * * * * * * * *
// DYNAMIC PLAYLISTS:
// * * * * * * * * *
if (getListType($_SESSION['jz_playlist']) == "dynamic") {
	$i = 0;
	$pl = $jzUSER->loadPlaylist();
	if (isset ($_POST['addrule'])) {
		if ($_POST['source1'] != "") {
			$source = $_POST['source1'];
		} else
			if ($_POST['source2'] != "") {
				$source = $_POST['source2'];
			} else {
				$source = "";
			}
		$pl->addRule($_POST['amount'], $_POST['function'], $_POST['type'], $source);
		$jzUSER->storePlaylist($pl);
	}
	if (isset ($_POST['updateRestrictions'])) {
		if (is_numeric($_POST['query'])) {
			$pl->setLimit($_POST['query']);
		}
		$jzUSER->storePlaylist($pl);
	}

	$arr = array ();
	$arr['action'] = "playlist";
	$arr['type'] = "playlist";
	$arr['jz_pl_id'] = $pl->getID();
	echo '<strong><a href="' . urlize($arr) . '"';
	if (checkPlayback() == "embedded") {
		echo ' ' . $jzSERVICES->returnPlayerHref();
	}
	echo '>' . word('Play this list') . '</a></strong><br>';
?>
		    <table class="jz_track_table" width="100%" cellpadding="1">
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="3%" valign="top">&nbsp;
		       
				</td>
				<td width="12%">
					<nobr>
					<strong><?php echo word("Amount"); ?></strong>
					</nobr>
				</td>
				<td width="35%">
					<nobr>
						<strong><?php echo word("Function"); ?></strong>
					</nobr>
				</td>
				<td width="10%">
					<nobr>
						<strong><?php echo word("Type"); ?></strong>
					</nobr>
				</td>
				<td width="40%">
					<nobr>
						<strong><?php echo word("Source"); ?></strong>
					</nobr>
				</td>
			</tr>
		        <?php

	$functions = getDynamicFunctions();
	$e = 0;
	$remove = false;
	$arr = array ();
	$arr['action'] = "popup";
	$arr['ptype'] = "playlistedit";
	echo '<form method="POST" action="' . urlize($arr) . '">';
	$rules = $pl->getRules();

	foreach ($rules as $rule) {
		if (isset ($_POST['plRuleDel-' . $e]) && !$remove) {
			// Ok, now let's delete that location
			$pl->removeRule($e);
			$remove = true;
		} else {
			echo '<tr class="' . $row_colors[$i] . '">';
			echo '<td><input type="image" value="' . $e . '" name="' . jz_encode('plRuleDel-' . $e) . '" src="' . $raw_img_play_clear . '" title="' . word("Delete") . '"></td>';
			echo '<td>&nbsp;' . $rule['amount'] . '</td>';
			echo '<td>' . $functions[$rule['function']] . '</td>';
			echo '<td>' . $rule['type'] . '</td>';
			if (($src = $rule['source']) == "") {
				$src = word('All Media');
			}
			echo '<td>' . $src . '</td>';
			$i = 1 - $i;
			$e++;
		}

	}
	if ($remove) {
		$jzUSER->storePlaylist($pl);
	}
	echo '</tr></form></table><br>';
	echo '<form method="POST" action="' . urlize($arr) . '">';
	echo word("Limit:");
	echo '&nbsp';
	echo '<input class="jz_input" size="3" name="query" value="' . $pl->getLimit() . '">';
	echo '&nbsp;';
	echo '<input class="jz_submit" type="submit" name="' . jz_encode('updateRestrictions') . '" value="' . word('Update') . '">';
	echo '</form>';

	echo '<br><br><br><br><br><br>';
	// ADD A RULE:
	echo '<form method="POST" action="' . urlize($arr) . '">';
	echo '<table border="0" align="center" cellspacing="0"><tr>';
	// AMOUNT
	echo '<td valign="top">';
	$random_play = explode("|", $random_play_amounts);
	echo '<select class="jz_select" name="' . jz_encode('amount') . '">';
	$ctr = 0;
	while (count($random_play) > $ctr) {
		echo '<option value="' . jz_encode($random_play[$ctr]) . '"';
		if ($random_play[$ctr] == $default_random_count) {
			echo " selected";
		}
		echo '>' . $random_play[$ctr] . '</option>' . "\n";
		$ctr = $ctr +1;
	}
	echo '</select></td>';
	// FUNCTION
	echo '<td valign="top">';

	echo '<select class="jz_select" name="' . jz_encode('function') . '">';
	foreach ($functions as $val => $name) {
		echo '<option value="' . jz_encode($val) . '">';
		echo $name;
		echo '</option>';
	}
	echo '</select></td>';
	// TYPE
	echo '<td valign="top">';
	echo '<select class="jz_select" name="' . jz_encode('type') . '">';
	echo '<option value="' . jz_encode('tracks') . '">' . word('Songs') . '</option>';
	echo '<option value="' . jz_encode('albums') . '">' . word('Albums') . '</option>';
	echo '</select>';
	echo '</td>';
	// SOURCE
	if (distanceTo('genre') !== false || distanceTo('artist') !== false) {
		echo '<td valign="top">' . word('from:') . '</td>';
		echo '<td valign="top">';
		if (distanceTo("genre") !== false) {
			$display->dropdown("genre", false, "source1");
		}
		if (distanceTo("genre") !== false && distanceTo("artist") !== false) {
			echo '<br>';
		}
		if (distanceTo("artist") !== false) {
			echo $display->dropdown("artist", false, "source2");
		}
		echo '</td>';
		echo '</td></tr></table><br>';
		echo '<table align="center" border="0" cellspacing="0"><tr><td>';
		echo '<input type="submit" class="jz_submit" name="' . jz_encode('addrule') . '" value="' . word('Add Rule') . '">';
		echo ' &nbsp;';
		echo '<input type="submit" name="deletePlaylist" value="' . word("Delete Playlist") . '" class="jz_submit">';
		echo '</td></tr></form>';

	}
	$this->closeBlock();
	return;
}

// Now let's get the list into an array
$plist = $jzUSER->loadPlaylist($_SESSION['jz_playlist']);
/*if ($plist == "") {
	exit ();
}*/

$plist->flatten();
$list = $plist->getList();

// Now we need to see if they deleted a track or not
$e = 0;
$remove = false;
foreach ($list as $item) {
	//echo 'plTrackDel-'. $e. "<br>";
	if (isset ($_POST['plTrackDel-' . $e])) {
		// Ok, now let's delete that location
		$plist->remove($e);
		$remove = true;
	}
	$e++;
}
if ($remove) {
	// Now let's store it
	$jzUSER->storePlaylist($plist, $plName);
	// Now let's read the list again
	$list = $plist->getList();
}

// Ok, did they submit the form or not
if (isset ($_POST['updatePlaylist'])) {
	// Ok, they want to update the playlist, so we need to rebuild it
	// let's get the track positions so we can reorder the array
	$nList = array ();
	$PostArray = $_POST;
	$i = 0;
	$nArr = array ();
	foreach ($PostArray as $key => $val) {
		if (stristr($key, "plTrackPos-")) {
			// Now let's make sure this spot isn't taken
			if (isset ($nArr[$val])) {
				// Now let's increment until we're clear
				$c = $val;
				while (isset ($nArr[$c])) {
					$c++;
				}
				$nArr[$c] = $list[$i];
			} else {
				$nArr[$val] = $list[$i];
			}
			$i++;
		}
	}

	// Let's truncate the old list
	$plist->truncate(0);
	// Now let's add them in order
	for ($i = 0; $i < count($nArr) + $c; $i++) {
		if (isset ($nArr[$i])) {
			$plist->add($nArr[$i]);
		}
	}

	// Now let's store it
	$jzUSER->storePlaylist($plist, $plName);
	// Now let's read the list again
	$list = $plist->getList();
}

$arr2 = array ();
$arr2['action'] = "playlist";
$arr2['type'] = "playlist";
$arr2['jz_pl_id'] = $plist->getID();
echo '<strong><a href="' . urlize($arr2) . '"';
if (checkPlayback() == "embedded") {
	echo ' ' . $jzSERVICES->returnPlayerHref();
}
echo '>' . word('Play this list') . '</a></strong>';
// Now we need to setup a table to display the list in
$i = 1;
?>
		    <form action=" <?php echo urlize($arr); ?>" method="POST">
		       <table class="jz_track_table" width="100%" cellpadding="1">
		       <tr>
		       <!--<td width="1%">
		       <nobr>
		       Playlist Type:
		      </nobr>
		       </td>
		       <td width="99%">
		       <select style="width:70;" name="plType" class="jz_select"><option value="private">Private</option><option value="public">Public</option></select>
		       </td>-->
		       <!--<td width="1%">
		       <nobr>
		       Share with user:
		      </nobr>
		       </td>
		       <td width="49%">
		       <select style="width:70;" name="shareWithUser" class="jz_select"><option value=""> - </option></select>
		       </td>
		       -->
		       </tr>
		       </table>
		<table class="jz_track_table" width="100%" cellpadding="1">
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="1%" valign="top">
					
				</td>
				<td width="1%">
					
				</td>
				<td width="1%">
					<nobr>
					<strong><?php echo word("Track"); ?></strong>
					</nobr>
				</td>
				<td width="1%">
					<nobr>
						<strong><?php echo word("Album"); ?></strong>
					</nobr>
				</td>
				<td width="1%">
					<nobr>
						<strong><?php echo word("Artist"); ?></strong>
					</nobr>
				</td>
				<td width="1%">
					<nobr>
						<strong><?php echo word("Genre"); ?></strong>
					</nobr>
				</td>
			</tr>
			<?php

$e = 0;
foreach ($list as $item) {
	// Now let's setup or names for below
	$track = $item->getName();
	$aItem = $item->getParent();
	$album = $aItem->getName();
	$artItem = $aItem->getParent();
	$artist = $artItem->getName();
	$gItem = $artItem->getParent();
	$genre = $gItem->getName();
?>
					<input type="hidden" name="plItemPath" value="<?php echo $item->getPath("String"); ?>"
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td width="1%">
							<input type="text" name="plTrackPos-<?php echo $e; ?>" size="2" class="jz_input" value="<?php echo $e; ?>">
						</td>
						<td width="1%">
							<input type="image" value="<?php echo $e; ?>" name="plTrackDel-<?php echo $e; ?>" src="<?php echo $raw_img_play_clear; ?>" title="<?php echo word("Delete"); ?>">
						</td>
						<td width="1%">
							<nobr>
							<?php echo $display->playlink($item,$display->returnShortName($track,20)); ?>
							</nobr>
						</td>
						<td width="1%">
							<nobr>
								<?php echo $display->returnShortName($album,20); ?>
							</nobr>
						</td>
						<td width="1%">
							<nobr>
								<?php echo $display->returnShortName($artist,20); ?>
							</nobr>
						</td>
						<td width="1%">
							<nobr>
								<?php echo $display->returnShortName($genre,20); ?>
							</nobr>
						</td>
					</tr>
					<?php

	$e++;
}
?>
		</table>
		<center>
			<br><br>
			<input type="submit" name="updatePlaylist" value="<?php echo word("Update Playlist"); ?>" class="jz_submit"> &nbsp;
			<input type="submit" name="deletePlaylist" value="<?php echo word("Delete Playlist"); ?>" class="jz_submit">
			<br><br><br>
		</center>
		</form>
		<?php


$this->closeBlock();
?>
