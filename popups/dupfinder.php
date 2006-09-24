<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');


/**
* Displays the full top played list
* 
* @author Ross Carlson, Ben Dodson
* @version 01/27/05
* @since 01/27/05
*/

$this->displayPageTop("", word("Duplicate Finder"));
$this->openBlock();

// Now let's see if they searched
if (isset ($_POST['searchDupArtists']) or isset ($_POST['searchDupAlbums']) or isset ($_POST['searchDupTracks'])) {
	// Ok, let's search, but for what?
	if (isset ($_POST['searchDupArtists'])) {
		$distance = distanceTo("artist");
		$what = "nodes";
	}
	if (isset ($_POST['searchDupAlbums'])) {
		$distance = distanceTo("album");
		$what = "nodes";
	}

	// Ok, now we need to get a list of ALL artist so we can show possible dupes
	echo word("Retrieving full list...") . "<br><br>";
	flushdisplay();

	$root = new jzMediaNode();
	$artArray = $root->getSubNodes($what, $distance);
	for ($i = 0; $i < count($artArray); $i++) {
		$valArray[] = $artArray[$i]->getName();
	}
	echo word("Scanning full list...") . "<br><br>";
	flushdisplay();

	$found = $root->search($valArray, $what, $distance, sizeof($valArray), "exact");
	foreach ($found as $e) {
		$matches[] = $e->getName();
		echo $e->getName() . '<br>';
		flushdisplay();
	}

	$this->closeBlock();
	exit ();

}

$arr = array ();
$arr['action'] = "popup";
$arr['ptype'] = "dupfinder";
echo '<form action="' . urlize($arr) . '" method="POST">';
echo "<br><br>";
echo "<center>";
echo word("Please select what you would like to search for") . "<br><br><br>";
echo '<input type="submit" value="' . word("Search Artists") . '" name="' . jz_encode("searchDupArtists") . '" class="jz_submit">';
echo ' &nbsp; ';
echo '<input type="submit" value="' . word("Search Albums") . '" name="' . jz_encode("searchDupAlbums") . '" class="jz_submit">';
echo ' &nbsp; ';
echo '<input type="submit" value="' . word("Search Tracks") . '" name="' . jz_encode("searchDupTracks") . '" class="jz_submit">';

echo "</center>";
echo '</form>';

$this->closeBlock();
?>
