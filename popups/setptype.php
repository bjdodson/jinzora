<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
	* Displays the tool to let the user set the page type
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

if (isset ($_POST['edit_auto_set_ptype'])) {
	$this->displayAutoPageType($node);
	exit ();
}

// Let's see if they submitted the form
if (isset ($_POST['newPType'])) {
	// Now let's set the type
	if ($_POST['newPType'] != "unchanged") {
		$node->setPType($_POST['newPType']);
	}

	$i = 1;
	while (isset ($_POST["newPType-$i"])) {
		if (($pt = $_POST["newPType-$i"]) != "unchanged") {
			$nodes = $node->getSubNodes("nodes", $i);
			foreach ($nodes as $n) {
				$n->setPType($pt);
			}
		}
		$i++;
	}
	echo "<br><br><center>";
	$this->closeButton(true);
	exit ();
}
$this->displayPageTop("", word("Set Page Type for") . ": " . $node->getName());
$this->openBlock();

// Let's show the form to edit with
$arr = array ();
$arr['action'] = "popup";
$arr['ptype'] = "setptype";
$arr['jz_path'] = $node->getPath("String");
echo '<form action="' . urlize($arr) . '" method="POST">';
echo word("Current Page Type") . ": " . $node->getPType() . "<br><br>";
echo '<table><tr><td>';
echo word("New Page Type") . ": ";
echo '</td><td>';
echo '<select name="' . jz_encode("newPType") . '" class="jz_select">';
echo '<option value="' . jz_encode("unchanged") . '">' . word("Unchanged") . '</option>';
echo '<option value="' . jz_encode("genre") . '">' . word("Genre") . '</option>';
echo '<option value="' . jz_encode("artist") . '">' . word("Artist") . '</option>';
echo '<option value="' . jz_encode("album") . '">' . word("Album") . '</option>';
echo '<option value="' . jz_encode("disk") . '">' . word("Disk") . '</option>';
echo '<option value="' . jz_encode("generic") . '">' . word("Generic") . '</option>';
echo '</select>';
echo '</td></tr>';
$i = 1;
while ($node->getSubNodeCount("nodes", $i) > 0) {
	echo "<tr><td>Level $i:</td><td>";
	echo '<select name="' . jz_encode("newPType-$i") . '" class="jz_select">';
	echo '<option value="' . jz_encode("unchanged") . '">' . word("Unchanged") . '</option>';
	echo '<option value="' . jz_encode("genre") . '">' . word("Genre") . '</option>';
	echo '<option value="' . jz_encode("artist") . '">' . word("Artist") . '</option>';
	echo '<option value="' . jz_encode("album") . '">' . word("Album") . '</option>';
	echo '<option value="' . jz_encode("disk") . '">' . word("Disk") . '</option>';
	echo '<option value="' . jz_encode("generic") . '">' . word("Generic") . '</option>';
	echo '</select></td></tr>';
	$i++;
}
echo "</table>";
echo '<br><input type="submit" name="updatePType" value="' . word("Update Type") . '" class="jz_submit">';
echo ' <input type="submit" name="edit_auto_set_ptype" value="' . word("Auto Set Page Type") . '" class="jz_submit">';
echo " ";
$this->closeButton();
echo '</form>';

$this->closeBlock();
exit ();
?>
