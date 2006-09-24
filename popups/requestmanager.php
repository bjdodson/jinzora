<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
* Displays the request manager
*
* @author Ross Carlson
* @since 03/19/05
* @version 03/19/05
*
**/

global $jzUSER, $node;

$this->displayPageTop("", word("Request Manager"));
$this->openBlock();

// Now let's see if they wanted to add
if (isset ($_POST['edit_add'])) {
	$node->addRequest($_POST['edit_request'], '', $jzUSER->getName());
}
// Did they want to delete
if (isset ($_POST['edit_delete'])) {
	$node->removeRequest($_POST['edit_previous_requests']);
}

// Let's setup our form
$arr = array ();
$arr['action'] = "popup";
$arr['ptype'] = "requestmanager";
$arr['jz_path'] = $node->getPath('String');
echo '<form action="' . urlize($arr) . '" method="POST">';
?>
		<?php echo word('Enter your request below'); ?>:<br>
		<input type="text" name="edit_request" class="jz_input" size="30">
		<input type="submit" name="edit_add" class="jz_submit" value="<?php echo word('Go'); ?>">
		<br>
		<br>
		<br>
		<?php echo word('Current Requests'); ?>:<br>
		<select class="jz_select" name="edit_previous_requests" size="10" style="width:200px;">
			<?php

$req = $node->getRequests(-1, "all");
rsort($req);
for ($i = 0; $i < count($req); $i++) {
	echo '<option value="' . $req[$i]['id'] . '">' . $req[$i]['entry'] . '</option>';
}
?>
		</select>
		<br><br>
		    <?php

if ($jzUSER->getSetting('admin')) {
?>
			<input type="submit" name="edit_delete" class="jz_submit" value="<?php echo word('Delete'); ?>">
			<!--<input type="submit" name="edit_notify" class="jz_submit" value="<?php echo word("Notify requestor"); ?>">-->
			<?php

}

echo "</form>";

$this->closeBlock();
?>
