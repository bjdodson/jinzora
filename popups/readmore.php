<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
* Displays the read more information on an artist from a popup
* 
* @author Ross Carlson
* @version 01/19/05
* @since 01/19/05
* @param $node The node that we are viewing
*/

global $cms_mode, $node;

// Let's setup our objects
$display = new jzDisplay();

// First let's display the top of the page and open the main block
$this->displayPageTop("", word("Profile") . ": " . $node->getName());
$this->openBlock();

// Now let's display the artist image and short description
if (($art = $node->getMainArt("200x200")) <> false) {
	$display->image($art, $node->getName(), 200, 200, "limit", false, false, "left", "5", "5");
}
if ($cms_mode == "false") {
	echo '<span class="jz_artistDesc">';
}
echo fixAMGUrls($node->getDescription());
if ($cms_mode == "false") {
	echo '</span>';
}

$this->closeBlock();
?>
