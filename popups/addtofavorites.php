<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');
/**
* Displays the quick box to add an item to favorites
*
* @author Ross Carlson
* @since 12.17.05
* @version 12.17.05
* @param $path The node that we are viewing
**/
global $include_path, $jzUSER;

$node = new jzMediaNode($path);
$display = new jzDisplay();
$be = new jzBackend();

// Let's start the page header
$this->displayPageTop("", word("Adding to Favorites"));
$this->openBlock();
echo word("Adding") . ": " . $node->getName();

// Now let's add it

$this->closeBlock();
?>
