<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');

/**
* Searches for meta data of the given node
* 
* @author Ross Carlson
* @version 01/18/05
* @since 01/18/05
* @param $node The node we are looking at
*/
global $jzSERVICES, $row_colors, $allow_id3_modify, $include_path, $allow_filesystem_modify, $node;

set_time_limit(0);

// First let's display the top of the page and open the main block
$this->displayPageTop("", word("Retrieving meta data for") . ":<br>" . $node->getName());
$this->openBlock();

// Let's show them the form so they can pick what they want to do
if (!isset ($_POST['metaSearchSubmit']) and !isset ($_POST['edit_meta_search_step'])) {
	$url_array = array ();
	$url_array['action'] = "popup";
	$url_array['ptype'] = "getmetadata";
	$url_array['jz_path'] = $_GET['jz_path'];
	$i = 0;
?>
			<form action="<?php echo urlize($url_array); ?>" method="post">
				<?php echo word("Search for"); ?>:<br>
				<input checked type="checkbox" name="edit_search_all_albums"> <?php echo word("Album data"); ?>
				<br>
				<?php

	if ($node->getPType() <> "album") {
?>
						<input checked type="checkbox" name="edit_search_all_artists"> <?php echo word("Artist data"); ?><br>
						<?php

	} else {
		echo '<input type="hidden" name="edit_search_all_artists" value="off">';
	}
?>
				<br>
				<?php echo word("Data to retrieve"); ?>:<br>
				<table width="100%" cellpadding="3" cellspacing="0">
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td valign="top" width="10%">
							<nobr>
							<input checked type="checkbox" name="edit_search_images"> <?php echo word("Images"); ?>
							<nobr>
						</td>
						<td valign="top" width="90%">
							<input value="miss" checked type="radio" name="edit_search_images_miss"> <?php echo word("If missing"); ?><br>
							<input value="always" type="radio" name="edit_search_images_miss"> <?php echo word("Always"); ?>
						</td>
					</tr>
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td valign="top" width="10%">
							<nobr>
							<input checked type="checkbox" name="edit_search_desc"> <?php echo word("Descriptions"); ?>
							<nobr>
						</td>
						<td valign="top" width="90%">
							<input value="miss" checked type="radio" name="edit_search_desc_miss"> <?php echo word("If missing"); ?><br>
							<input value="always" type="radio" name="edit_search_desc_miss"> <?php echo word("Always"); ?>
						</td>
					</tr>
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td valign="top" width="10%">
							<nobr>
							<input checked type="checkbox" name="edit_search_rating"> <?php echo word("Rating"); ?>
							<nobr>
						</td>
						<td valign="top" width="90%">
							<input value="miss" checked type="radio" name="edit_search_rating_miss"> <?php echo word("If missing"); ?><br>
							<input value="always" type="radio" name="edit_search_rating_miss"> <?php echo word("Always"); ?>
						</td>
					</tr>
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td valign="top" width="10%">
							<nobr>
							<input checked type="checkbox" name="edit_search_year"> <?php echo word("Year"); ?>
							<nobr>
						</td>
						<td valign="top" width="90%">
							<input value="miss" checked type="radio" name="edit_search_year_miss"> <?php echo word("If missing"); ?><br>
							<input value="always" type="radio" name="edit_search_year_miss"> <?php echo word("Always"); ?>
						</td>
					</tr>
				</table>
				<br>
				<input type="submit" name="<?php echo jz_encode("metaSearchSubmit"); ?>" value="<?php echo word("Search"); ?>" class="jz_submit">
				<!--<input type="submit" name="<?php echo jz_encode("edit_meta_search_step"); ?>" value="<?php echo word("Search"); ?>" class="jz_submit">-->
			</form>
			<?php

	$this->closeButton();
	$this->closeBlock();
	exit ();
}

// Did they want to verify the search?
if (isset ($_POST['edit_meta_search_step'])) {
	$this->stepMetaSearch($node);
}
?>
		<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT"><!--\
			window.resizeTo(500,800)
		-->
		</SCRIPT>
		<?php

flushdisplay();

// Ok, they submitted the form, let's do what they wanted						
echo word("Searching, please wait...") . "<br><br>";
echo '<div id="artist"></div>';
echo '<div id="arStatus"></div>';
echo '<div id="count"></div>';
echo '<div id="art"></div>';
?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			ar = document.getElementById("artist");
			ars = document.getElementById("arStatus");
			c = document.getElementById("count");
			i = document.getElementById("art");
			-->
		</SCRIPT>
		<?php


flushdisplay();
// Now let's search, first we need to get all the nodes from here down
$nodes = $node->getSubNodes("nodes", -1);

// Now let's add the node for what we are viewing
$nodes = array_merge(array (
	$node
), $nodes);
$total = count($nodes);
$c = 0;
$start = time();

foreach ($nodes as $item) {
	// Now let's see if this is an artist
	if ($item->getPType() == 'artist') {
		// Now do we want to look at this?
		if ($_POST['edit_search_all_artists'] == "on") {
			// Ok, let's get data for this artist
?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						ar.innerHTML = '<nobr><?php echo word("Artist"); ?>: <?php echo $item->getName(); ?></nobr>';					
						ars.innerHTML = '<?php echo word("Status: Searching..."); ?>';
						-->
					</SCRIPT>
					<?php

			flushdisplay();
			// Now let's get the data IF we should
			if ($_POST['edit_search_images_miss'] == "always" or ($item->getMainArt() == "") or $_POST['edit_search_desc_miss'] == "always" or ($item->getDescription() == "")) {

				$arr = array ();
				$arr = $jzSERVICES->getArtistMetadata($item, true, "array");
				// Now let's see if they want to get art or need to
				if (($_POST['edit_search_images_miss'] == "always" or $item->getMainArt() == "") and $arr['image'] <> "") {
?>
							<SCRIPT LANGUAGE=JAVASCRIPT><!--\
								i.innerHTML = '<br><center><?php echo word("Last Image Found"). "<br>". $item->getName();?><br><img src="<?php echo $arr["image"]; ?>"><center>';					
								-->
							</SCRIPT>
							<?php

					flushdisplay();
					// Ok, we want the art
					writeArtistMetaData($item, $arr['image'], false, true);
				}
				if (($_POST['edit_search_desc_miss'] == "always" or $item->getDescription() == "") and $arr['bio'] <> "") {
					writeArtistMetaData($item, false, $arr['bio'], true);
				}
			}
		}
	}
	// Now let's look at the album
	if ($item->getPType() == 'album') {
		$parent = $item->getParent();
		$artist = $parent->getName();
		if ($_POST['edit_search_all_albums'] == "on") {
?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						ar.innerHTML = '<nobr><?php echo word("Album"); ?>: <?php echo $item->getName(). "<br>". word("Artist"). ": ". $artist; ?></nobr>';			
						ars.innerHTML = '<?php echo word("Status: Searching..."); ?>';					
						-->
					</SCRIPT>
					<?php

			flushdisplay();

			$arr = array ();
			$arr = $jzSERVICES->getAlbumMetadata($item, true, "array");

			// Ok, now should we do the art?
			if (($_POST['edit_search_images_miss'] == "always" or $item->getMainArt() == "") and $arr['image'] <> "") {
?>
						<SCRIPT LANGUAGE=JAVASCRIPT><!--\
							ars.innerHTML = '<?php echo word("Status: Writing image"); ?>';		
							i.innerHTML = '<br><center><?php echo word("Last Image Found"). "<br>". $item->getName();?><br><img src="<?php echo $arr["image"]; ?>"></center>';			
							-->
						</SCRIPT>
						<?php

				flushdisplay();
				writeAlbumMetaData($item, false, $arr['image']);
			} else {
				unset ($arr['image']);
			}
			// Ok, now should we do the description?
			if (($_POST['edit_search_desc_miss'] == "always" or $item->getDescription() == "") and $arr['review'] <> "") {
?>
						<SCRIPT LANGUAGE=JAVASCRIPT><!--\
							ars.innerHTML = '<?php echo word("Status: Writing review"); ?>';					
							-->
						</SCRIPT>
						<?php

				flushdisplay();
				writeAlbumMetaData($item, false, false, false, $arr['review']);
				usleep(250000);
			} else {
				unset ($arr['review']);
			}
			// Ok, now should we do the year?
			if ($_POST['edit_search_year_miss'] == "always" or $item->getYear() == "") {
			} else {
				unset ($arr['year']);
			}
			// Ok, now should we do the rating?
			if ($_POST['edit_search_rating_miss'] == "always" and $arr['rating'] <> "") {
?>
						<SCRIPT LANGUAGE=JAVASCRIPT><!--\
							ars.innerHTML = '<?php echo word("Status: Writing rating"); ?>';					
							-->
						</SCRIPT>
						<?php

				flushdisplay();
				writeAlbumMetaData($item, false, false, false, false, $arr['rating']);
				usleep(250000);
			}

			// Now let's write the ID to the database
			if ($arr['id'] <> "" and $arr['id'] <> "NULL") {
?>
						<SCRIPT LANGUAGE=JAVASCRIPT><!--\
							ars.innerHTML = '<?php echo word("Status: Updating Amazon ID"); ?>';					
							-->
						</SCRIPT>
						<?php

				if ($allow_filesystem_modify == "true") {
					$fName = $item->getDataPath("String") . "/album.id";
					$handle = @ fopen($fName, "w");
					@ fwrite($handle, $arr['id']);
					@ fclose($handle);
				}
				$item->setID($arr['id']);
			}

			// Did they want to write this to the id3 tags?
			if ($allow_id3_modify == "true" and (isset ($arr['year']) or isset ($arr['image']))) {
?>
						<SCRIPT LANGUAGE=JAVASCRIPT><!--\
							ars.innerHTML = '<?php echo word("Status: Updating tracks..."); ?>';					
							-->
						</SCRIPT>
						<?php

				flushdisplay();

				// Now let's set the meta fields so they get updated for all the tracks
				if (isset ($arr['year'])) {
					$meta['year'] = $arr['year'];
				}
				if (isset ($arr['image'])) {
					// Ok, now let's resize this first
					// If art is too big it looks like shit in the players
					$imageData = file_get_contents($arr['image']);

					// but only if the image is larger than 200x200
					$image_info = getimagesize($arr['image']);

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
					if (strlen($imageData) < 2000 or !$mimeType) {
						$imageData = "";
					} else {

						$imgShortName = $item->getName() . ".jpg";

						$meta['pic_mime'] = 'image/jpeg';
						$meta['pic_data'] = $imageData;
						$meta['pic_ext'] = "jpg";
						$meta['pic_name'] = $imgShortName;
					}
				}

				if (isset ($arr['image']) or $meta['year'] <> "") {
					// Now let's update
					$item->bulkMetaUpdate($meta, false, false);
				}
			}
?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						ars.innerHTML = '<?php echo word("Status: Complete!"); ?>';					
						-->
					</SCRIPT>
					<?php

			flushdisplay();
			unset ($arr);
		}
	}
	// Now let's figure out the progress
	$c++;
?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				c.innerHTML = '<?php echo word("Progress: "). $c. "/". $total; ?>';					
				-->
			</SCRIPT>
			<?php

	flushdisplay();
}

// Now let's purge the cache
?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			ars.innerHTML = '&nbsp;';					
			c.innerHTML = '&nbsp;';					
			ar.innerHTML = '<?php echo word("Purging cache"). "..."; ?>';			
			i.innerHTML = '&nbsp;';					
			-->
		</SCRIPT>
		<?php

flushdisplay();
$display = new jzDisplay();
if ($node->getPType() == "artist") {
	$display->purgeCachedPage($node);
} else {
	$parent = $node->getAncestor("artist");
	$display->purgeCachedPage($parent);
}
?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			ars.innerHTML = '&nbsp;';					
			c.innerHTML = '&nbsp;';					
			ar.innerHTML = '<?php echo word("Complete!"); ?>';			
			i.innerHTML = '&nbsp;';					
			-->
		</SCRIPT>
		<?php

echo "<br><center>";
$this->closeButton(true);
exit ();
?>
