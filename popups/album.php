<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

// This function will display the complete list of artists

global $row_colors, $web_root, $root_dir, $directory_level;

// Let's display the top of our page	
$this->displayPageTop("", word("All Albums"));
$this->openBlock();

echo "<center>";

// Now let's give them a list of choices
$ua = array ();
$ua['action'] = "popup";
$ua['ptype'] = "album";

// Let's give them a search bar.
$search = isset ($_POST['query']) ? $_POST['query'] : "";
echo "<form action=\"" . urlize($ua) . "\" method=\"post\" name=\"selectAlbum\">";
echo "<input type=\"text\" class=\"jz_input\" size=\"18\" value=\"$search\" name=\"query\">";
echo '<input class="jz_submit" type="submit" name="' . jz_encode('lookup') . '" value="' . word("Go") . '">';
echo "</form><br>";
// That's all for the search bar.

$i = 97;
$c = 2;
$ua['a'] = "#";
echo '<a href="' . urlize($ua) . '">1-10</a> | ';
while ($i < 123) {
	$ua['a'] = chr($i);
	echo '<a href="' . urlize($ua) . '">' . strtoupper(chr($i)) . '</a>';
	if ($c % 9 == 0) {
		echo "<br>";
	} else {
		echo " | ";
	}
	$i++;
	$c++;
}
echo "<br>";

// Now let's setup our form
echo '<form action="' . urlize($ua) . '" method="post" name="selectAlbum">';
// Now let's set so we'll know where to go back to
echo '<input type="hidden" name="return" value="' . $_GET['return'] . '">';

// See if they ran a search.
if ($search != "") {
	// Now let's get all the genres from our cache file
	$root = & new jzMediaNode();
	$matches = $root->search($search, "nodes", distanceTo("album"));

	// arrayify search.
	echo '<select name="' . jz_encode("chosenPath") . '"size="18" class="jz_select" style="width: 200px" onChange="submit()">';
	for ($i = 0; $i < count($matches); $i++) {
		$parent = $matches[$i]->getNaturalParent();
		echo '<option value="' . jz_encode(htmlentities($matches[$i]->getPath("String"))) . '">' . $matches[$i]->getName() . " (" . $parent->getName() . ")";
	}
	echo "</select>";
}
// End search stuff.

// Now let's see if they wanted a letter or not
else
	if (isset ($_GET['a'])) {
		// Now let's get all the artists from our cache file
		$root = & new jzMediaNode();
		$matches = $root->getAlphabetical($_GET['a'], "nodes", distanceTo("album"));
		echo '<select name="' . jz_encode("chosenPath") . '"size="15" class="jz_select" style="width: 200px" onChange="submit()">';
		for ($i = 0; $i < count($matches); $i++) {
			$parent = $matches[$i]->getNaturalParent();
			echo '<option value="' . jz_encode(htmlentities($matches[$i]->getPath("String"))) . '">' . $matches[$i]->getName() . " (" . $parent->getName() . ")";
		}
		echo '</select>';
	}
echo "</form>";
echo "<br><br>";
$this->closeButton();
echo "</center>";

$this->closeBlock();
exit ();
?>
