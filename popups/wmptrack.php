<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
* Displays the track details for the currently playing track in WMP
*
* @author Ross Carlson
* @since 8.3.06
* @version 8.3.06
**/
global $this_site, $root_dir, $jzSERVICES, $web_root;

// Let's setup our display object
$display = new jzDisplay();

// Let's create the track object
$track = new jzMediaTrack($_GET['jz_path']);
$meta = $track->getMeta();

// Now let's get the album and artist
$album = $track->getNaturalParent("album");
$artist = $album->getNaturalParent("artist");
$desc = $album->getDescription();
while (substr($desc, 0, 4) == "<br>" or substr($desc, 0, 6) == "<br />") {
	if (substr($desc, 0, 4) == "<br>") {
		$desc = substr($desc, 5);
	}
	if (substr($desc, 0, 6) == "<br />") {
		$desc = substr($desc, 7);
	}
}

// Now let's get the art
$art = $album->getMainArt("200x200");
if ($art <> "") {
	$albumArt = $display->returnImage($art, $album->getName(), 150, 150, "limit", false, false, "left", "3", "3");
} else {
	$art = $jzSERVICES->createImage($web_root . $root_dir . '/style/images/default.jpg', "200x200", $track->getName(), "audio", "true");
	$albumArt = '<img src="' . $this_site . $root_dir . "/" . $art . '" border="0" align="left" hspace="3" vspace="3">';
}

// Now let's setup Smarty
$smarty = smartySetup();

// Let's setup the Smarty variables
$smarty->assign('trackName', $track->getName());
$smarty->assign('albumName', $album->getName());
$smarty->assign('artistName', $artist->getName());
$smarty->assign('albumArt', $albumArt);
$smarty->assign('lyrics', $meta['lyrics']);
$smarty->assign('trackNum', $meta['number']);
$smarty->assign('albumDesc', $desc);
$smarty->assign('totalTracks', $_GET['totalTracks']);

// Now let's display the template
$smarty->display(SMARTY_ROOT . 'templates/general/asx-display.tpl');
?>
