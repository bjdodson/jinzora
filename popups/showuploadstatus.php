<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
* Displays the upload status box
* 
* @author Ross Carlson, Ben Dodson
* @version 03/01/05
* @since 03/01/05
*/

global $root_dir;

$this->displayPageTop("", word("Uploading Media, Please wait..."));
$this->openBlock();

echo '<br><center>';
echo word('<strong>File upload in progress!</strong><br><br>This page will go away automatically when the upload is complete. Please be patient!') . "<br><br>";
echo '<img src="' . $root_dir . '/style/images/computer.gif" border="0">';
echo '<img src="' . $root_dir . '/style/images/uploading.gif" border="0">';
echo '<img src="' . $root_dir . '/style/images/computer.gif" border="0">';

$this->closeBlock();
?>
