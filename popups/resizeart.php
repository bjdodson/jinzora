<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

   /**
	* Goes through each subnode, one by one, and resizes the art
	*
	* @author Ross Carlson
	* @since 04/05/05
	* @version 04/05/05
	* @param $node object The node we are viewing
	*
	**/
global $node;

$this->displayPageTop("", word("Resize all album art"));
$this->openBlock();

// Did they submit?
if (isset ($_POST['edit_resize_art'])) {
	// Let's set the start time
	$start = time();

	echo word("Resizing, please wait...");
	echo "<br><br>";
	echo '<div id="artist"></div>';
	echo '<div id="album"></div>';
	echo '<div id="total"></div>';
	// Ok, now we need to recurisvely get ALL subnodes
	$i = 0;
	$nodes = $node->getSubNodes("nodes", -1);
?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				ar = document.getElementById("artist");
				a = document.getElementById("album");
				t = document.getElementById("total");
				-->
			</SCRIPT>
			<?php

	foreach ($nodes as $node) {
		if ($node->getName() <> "" and $node->getPtype() == "album") {
			$parent = $node->getParent();
?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						ar.innerHTML = '<nobr><?php echo word("Artist"); ?>: <?php echo $parent->getName(); ?></nobr>';					
						a.innerHTML = '<nobr><?php echo word("Album"); ?>: <?php echo $node->getName(); ?></nobr>';					
						t.innerHTML = '<nobr><?php echo word("Analyzed"); ?>: <?php echo $i; ?></nobr>';					
						-->
					</SCRIPT>
					<?php

			flushdisplay();
			// Now let's look at the art for this item and resize it if needed
			// BUT we don't want to create blank ones with this tool...
			$node->getMainArt($_POST['edit_resize_dim'], false);
			$i++;
		}
	}
?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				ar = document.getElementById("artist");
				a = document.getElementById("album");
				ar.innerHTML = '<nobr><?php echo word("Completed in"). " ". convertSecMins((time() - $start)). " ". word("seconds"); ?></nobr>';					
				a.innerHTML = '<nobr><?php echo word("Analyzed"); ?>: <?php echo $i; ?></nobr>';							
				t.innerHTML = '&nbsp;';							
				-->
			</SCRIPT>
			<?php

	flushdisplay();
	echo "<br><br><center>";
	$this->closeButton();
	exit ();
}

// Let's setup our form
$arr = array ();
$arr['action'] = "popup";
$arr['ptype'] = "resizeart";
$arr['jz_path'] = $node->getPath('String');
echo '<form action="' . urlize($arr) . '" method="POST">';
?>
		<?php echo word("This tool will resize all your art to the specified dimensions below.  This will not delete or remove your existing art.  This will precreate the art for tools like the random albums so that it will run faster."); ?>
		<br><br>
		<?php echo word("100x100 is used by the random album block<br>Other common values are 75x75 and 150x150"); ?>
		<br>
		<br>
		<?php echo word("Dimensions (WidthxHeight)"); ?><br><input type="text" class="jz_input" name="edit_resize_dim" value="100x100">
		<br><br>
		<input type="submit" class="jz_submit" value="<?php echo word("Resize All Art"); ?>" name="edit_resize_art">
		<?php

$this->closeButton();
echo '</form>';

$this->closeBlock();
?>
