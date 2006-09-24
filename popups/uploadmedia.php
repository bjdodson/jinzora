<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');
/**
	* Allows the user to add media
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 03/01/05
	* @since 03/01/05
	*/
	
		global $audio_types, $video_types, $include_path, $root_dir, $jzUSER,$node;
		

		if (checkPermission($jzUSER,"upload",$node->getPath("String")) === false) {
			echo word("Insufficient permissions.");
			exit();
		}
		// Did they want to actually create the link track
		if (isset($_POST['edit_add_link_track'])){
			// Ok, let's add the link
			$node->inject(array($_POST['edit_link_track_name']), $_POST['edit_link_track_url'],"track");

			exit();
			$this->closeWindow(true);
		}
		
		// Let's open the page
		$this->displayPageTop("",word("Add Media"). ": ". $node->getName());
		$this->openBlock();
		
		// Did they want to create a link track
		// This will show them the form
		if (isset($_POST['add_link_track'])){
			$arr = array();
			$arr['action'] = "popup";
			$arr['ptype'] = "uploadmedia";
			$arr['jz_path'] = $_GET['jz_path'];
			echo '<form action="'. urlize($arr). '" method="POST">';
			echo '<table class="jz_track_table" width="100%" cellpadding="3">';
			echo '<tr><td align="right">';
			echo word("Track Name"). ":";
			echo '</td><td>';
			echo '<input type="text" name="edit_link_track_name" class="jz_input" size="30">';
			echo '</td></tr>';
			echo '<tr><td align="right">';
			echo word("Track URL"). ":";
			echo '</td><td>';
			echo '<input type="text" name="edit_link_track_url" class="jz_input" size="30">';
			echo '</td></tr>';
			echo '</table>';
			echo '<br><center>';
			echo '<input type="submit" name="edit_add_link_track" value="'. word("Add Link Track"). '" class="jz_submit"></form> ';
			$this->closeButton(true);
			exit();
		}
		
		// Ok, did they want to uploade?
		if (isset($_POST['uploadfiles'])){
			// First let's flushout the display
			flushdisplay();
			
			echo word("Writing out files, please stand by..."). "<br><br>";
			echo '<div id="status"></div>';
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				s = document.getElementById("status");
				-->
			</SCRIPT>
			<?php
			// BEN PUT THIS IN:
			// I'm not sure what it's supposed to be set to.
			// fixing a PHP warning.
			$c=0;
			// Ok, did they want to add a new sub location
			if (isset($_POST['edit_new_sub'])){
				// Ok, we need to create that new dir
				$newDir = $node->getDataPath("String"). "/". $_POST['edit_new_sub'];
				// Now we need to make sure that exsists
				$dArr = explode("/",$newDir);
				$newDir = "";
				for ($i=0;$i<count($dArr)+$c;$i++){
					if ($dArr[$i] <> ""){
						// Now let's build the newdir
						$newDir .= "/". $dArr[$i];
						if (!is_dir($newDir)){
							mkdir($newDir);
							chmod($newDir,0666);
							?>
							<SCRIPT LANGUAGE=JAVASCRIPT><!--\
								s.innerHTML = '<nobr><?php echo word("Status: Creating Dir:"); ?> <?php echo $dArr[$i]; ?></nobr>';
								-->
							</SCRIPT>
							<?php
							flushdisplay();
							sleep(1);
						}
					}
				}
			} else {
				$newDir =  $node->getDataPath("String");
			}
			$c=0;
			for ($i=1;$i<6;$i++){
				// Now let's see what they uploaded
				if ($_FILES['edit_file'. $i]['name'] <> ""){
					// Ok, They wanted to upload file #1, let's do it
					$newLoc = $newDir. "/". $_FILES['edit_file'. $i]['name'];
					// Ok, now that we've got the new name let's put it there
					if (copy($_FILES['edit_file'. $i]['tmp_name'], $newLoc)){
						// Now let's set the permissions
						chmod($newLoc, 0666);
						?>
						<SCRIPT LANGUAGE=JAVASCRIPT><!--\
							s.innerHTML = "<nobr><?php echo word('Status: Adding File:'); ?> <?php echo $_FILES['edit_file'. $i]['name']; ?></nobr>";
							-->
						</SCRIPT>
						<?php
						flushdisplay();
						sleep(1);
						$c++;
						// Ok, now was this a zip file?
						if (substr($_FILES['edit_file'. $i]['name'],-4) == ".zip"){
							?>
							<SCRIPT LANGUAGE=JAVASCRIPT><!--\
								s.innerHTML = "<nobr><?php echo word('Status: Extracting files in:'); ?> <?php echo $_FILES['edit_file'. $i]['name']; ?></nobr>";
								-->
							</SCRIPT>
							<?php
							flushdisplay();
							sleep(1);
							include_once($include_path. "lib/pclzip.lib.php");
							$zipfile = $newLoc;
							$archive = new PclZip($zipfile);
							if ($archive->extract(PCLZIP_OPT_PATH, $newDir) == 0) {
								?>
								<SCRIPT LANGUAGE=JAVASCRIPT><!--\
									s.innerHTML = "<nobr><?php echo word('Status: Extracting files in:'); ?> <?php echo $_FILES['edit_file'. $i]['name']; ?>!</nobr>";
									-->
								</SCRIPT>
								<?php
								flushdisplay();
							} else {
								$fileList = $archive->listContent();
								for ($i=0; $i < count($fileList); $i++){
									?>
									<SCRIPT LANGUAGE=JAVASCRIPT><!--\
										s.innerHTML = "<nobr><?php echo word('Status: Extracting file:'); ?> <?php echo $fileList[$i]['filename']; ?></nobr>";
										-->
									</SCRIPT>
									<?php
									flushdisplay();
									sleep(1);
									$c++;
								}
								$c=$c-1;
							}
							flushdisplay();
							// Now let's unlink that file
							unlink($zipfile);
						}
					}
				}
			}
			
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				s.innerHTML = "<nobr><?php echo word('Status: Upload Complete!'); ?><br><?php echo $c; ?> <?php echo word('files uploaded'); ?></nobr>";
				-->
			</SCRIPT>
			<?php
			flushdisplay();
			sleep(1);
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT"><!--\
				thisWin = window.open('','StatusPop','');
				thisWin.close();
			-->
			</SCRIPT>
			<?php	
			echo '<br><br><center>';	
			$this->closeButton();	
			echo '</center>';
			exit();
		}
		// Did they just want to close?
		if (isset($_POST['justclose'])){
			$this->closeWindow(false);
		}
		
		echo word('When uploading you may upload single files or zip files containing all the files you wish to upload.  These will then be extracted once they have been uploaded.  You may also add your descritpion files and album art now and they will be displayed.  The following media types are supported by this system and may be uploaded:');
		echo "<br><br>". word('Audio'). ": ". $audio_types. "<br>". word('Video'). ": ". $video_types;
		echo "<br><br>";
		
		// Now let's start our form so they can upload
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "uploadmedia";
		$arr['jz_path'] = $_GET['jz_path'];
		echo '<form action="'. urlize($arr). '" method="POST" enctype="multipart/form-data">';
		?>		
		<center>
			<?php echo word("New Sub Path"); ?>: <br>
			<input type="text" name="edit_new_sub" class="jz_input" size="40"><br><br>
			<?php echo word('File'); ?> 1: <input type="file" name="edit_file1" class="jz_input" size="40"><br>
			<?php echo word('File'); ?> 2: <input type="file" name="edit_file2" class="jz_input" size="40"><br>
			<?php echo word('File'); ?> 3: <input type="file" name="edit_file3" class="jz_input" size="40"><br>
			<?php echo word('File'); ?> 4: <input type="file" name="edit_file4" class="jz_input" size="40"><br>
			<?php echo word('File'); ?> 5: <input type="file" name="edit_file5" class="jz_input" size="40"><br>
			<br><br>
			<input type=submit class="jz_submit" name="<?php echo jz_encode('justclose'); ?>" value="<?php echo word('Close'); ?>">
			<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT"><!--\
								function				 openStatusPop(obj, boxWidth, boxHeight){
					var sw = screen.width;
					var sh = screen.height;
					var winOpt = "width=" + boxWidth + ",height=" + boxHeight + ",left=" + ((sw - boxWidth) / 2) + ",top=" + ((sh - boxHeight) / 2) + ",menubar=no,toolbar=no,location=no,directories=no,status=yes,scrollbars=yes,resizable=no";
					thisWin = window.open(obj,'StatusPop',winOpt);
				}	
			-->
			</SCRIPT>
			<?php
				$aRR = array();
				$aRR['action'] = "popup";
				$aRR['ptype'] = "showuploadstatus";
			?>
			<input onMouseDown="openStatusPop('<?php echo urlize($aRR); ?>',300,200)" type=submit class="jz_submit" name="<?php echo jz_encode('uploadfiles'); ?>" value="<?php echo word('Upload'); ?>">
			<!--<input type=submit class="jz_submit" name="<?php echo jz_encode('add_link_track'); ?>" value="<?php echo word('Add Link Track'); ?>">-->
		</center>
		<?php
		echo '</form>';
		
		$this->closeBlock();

?>
