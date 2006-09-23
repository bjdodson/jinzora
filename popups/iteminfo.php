<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');

/**
* Displays the Item Information editing tool
* 
* @author Ross Carlson, Ben Dodson
* @version 01/27/05
* @since 01/27/05
* @param $node The node we are looking at
*/
global $row_colors, $include_path, $allow_filesystem_modify, $resize_images, $jzSERVICES, $jzUSER;

if (!checkPermission($jzUSER, "admin", $node->getPath("String"))) {
	$this->itemInformation($node);
	return;
}
// Ok, did they submit this form?
if (isset ($_POST['closeupdate']) or isset ($_POST['updatedata'])) {
	// Alright, they wanted to update

	// Let's update the descriptions
	if ($allow_filesystem_modify == "true") {
		$fName = $node->getDataPath("String") . "/album-desc.txt";
		$handle = @ fopen($fName, "w");
		@ fwrite($handle, $_POST['edit_long_desc']);
		@ fclose($handle);
	}
	$node->addShortDescription($_POST['edit_short_desc']);
	$node->addDescription($_POST['edit_long_desc']);

	// Now, did they update the ID
	if ($_POST['edit_item_id'] <> $node->getID()) {
		if ($allow_filesystem_modify == "true") {
			$fName = $node->getDataPath("String") . "/album.id";
			$handle = @ fopen($fName, "w");
			@ fwrite($handle, $_POST['edit_item_id']);
			@ fclose($handle);
		}
		$node->setID($_POST['edit_item_id']);
	}

	// Now, did they update the year?
	if ($_POST['edit_item_year'] <> $node->getYear()) {
		// Ok, now we need to update the year on this node
		$meta['year'] = $_POST['edit_item_year'];
		$dirtyFlag = true;

		// Now let's update the cache for the node
		//$node->updateCache(true, false, false, true);
	}

	// Now let's update the image, IF they did
	if ($_FILES['edit_thumbnail']['name'] <> "") {
		// Ok, now we need to put it into the data dir
		// First let's get the name of the new image
		if ($allow_filesystem_modify == "true") {
			$imgName = $node->getDataPath("String") . "/" . $node->getName() . ".jpg";

			//NOTE: this should really be put in the general lib
			//*ALL* filenames should be given this treatment
			$imgName = preg_replace("/(:|\*|\?|<|>|\'|\"|\|)/", "", $imgName);
		} else {
			$imgName = $include_path . "data/images/" . str_replace("/", "---", $node->getPath("String")) . ".jpg";
			$imgName = preg_replace("/(:|\*|\?|<|>|\'|\"|\|)/", "", $imgName);
		}

		// Now let's kill the old image if it's there
		if (is_file($imgName)) {
			unlink($imgName);
		}

		// Now let's put the new file in place
		//echo $_FILES['edit_thumbnail']['tmp_name']; exit();
		if (copy($_FILES['edit_thumbnail']['tmp_name'], $imgName)) {

			// Now let's set the permissions
			chmod($imgName, 0666);
			// Now let's add it to the node
			$node->addMainArt($imgName);

			//Regenerate the thumbnail images
			//HACK: don't know a better way to get the filename we're trying to write to
			//Don't unlink the image if it wasn't resized

			$retVal100 = $jzSERVICES->resizeImage($imgName, "100x100");
			if (!strcmp($imgName, $retVal100)) {
				@ unlink($retVal100);
			}

			$retVal150 = $jzSERVICES->resizeImage($imgName, "150x150");
			if (!strcmp($imgName, $retVal150)) {
				@ unlink($retVal150);
			}

			$retVal200 = $jzSERVICES->resizeImage($imgName, "200x200");
			if (!strcmp($imgName, $retVal150)) {
				@ unlink($retVal200);
			}
		}
	}

	if (isset ($_POST['edit_delete_thumb'])) {
		$node->addMainArt('');
	}

	// Now do we need to close out?
	if (isset ($_POST['closeupdate'])) {
		$this->closeWindow(true);
	}

	// Did they want to rotate the art?
	if (isset ($_POST['edit_rotate_image'])) {
		$jzSERVICES->rotateImage($node->getMainArt(), $node);
	}

	// Now update the id3 tag with the image
	if ($_FILES['edit_thumbnail']['name'] <> "" && isset ($_POST['edit_image_to_id3'])) {

		$imageData = file_get_contents($imgName);
		$image_info = getimagesize($imgName);

		// From getimagesize(); 1 = jpeg, 2 = gif
		$mimeType = null;
		$picExt = null;
		if ($image_info[2] == 1) {
			$mimeType = "image/jpeg";
			$picExt = "jpg";
		} else
			if ($image_info[2] == 2) {
				$mimeType = "image/gif";
				$picExt = "gif";
			}

		$needsResizing = ($image_info[0] > 200 || $image_info[1] > 200);

		// Ok, now let's resize this first
		// If art is too big it looks like shit in the players
		if ($needsResizing) {

			// Now let's write it out
			$file = $include_path . 'temp/tempimage.jpg';
			$dest = $include_path . 'temp/destimage.jpg';
			$handle = fopen($file, "w");
			fwrite($handle, $imageData);
			fclose($handle);

			// Now let's resize; do this for all standard images larger than 200x200
			// Note that if this fails, we just use the original image in the tag
			if (strcmp($jzSERVICES->resizeImage($file, "200x200", $dest), $imgName) != 0) {
				// Now let's get new data for the tag writing
				unset ($imageData);
				$imageData = file_get_contents($dest);

				// Reset the mime type, since we're probably converting the image to a jpg
				// regardless of the input type
				$new_image_info = getimagesize($dest);
				$mimeType = null;
				$picExt = null;

				// From getimagesize(); 1 = jpeg, 2 = gif
				if ($new_image_info[2] == 1) {
					$mimeType = "image/jpeg";
					$picExt = "jpg";
				} else
					if ($new_image_info[2] == 2) {
						$mimeType = "image/gif";
						$picExt = "gif";
					} else {
						// currently unsupported type
						$mimeType = null;
					}
			}

			// Now let's clean up
			@ unlink($file);
			@ unlink($dest);
		}

		// Now let's make sure that was valid
		if (strlen($imageData) >= 2000 && $mimeType) {
			$dirtyFlag = true;

			$nameParts = explode('/', $imgName);
			$imgShortName = $nameParts[count($nameParts) - 1];

			$meta['pic_mime'] = $mimeType;
			$meta['pic_data'] = $imageData;
			$meta['pic_ext'] = $picExt;
			$meta['pic_name'] = $imgShortName;
		}
	}

	if ($dirtyFlag) {
		$node->bulkMetaUpdate($meta);
	}
}

$this->displayPageTop("", word("Item Information for") . ": " . $node->getName());
$this->openBlock();
$display = new jzDisplay();

// Let's setup our form
$arr = array ();
$arr['action'] = "popup";
$arr['ptype'] = "iteminfo";
$arr['jz_path'] = $_GET['jz_path'];
echo '<form action="' . urlize($arr) . '" method="POST" enctype="multipart/form-data">';

// Ok, now let's see what they can edit?
$i = 0;
?>
				<table class="jz_track_table" width="100%" cellpadding="5" cellspacing="0" border="0">
				   <?php

$artist = $node->getAncestor("artist");
if ($artist !== false) {
?>
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td width="30%" valign="top">
							<nobr>
								<?php echo word('Artist'); ?>:
							</nobr>
						</td>
						<td width="70%" valign="top">
							<!--<input type="text" name="edit_item_name" value="<?php echo $node->getName(); ?>" class="jz_input">-->
							<?php echo $artist->getName(); ?>
						</td>
					</tr>
						    <?php

}
$album = $node->getAncestor("album");
if ($album !== false) {
?>
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td width="30%" valign="top">
							<nobr>
								<?php echo word('Album'); ?>:
							</nobr>
						</td>
						<td width="70%" valign="top">
							<!--
							<select name="edit_item_parent" class="jz_select" style="width:185px;">
								<?php

	// Now let's get all the items at this level
	$root = new jzMediaNode();
	switch ($node->getPType()) {
		case "artist" :
			$valArr = $root->getSubNodes("nodes", distanceTo("genre"));
			break;
	}
	for ($e = 0; $e < count($valArr); $e++) {
		echo '<option ';
		if ($valArr[$e]->getName() == $parent->getName()) {
			echo ' selected ';
		}
		echo 'value="' . $valArr[$e]->getName() . '">' . $valArr[$e]->getName() . "</option>\n";
	}
?>
							</select>-->
							<?php echo $album->getName(); ?>
						</td>
					</tr>
					<?php

}
if ($node->getPType() == "album") {
?>
						<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
							<td width="30%" valign="top">
								<nobr>
									<?php echo word("Album year"); ?>
								</nobr>
							</td>
							<td width="70%" valign="top">
								<input type="text" name="edit_item_year" value="<?php echo $node->getYear(); ?>" class="jz_input" size="4">
							</td>
						</tr>
								    <?php

} else {
	echo '<input type="hidden" name="edit_item_year" value="' . $node->getYear() . '" class="jz_input" size="4">';
}
$be = new jzBackend();
if ($be->hasFeature('setID')) {
?>
									    
						<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
							<td width="30%" valign="top">
								    
								<nobr>
									<?php echo word("Item ID"); ?>
								</nobr>
							</td>
							<td width="70%" valign="top">
								<input type="text" name="edit_item_id" value="<?php echo $node->getID(); ?>" class="jz_input" size="20">
							</td>
						</tr>
					<?php

}
?>
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td width="30%" valign="top">
							<nobr>
								<?php echo word("Short Description"); ?>
							</nobr>
						</td>
						<td width="70%" valign="top">
							<textarea class="jz_input" name="edit_short_desc" style="width: 250px" rows="10"><?php echo $node->getShortDescription(); ?></textarea>
						</td>
					</tr>
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td width="30%" valign="top">
							<nobr>
								<?php echo word("Description"); ?>
							</nobr>
						</td>
						<td width="70%" valign="top">
							<textarea class="jz_input" name="edit_long_desc" style="width: 250px" rows="10"><?php echo $node->getDescription(); ?></textarea>
						</td>
					</tr>
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td width="30%" valign="top">
							<nobr>
								<?php echo $third_desc; ?>
							</nobr>
						</td>
						<td width="70%" valign="top">
							<?php

if (($art = $node->getMainArt()) <> false) {
	$display->image($art, $node->getName(), 150, 150, "limit", false, false, false, "", "");
	echo "<br>";
}
?>
							New Image:<br><input type="file" class="jz_input" name="edit_thumbnail" size="30"><br>
							<input type="checkbox" name="edit_image_to_id3"> <?php echo word("Apply image to ID3 tags"); ?>
							<input type="checkbox" name="edit_delete_thumb"> <?php echo word("Delete image"); ?>
						</td>
					</tr>
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td width="100%" colspan="2" valign="top" align="center">
							<br><br>
							<input type=submit class="jz_submit" name="<?php echo jz_encode('closeupdate'); ?>" value="<?php echo word("Update & Close"); ?>">
							<input type=submit class="jz_submit" name="<?php echo jz_encode('updatedata'); ?>" value="<?php echo word("Update"); ?>">
							<?php

if ($resize_images = "true") {
	//echo '<input type=submit class="jz_submit" name="edit_rotate_image" value="'. word("Rotate Image"). '">';
}
echo "<br><br>";
$this->closeButton();
?>
							<br><br><br>
						</td>
					</tr>
				</table>
				<?php

echo "</form>";

$this->closeBlock();
?>
