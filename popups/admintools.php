<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
* Displays the Admin Tools Section
*
* @author Ross Carlson
* @since 11/28/05
* @version 11/28/05
* @param $node The node that we are viewing
**/
global $include_path, $jzUSER, $allow_filesystem_modify, $enable_podcast_subscribe,$path;

$node = new jzMediaNode($path);
$display = new jzDisplay();

// Let's start the page header
$this->displayPageTop("", word("Admin Tools"));
$this->openBlock();

if ($jzUSER->getSetting('admin') <> true) {
	echo "<br><br><br><center>PERMISSION DENIED!!!";
	$this->closeBlock();
}

// Let's start our tabs
$display->displayTabs(array (
	"Media Management",
	"Meta Data",
	"System Tools"
));

// Let's setup our links
$url_array = array ();
$url_array['jz_path'] = $node->getPath("String");
$url_array['action'] = "popup";

// Now let's build an array of all the values for below
if (checkPermission($jzUSER, "upload", $node->getPath("String")) and $allow_filesystem_modify == "true") {
	$url_array['ptype'] = "uploadmedia";
	$valArr[] = '<a href="' . urlize($url_array) . '">' . word("Add Media") . '</a>';
}
$url_array['ptype'] = "addlinktrack";
$valArr[] = '<a href="' . urlize($url_array) . '">' . word("Add Link Track") . '</a>';

$url_array['ptype'] = "setptype";
$valArr[] = '<a href="' . urlize($url_array) . '">' . word("Set Page Type") . '</a>';

$url_array['ptype'] = "scanformedia";
$valArr[] = '<a href="' . urlize($url_array) . '">' . word("Rescan Media") . '</a>';

$url_array['ptype'] = "artfromtags";
$valArr[] = '<a href="' . urlize($url_array) . '">' . word("Pull art from Tag Data") . '</a>';

if ($node->getPType() == "artist" or $node->getPType() == "album") {
	// Ok, is it already featured?
	if (!$node->isFeatured()) {
		$url_array['ptype'] = "addfeatured";
		$valArr[] = '<a href="' . urlize($url_array) . '">' . word("Add to Featured") . '</a>';
	} else {
		$url_array['ptype'] = "removefeatured";
		$valArr[] = '<a href="' . urlize($url_array) . '">' . word("Remove from Featured") . '</a>';
	}
}

if ($node->getPType() == "album") {
	$url_array['ptype'] = "bulkedit";
	$valArr[] = '<a href="' . urlize($url_array) . '">' . word("Bulk Edit") . '</a>';

	$url_array['ptype'] = "getalbumart";
	$valArr[] = '<a href="' . urlize($url_array) . '">' . word("Search for Album Art") . '</a>';

	$url_array['ptype'] = "pdfcover";
	$valArr[] = '<a href="' . urlize($url_array) . '">' . word("Create PDF Cover") . '</a>';
}

if ($enable_podcast_subscribe == "true") {
	$url_array['ptype'] = "addpodcast";
	$valArr[] = '<a href="' . urlize($url_array) . '">' . word("Podcast Manager") . '</a>';
}

// Now let's put the content into the tabs
$i = 0;
echo '<div id="panel1" class="panel"><table width="90%" cellpadding="8" cellspacing="0" border="0">';
foreach ($valArr as $item) {
	if ($i == 0) {
		echo "</tr><tr>";
	}
	echo "<td>";
	echo $item;
	echo "</td>";
	$i++;
	if ($i == 3) {
		$i = 0;
	}
}
echo '</table></div>';
?>
		
		<div id="panel2" class="panel">
			<table width="90%" cellpadding="5" cellspacing="0" border="0">
				<tr>
					<td>
						<?php $url_array['ptype'] = "getmetadata";  echo '<a href="'. urlize($url_array). '">'. word("Retrieve Meta Data"). '</a>'; ?>
					</td>
					<td>
						<?php $url_array['ptype'] = "searchlyrics";  echo '<a href="'. urlize($url_array). '">'. word("Retrieve Lyrics"). '</a>'; ?>
					</td>
					<td>
						<?php $url_array['ptype'] = "resizeart";  echo '<a href="'. urlize($url_array). '">'. word("Resize All Art"). '</a>'; ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php $url_array['ptype'] = "autorenumber";  echo '<a href="'. urlize($url_array). '">'. word("Auto Renumber"). '</a>'; ?>		
					</td>
					<td>
						<?php $url_array['ptype'] = "iteminfo";  echo '<a href="'. urlize($url_array). '">'. word("Item Information"). '</a>'; ?>		
					</td>
					<td>
						<?php $url_array['ptype'] = "retagger";  echo '<a href="'. urlize($url_array). '">'. word("Retag Tracks"). '</a>'; ?>		
					</td>
				</tr>
			</table>
		</div>   
		<div id="panel3" class="panel">
			<table width="90%" cellpadding="5" cellspacing="0" border="0">
				<tr>
					<td>
						<?php $url_array['ptype'] = "mediamanager";  echo '<a href="'. urlize($url_array). '">'. word("Media Manager"). '</a>'; ?>
					</td>
					<td>
						<?php $url_array['ptype'] = "usermanager";  echo '<a href="'. urlize($url_array). '">'. word("User Manager"). '</a>'; ?>
					</td>
					<td>
						<?php $url_array['ptype'] = "sitesettings";  echo '<a href="'. urlize($url_array). '">'. word("Settings Manager"). '</a>'; ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php $url_array['ptype'] = "sitenews";  echo '<a href="'. urlize($url_array). '">'. word("Manage Site News"). '</a>'; ?>
					</td>
					<td>
						<?php $url_array['ptype'] = "nodestats"; unset($url_array['jz_path']); echo '<a href="'. urlize($url_array). '">'. word("Show Full Site Stats"). '</a>'; ?>
					</td>
					<td>
						<!--<?php $url_array['ptype'] = "dupfinder";  echo '<a href="'. urlize($url_array). '">'. word("Duplicate Finder"). '</a>'; ?>-->
					</td>
				</tr>
				<tr>
					<td>
						<?php $url_array['ptype'] = "cachemanager";  $url_array['jz_path'] = $node->getPath("String"); echo '<a href="'. urlize($url_array). '">'. word("Cache Manager"). '</a>'; ?>
					</td>
					<td>

					</td>
					<td>

					</td>
				</tr>
			</table>
		</div>   
		<?php

$this->closeBlock();
?>
