<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');


/**
* Displays the tool to let the user add a link track.
* 
* @author Ross Carlson, Ben Dodson
* @version 9/22/05
* @since 9/22/05
* @param $node The node we are looking at
*/
global $node;

$this->displayPageTop("", word("Add Link Track in") . ": " . $node->getName());
$this->openBlock();

if (isset ($_POST['edit_taddress'])) {
	$path = array ();
	$path[] = $_POST['edit_tname'];
	$tr = $node->inject($path, $_POST['edit_taddress']);
	if ($tr !== false) {
		$meta = $tr->getMeta();
		$meta['title'] = $_POST['edit_tname'];
		$tr->setMeta($meta);
	}

	echo word("Added") . ": " . $_POST['edit_tname'];
	echo " (" . $_POST['edit_taddress'] . ")";

	echo '<br><br>';
	$this->closeButton();
	$this->closeBlock();
	return;
}

// Let's show the form to edit with
$arr = array ();
$arr['action'] = "popup";
$arr['ptype'] = "addlinktrack";
$arr['jz_path'] = $node->getPath("String");
echo '<form action="' . urlize($arr) . '" method="POST">';
echo '<table><tr><td width="30%">';
echo word("Name") . ": ";
echo '</td><td>';
echo '<input name="edit_tname" class="jz_input">';
echo '</td></tr>';
echo '<tr><td>';
echo word("Address") . ": ";
echo '</td><td>';
echo '<input name="edit_taddress" class="jz_input">';
echo '</td></tr></table>';
echo '<br><br>';
echo '<input type="submit" class="jz_submit" value="' . word('Add Link') . '">';
$this->closeButton();
echo '</form>';
$this->closeBlock();
?>
