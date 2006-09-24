<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');
/**
* Displays the tool to purge the shoutbox
*
* @author Ross Carlson
* @since 8.9.06
* @version 8.9.06
**/
global $root_dir, $web_root;

// Let's start the page header
$this->displayPageTop("", word("Purge Shoutbox"));
$this->openBlock();

// Let's kill the file
@ unlink($web_root . $root_dir . "/data/yshout/logs/main.txt");

echo "<center>";
echo "<br>" . word("Shoutbox Data Purged") . "!<br><br><br>";
$this->closeButton(true);
echo "</center>";

$this->closeBlock();
?>
