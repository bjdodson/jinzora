<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
* Adds the selected node to the featured list
* 
* @author Ross Carlson
* @version 01/19/05
* @since 01/19/05
* @param $node The node that we are viewing
*/
global $node;

// First let's display the top of the page and open the main block
$this->displayPageTop("", word("Adding to featured") . "<br>" . $node->getName());
$this->openBlock();

// Now let's add this puppy
$node->addFeatured();

// Let's display status
echo "<br>" . word("Add complete!");

// Now let's close out
$this->closeBlock();
flushDisplay();

sleep(3);
$this->closeWindow(true);
?>
