<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
* Rates the currently viewed item
* 
* @author Ross Carlson
* @version 01/19/05
* @since 01/19/05
* @param $node The node that we are viewing
*/
global $node;

// Let's see if they rated it?
if (isset ($_POST['itemRating'])) {
	// Ok, let's rate and close
	$node->addRating($_POST['itemRating']);
	$this->closeWindow(true);
}

// First let's display the top of the page and open the main block
$this->displayPageTop("", word("Rate Item") . "<br>" . $node->getName());
$this->openBlock();

// Now let's setup the values
$url_array = array ();
$url_array['jz_path'] = $node->getPath("String");
$url_array['action'] = "popup";
$url_array['ptype'] = "rateitem";

echo '<form action="' . urlize($url_array) . '" method="POST">';
echo '<center><br>' . word("Rating") . ': ';
echo '<select name="' . jz_encode('itemRating') . '" class="jz_select">';
for ($i = 5; $i > 0;) {
	echo '<option value="' . jz_encode($i) . '">' . $i . '</option>';
	$i = $i -.5;
}
echo '</select>';
echo '<br><br><input type="submit" name="' . jz_encode('submitRating') . '" value="' . word("Rate Item") . '" class="jz_submit">';
echo " ";
$this->closeButton();
echo '</center>';
echo '</form>';

// Now let's close out
$this->closeBlock();
?>
