<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
* Allows the user to burn a CD
*
* @author Ross Carlson
* @since 6/20/05
* @version 6/20/05
* @param $node The node that we are viewing
**/
global $include_path, $jzSERVICES, $node;

$this->displayPageTop("", word("Burn CD"));
$this->openBlock();

// Did they want to burn?
if (isset ($_GET['sub_action'])) {
	if ($_GET['sub_action'] == "create") {
		// Ok, we need to get a list of all the tracks
		$tracks = $node->getSubNodes("tracks", -1);
		$fileArray = array ();
		foreach ($tracks as $track) {
			// Now we need to resample each one to a WAV file
			// First let's create the new file name - we'll make this random
			echo "Resampling: " . $track->getName() . "<br>";
			flushdisplay();
			$fileArray[] = $jzSERVICES->createResampledTrack($track->getDataPath(), "wav", "", "", getcwd() . "/data/burn/" . $track->getName() . ".wav");
			flushdisplay();
		}

		// Now let's burn this list of files
		$album = $node->getName();
		$art = $node->getAncestor("artist");
		$artist = $art->getName();

		echo "<br><br>";
		$jzSERVICES->burnTracks($node, $artist, $album);

		exit ();
	}
}

$dlarr = array ();
$dlarr['action'] = "popup";
$dlarr['ptype'] = "burncd";
$dlarr['sub_action'] = "create";
$dlarr['jz_path'] = $node->getPath("string");

echo '<a href="' . urlize($dlarr) . '">Burn CD</a>';

$this->closeBlock();
?>
