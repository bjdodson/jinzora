<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
* Displays the cache management tools
*
* @author Ross Carlson
* @since 2.22.06
* @version 2.22.06
* @param $path The node that we are viewing
**/
global $web_root, $root_dir, $path;

// Let's start the page header
$this->displayPageTop("", word("Cache Manager"));
$this->openBlock();

// Let's create the node
$node = new jzMediaNode($path);

// Did they want to do something?
if (isset ($_GET['subpage'])) {
	switch ($_GET['subpage']) {
		case "deleteall" :
			$i = 0;
			$d = dir($web_root . $root_dir . "/temp/cache");
			while ($entry = $d->read()) {
				if ($entry == "." || $entry == "..") {
					continue;
				}
				if (@ unlink($web_root . $root_dir . "/temp/cache/" . $entry)) {
					$i++;
				}
			}
			echo word('%s cache files deleted.', $i);
			break;
		case "thisnode" :
			$display = new jzDisplay();
			$display->purgeCachedPage($node);
			$nodes = $node->getSubNodes("nodes", -1);
			$i = 1;
			foreach ($nodes as $item) {
				$display->purgeCachedPage($item);
				$i++;
			}
			echo word("%s nodes purged", $i);
			break;
		case "viewsize" :
			$d = dir($web_root . $root_dir . "/temp/cache");
			$size = 0;
			while ($entry = $d->read()) {
				$size = $size +filesize($web_root . $root_dir . "/temp/cache/" . $entry);
			}
			echo word("Total cache size: %s MB", round((($size / 1024) / 1024), 2));
			break;
	}
	echo "<br><br>";
}

$url_array = array ();
$url_array['jz_path'] = $node->getPath("String");
$url_array['action'] = "popup";
$url_array['ptype'] = "cachemanager";

$url_array['subpage'] = "deleteall";
echo '<a href="' . urlize($url_array) . '">' . word("Purge ALL caches") . '</a><br>';

$url_array['subpage'] = "thisnode";
echo '<a href="' . urlize($url_array) . '">' . word("Purge Cache for") . ": " . $node->getName() . '</a><br>';

$url_array['subpage'] = "viewsize";
echo '<a href="' . urlize($url_array) . '">' . word("View Cache Size") . '</a><br><br>';

$this->closeButton();
$this->closeBlock();
?>
