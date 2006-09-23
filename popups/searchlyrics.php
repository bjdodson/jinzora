<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');


/**
* Searches for lyrics of the given node
* 
* @author Ross Carlson
* @version 01/18/05
* @since 01/18/05
* @param $node The node we are looking at
*/
global $jzSERVICES;

if (!isset ($_POST['edit_search_lyrics'])) {
	$this->displayPageTop("", word("Retrieving lyrics for") . ":<br>" . $node->getName());
	$this->openBlock();

	$arr = array ();
	$arr['action'] = "popup";
	$arr['ptype'] = "searchlyrics";
	$arr['jz_path'] = $node->getPath("String");
	echo '<form name="lyrics" action="' . urlize($arr) . '" method="POST">';
	echo word("Get lyrics for") . ": ";
	echo '<select name="edit_lyrics_get" class="jz_select">';
	echo '<option value="missing">' . word("Tracks Missing Lyrics	") . '</option>';
	echo '<option value="all">' . word("All Tracks") . '</option>';
	echo '</select>';
	echo '<input type="submit" name="edit_search_lyrics" value="' . word("Go") . '" class="jz_submit">';
	echo '</form>';

	$this->closeBlock();
	exit ();
}

// First let's display the top of the page and open the main block
$this->displayPageTop("", word("Retrieving lyrics for") . ":<br>" . $node->getName());
$this->openBlock();

echo '<div id="group"></div>';
echo '<div id="album"></div>';
echo '<div id="artist"></div>';
echo '<div id="current"></div>';
echo '<div id="status"></div>';
echo '<div id="progress"></div>';
?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			gr = document.getElementById("group");
			c = document.getElementById("current");
			s = document.getElementById("status");
			a = document.getElementById("album");
			ar = document.getElementById("artist");
			p = document.getElementById("progress");
			c.innerHTML = '<?php echo word("Please wait while we load the track data..."); ?>';					
			-->
		</SCRIPT>
		<?php

flushDisplay();

// Now let's see what grouping were on
$length = 50;
if (!isset ($_SESSION['jz_retag_group'])) {
	$_SESSION['jz_retag_group'] = 0;
} else {
	$_SESSION['jz_retag_group'] = $_SESSION['jz_retag_group'] + $length;
}
$allTracks = $node->getSubNodes("tracks", -1);
?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			gr.innerHTML = '<nobr><?php echo word("Files"); ?>: <?php echo $_SESSION['jz_retag_group']. " - ". ($_SESSION['jz_retag_group'] + $length). "/". count($allTracks); ?></nobr>';
			-->
		</SCRIPT>
		<?php

flushdisplay();

$tracks = array_slice($allTracks, $_SESSION['jz_retag_group'], $length);
$total = count($tracks);
$i = 0;
$a = 0;
$start = time();
$c = 0;

// Now let's add the node for what we are viewing
$totalCount = 0;
foreach ($tracks as $track) {
	// Let's give status
	$parent = $track->getParent();
	$album = str_replace("'", "", $parent->getName());
	$gparent = $parent->getParent();
	$artist = str_replace("'", "", $gparent->getName());
?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				c.innerHTML = '<?php echo word("Track"); ?>: <?php echo $track->getName(); ?>';					
				a.innerHTML = '<nobr><?php echo word("Item"); ?>: <?php echo $album; ?> - <?php echo $artist; ?></nobr>';					
				-->
			</SCRIPT>
			<?php

	flushDisplay();

	$meta = array ();
	$echoVal = "<nobr><b>" . word("Track") . ":</b> " . $track->getName();

	// Do we want all track or just those missing?
	if ($_POST['edit_lyrics_get'] == "missing") {
		$metaData = $track->getMeta();
		if ($metaData['lyrics'] == "") {
			$meta['lyrics'] = $jzSERVICES->getLyrics($track);
		} else {
			$meta['lyrics'] = "EXIST";
		}
	} else {
		$meta['lyrics'] = $jzSERVICES->getLyrics($track);
	}
	if ($meta['lyrics'] == "EXIST") {
?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					s.innerHTML = '<nobr><?php echo word("Status: Exists"); ?></nobr>';
					-->
				</SCRIPT>
				<?php

	} else
		if ($meta['lyrics'] <> "") {
			$track->setMeta($meta);
?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					s.innerHTML = '<nobr><?php echo word("Status: Found"); ?></nobr>';
					-->
				</SCRIPT>
				<?php

		} else {
?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					s.innerHTML = '<nobr><?php echo word("Status: Not Found"); ?></nobr>';
					-->
				</SCRIPT>
				<?php

		}
	flushDisplay();

	// Now let's get the progress
	$progress = round(($c / $total) * 100);
	$totalCount++;
	$progress = $c . "/" . $total . " - " . $progress . "%";
	// now let's write it
?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				p.innerHTML = '<nobr><?php echo word("Progress"); ?>: <?php echo $progress; ?></nobr>';
				-->
			</SCRIPT>
			<?php

	flushdisplay();
	$c++;
}

// Now are we done or do we continue?
if (count($allTracks) < $_SESSION['jz_retag_group']) {
?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				c.innerHTML = '<?php echo word("Process Complete!"); ?>';
				a.innerHTML = '&nbsp;';
				gr.innerHTML = '&nbsp;';
				ar.innerHTML = '&nbsp;';
				s.innerHTML = '&nbsp;';	
				p.innerHTML = '&nbsp;';	
			</SCRIPT>
			<?php


	unset ($_SESSION['jz_retag_group']);
	$this->closeBlock();
	flushDisplay();
	echo '<br><br><center>';
	$this->closeButton();
} else {
?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				c.innerHTML = '<?php echo word("Proceeding, please stand by..."); ?>';
				a.innerHTML = '&nbsp;';
				gr.innerHTML = '&nbsp;';
				ar.innerHTML = '&nbsp;';
				s.innerHTML = '&nbsp;';	
				p.innerHTML = '&nbsp;';		
				-->
			</SCRIPT>
			<?php

	flushdisplay();
	// Now we need to setup our bogus form
	$arr = array ();
	$arr['action'] = "popup";
	$arr['ptype'] = "searchlyrics";
	$arr['jz_path'] = $node->getPath("String");
	echo '<form name="lyrics" action="' . urlize($arr) . '" method="POST">';

	$PostArray = $_POST;
	foreach ($PostArray as $key => $val) {
		echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) . '">' . "\n";
	}
	echo '</form>';
?>
			<SCRIPT language="JavaScript">
			document.lyrics.submit();
			</SCRIPT>
			<?php

	exit ();
}
?>
