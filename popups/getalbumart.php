<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	/**
	* This tools displays a page of art so that the user can choose which one they want
	*
	* @author Ross Carlson
	* @since 03/07/05
	* @version 03/07/05
	* @param $node The node we are looking at
	*
	**/
		global $allow_filesystem_modify, $backend, $include_path,$node;
		
		// Now let's see if they choose an image
		$i=0;
		while($i<5){
			if (isset($_POST['edit_download_'. $i])){
				// Ok, we got it, now we need to write this out
				$image = $_POST['edit_image_'. $i];
				$imageData = file_get_contents($image);
				
				// now let's set the path for the image
				if (stristr($backend,"id3") or $allow_filesystem_modify == "false"){
					$imgFile = $include_path. "data/images/". str_replace("/","--",$node->getPath("String")). "--". $node->getName(). ".jpg";
				} else {
					$imgFile = $node->getDataPath(). "/". $node->getName(). ".jpg";
				}
				
				// Now let's delete it if it already exists
				if (is_file($imgFile)){ unlink($imgFile); }
				// Now we need to see if any resized versions of it exist
				$retArray = readDirInfo($include_path. "data/images","file");
				foreach($retArray as $file){
					if (stristr($file,str_replace("/","--",$node->getPath("String")). "--". $node->getName())){	
						// Ok, let's wack it
						@unlink($include_path. "data/images/".$file);
					}
				}

				// Now let's get the data and add it to the node
				$handle = fopen($imgFile, "w");
				if (fwrite($handle,$imageData)){
					// Ok, let's write it to the backend
					$node->addMainArt($imgFile);
				}
				fclose ($handle);
				
				// now let's close out
				$this->closeWindow(true);
				exit();
			}
			$i++;
		}
	
		// Let's resize
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT"><!--\
			window.resizeTo(500,700)
		-->
		</SCRIPT>
		<?php	
		flushdisplay();
		
		$display = new jzDisplay();
	
		$this->displayPageTop("","Searching for art for: ". $node->getName());
		$this->openBlock();
		
		echo word('Searching, please wait...'). "<br><br>";
		flushdisplay();
		
		// Now let's display what we got
		$i=0;
		echo "<center>";
		// Let's setup our form
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "getalbumart";
		$arr['jz_path'] = $node->getPath('String');
		echo '<form action="'. urlize($arr). '" method="POST">';
		
		$i=0;
		// Ok, now let's setup a service to get the art for each of the providers
		// Now let's get a link from Amazon
		$jzService = new jzServices();		
		$jzService->loadService("metadata", "amazon");
		$image = $jzService->getAlbumMetadata($node, false, "image");
		if (strlen($image) <> 0){
			echo '<img src="'. $image. '" border="0"><br>';
			echo $display->returnImageDimensions($image);
			echo '<br><br>';
			echo '<input type="hidden" value="'. $image. '" name="edit_image_'. $i. '">';
			echo '<input type="submit" name="edit_download_'. $i. '" value="'. word('Download'). '" class="jz_submit"><br><br><br>';
			$i++;
		}
		flushdisplay();
		
		// Now let's get a link from Rollingstone
		unset($jzService);unset($image);
		$jzService = new jzServices();		
		$jzService->loadService("metadata", "google");
		$image = $jzService->getAlbumMetadata($node, false, "image");
		if (strlen($image) <> 0){
			echo '<img src="'. $image. '" border="0"><br>';
			echo $display->returnImageDimensions($image);
			echo '<br><br>';
			echo '<input type="hidden" value="'. $image. '" name="edit_image_'. $i. '">';
			echo '<input type="submit" name="edit_download_'. $i. '" value="'. word('Download'). '" class="jz_submit"><br><br><br>';
			$i++;
		}
		flushdisplay();
		
		// Now let's get a link from Rollingstone
		unset($jzService);unset($image);
		$jzService = new jzServices();		
		$jzService->loadService("metadata", "rs");
		$image = $jzService->getAlbumMetadata($node, false, "image");
		if (strlen($image) <> 0){
			echo '<img src="'. $image. '" border="0"><br>';
			echo $display->returnImageDimensions($image);
			echo '<br><br>';
			echo '<input type="hidden" value="'. $image. '" name="edit_image_'. $i. '">';
			echo '<input type="submit" name="edit_download_'. $i. '" value="'. word('Download'). '" class="jz_submit"><br><br><br>';
			$i++;
		}
		flushdisplay();
		
		// Now let's get a link from Rollingstone
		unset($jzService);unset($image);
		$jzService = new jzServices();		
		$jzService->loadService("metadata", "msnmusic");
		$image = $jzService->getAlbumMetadata($node, false, "image");
		if (strlen($image) <> 0){
			echo '<img src="'. $image. '" border="0"><br>';
			echo $display->returnImageDimensions($image);
			echo '<br><br>';
			echo '<input type="hidden" value="'. $image. '" name="edit_image_'. $i. '">';
			echo '<input type="submit" name="edit_download_'. $i. '" value="'. word('Download'). '" class="jz_submit"><br><br><br>';
			$i++;
		}
		flushdisplay();
		
		// Now let's get a link from Musicbrainz
		unset($jzService);unset($image);
		$jzService = new jzServices();		
		$jzService->loadService("metadata", "musicbrainz");
		$image = $jzService->getAlbumMetadata($node, false, "image");
		if (strlen($image) <> 0){
			echo '<img src="'. $image. '" border="0"><br>';
			echo $display->returnImageDimensions($image);
			echo '<br><br>';
			echo '<input type="hidden" value="'. $image. '" name="edit_image_'. $i. '">';
			echo '<input type="submit" name="edit_download_'. $i. '" value="'. word('Download'). '" class="jz_submit"><br><br><br>';
			$i++;
		}
		flushdisplay();
		echo "<br>";
		$this->closeButton();
		echo "</form></center>";		
		
		$this->closeBlock();

?>
