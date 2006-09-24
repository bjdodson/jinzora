<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
* Displays the site/location news block text to be edited
* 
* @author Ross Carlson, Ben Dodson
* @version 01/27/05
* @since 01/27/05
* @param $node The node we are looking at
*/

global $jzUSER, $node;

if (!checkPermission($jzUSER, "admin", $node->getPath("String"))) {
	echo word("Insufficient permissions.");
	return;
}

$be = new jzBackend();

// Let's figure out the news location
if ($node->getName() == "") {
	$news = "site-news";
	$title = word("Site News");
} else {
	$news = $node->getName() . "-news";
	$title = word("Site News") . ": " . $node->getName();
}

$this->displayPageTop("", $title);
$this->openBlock();

// Did they submit the form to edit the news?
if (isset ($_POST['updateSiteNews'])) {
	// Now let's store the data
	$be->storeData($news, nl2br(str_replace("<br />", "", $_POST['siteNewsData'])));
}

// Let's show the form to edit with
$arr = array ();
$arr['action'] = "popup";
$arr['ptype'] = "sitenews";
$arr['jz_path'] = $_GET['jz_path'];
echo '<form action="' . urlize($arr) . '" method="POST">';
?>
		<br>
		<center>
			<textarea name="siteNewsData" cols="60" rows="20" class="jz_input"><?php echo $be->loadData($news); ?></textarea>
			<br><br>
			<input type="submit" value="<?php echo word("Update News"); ?>" name="<?php echo jz_encode("updateSiteNews"); ?>" class="jz_submit">
			&nbsp;
			<?php

$this->closeButton(false);
?>
		</center>
		<?php


$this->closeBlock();
exit ();
?>
