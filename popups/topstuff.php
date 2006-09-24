<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
* Displays the full top played list
* 
* @author Ross Carlson
* @version 01/27/05
* @since 01/27/05
* @param $node The node we are looking at
*/
global $img_tiny_play, $album_name_truncate, $root_dir, $node;

$display = new jzDisplay();

// First let's display the top of the page and open the main block
$title = "Top ";
switch ($_GET['tptype']) {
	case "played-albums" :
		$limit = 50;
		$title .= $limit . " " . word("Played Albums");
		$type = "album";
		$func = "getMostPlayed";
		break;
	case "played-artists" :
		$limit = 50;
		$title .= $limit . " " . word("Played Artists");
		$type = "artist";
		$func = "getMostPlayed";
		break;
	case "played-tracks" :
		$limit = 50;
		$title .= $limit . " " . word("Played Tracks");
		$type = "track";
		$func = "getMostPlayed";
		break;
	case "downloaded-albums" :
		$limit = 50;
		$title .= $limit . " " . word("Downloaded Albums");
		$type = "album";
		$func = "getMostDownloaded";
		break;
	case "new-albums" :
		$limit = 100;
		$title .= $limit . " " . word("New Albums");
		$type = "album";
		$func = "getRecentlyAdded";
		break;
	case "new-artists" :
		$limit = 100;
		$title .= $limit . " " . word("New Artists");
		$type = "artist";
		$func = "getRecentlyAdded";
		break;
	case "new-tracks" :
		$limit = 100;
		$title .= $limit . " " . word("New Tracks");
		$type = "track";
		$func = "getRecentlyAdded";
		break;
	case "recentplayed-albums" :
		$limit = 50;
		$title .= $limit . " " . word("Played Albums");
		$type = "album";
		$func = "getRecentlyPlayed";
		break;
	case "recentplayed-artists" :
		$limit = 50;
		$title .= $limit . " " . word("Played Artists");
		$type = "artist";
		$func = "getRecentlyPlayed";
		break;
	case "recentplayed-albums" :
		$limit = 50;
		$title .= $limit . " " . word("Played Albums");
		$type = "album";
		$func = "getRecentlyPlayed";
		break;
	case "recentplayed-tracks" :
		$limit = 50;
		$title .= $limit . " " . word("Played Tracks");
		$type = "track";
		$func = "getRecentlyPlayed";
		break;
	case "toprated-artists" :
		$limit = 50;
		$title .= $limit . " " . word("Rated Artists");
		$type = "artist";
		$func = "getTopRated";
		break;
	case "toprated-albums" :
		$limit = 50;
		$title .= $limit . " " . word("Rated Albums");
		$type = "album";
		$func = "getTopRated";
		break;
	case "topviewed-artists" :
		$limit = 50;
		$title .= $limit . " " . word("Viewed Artists");
		$type = "artist";
		$func = "getMostViewed";
		$showCount = "view";
		break;

}
$this->displayPageTop("", $title);
$this->openBlock();

// Now let's get the recently added items
if ($type == "track") {
	$retType = "tracks";
} else {
	$retType = "nodes";
}
$recent = $node-> $func ($retType, distanceTo($type, $node), $limit);

// Now let's loop through the results
for ($i = 0; $i < count($recent); $i++) {
	// Now let's create our node and get the properties
	$item = $recent[$i];
	$album = $item->getName();
	$parent = $item->getParent();
	$artist = $parent->getName();

	// Now let's create our links
	$albumArr['jz_path'] = $item->getPath("String");
	$artistArr['jz_path'] = $parent->getPath("String");

	// Now let's create our short names
	$artistTitle = returnItemShortName($artist, $album_name_truncate);
	$albumTitle = returnItemShortName($album, $album_name_truncate);

	// Now let's display it
	echo "<nobr>";
	$display->playLink($item, $img_tiny_play, $album);

	// Now let's set the hover code
	$innerOver = "";
	if (($art = $item->getMainArt()) <> false) {
		$innerOver .= $display->returnImage($art, $item->getName(), 75, 75, "limit", false, false, "left", "3", "3");
	}
	$desc_truncate = 200;
	$desc = $item->getDescription();
	$innerOver .= $display->returnShortName($desc, $desc_truncate);
	if (strlen($desc) > $desc_truncate) {
		$innerOver .= "...";
	}
	$innerOver = str_replace('"', "", $innerOver);
	$innerOver = str_replace("'", "", $innerOver);

	// Now let's return our tooltip													
	$capTitle = $artist . " - " . $album;
	$overCode = $display->returnToolTip($innerOver, $capTitle);
	echo ' <a onClick="opener.location.href=\'' . urlize($albumArr) . '\';window.close();" ' . $overCode . 'href="javascript:void()">' . $albumTitle;
	$cval = false;
	// TODO: showCount values can be:
	// view,dowload,play
	if ($showCount == "view") {
		$cval = $item->getViewCount();
	} else {
		$cval = $item->getPlayCount();
	}
	if ($cval !== false && $cval <> 0) {
		echo ' (' . $cval . ')';
	}
	echo "</a><br>";
	// Now let's set the hover code
	//echo ' <a title="'. $artist. ' - '. $album. '" href="'. urlize($albumArr). '">'. $albumTitle. '</a> ('. $albumPlayCount. ')';
	//echo "<br>";
	echo "</nobr>";
	flushdisplay();
}

$this->closeBlock();
?>
