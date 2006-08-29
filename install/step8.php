<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	// Let's figure out the path stuff so we'll know how/where to include from$form_action = "index.php?install=step8";
	$form_action = setThisPage() . "install=step8";
	$form_action2 = setThisPage() . "install=step9";
	
	// Ok, did they want to download the settings file?
	if (isset($_POST['saveSettings'])){
		createSettings($error);
	}
	
	// Now let's create the functions to create the settings and users files
	function createSettings($save = false){
		global $word_written_success, $version, $include_path; 
		
		// Now let's figure out the root dir
		$rootArray = explode("/",$_SERVER['SCRIPT_NAME']);
		$rootArray[count($rootArray)-1] = "";
		$root_dir = "";
		for ($c=0; $c < count($rootArray); $c++){
			if ($rootArray[$c] <> ""){
				$root_dir .= "/". $rootArray[$c];
			}
		}		
		$root_dir = $root_dir. "/". $include_path;
		$root_dir = substr($root_dir,0,strlen($root_dir)-1);

		// Now let's figure out the version
		include_once($include_path. 'system.php');
		
		// Now let's create the dynamic variables from the installer
		if ($_POST['cms_type'] <> "standalone"){
			if ($_POST['frontend'] == "slick"){
				$jinzora_skin = "slick";
			} else {
				$jinzora_skin = "cms-theme";
			}
		} else {
			if ($_POST['frontend'] == "slick"){
				$jinzora_skin = "slick";
			} else {
				$jinzora_skin = "sandstone";
			}
		}
		
		// Ok, now let's include all the defaults
		include_once($include_path. 'install/defaults.php');
		$content .= "?>";
		
		if ($save){
			$filename = $include_path. 'settings.php';
			$handle = fopen($filename, "w");
			fwrite($handle,$content);	
			fclose ($handle);
			echo 'settings.php - <font color="green"><strong>'. $word_written_success. '</strong></font><br><br>';			
		} else {
			header ("Content-Type: text/html");
			header ('Content-Disposition: attachment; filename="settings.php"');
			echo $content;
			exit();
		}
	}

	// Now let's include the left
	include_once($include_path. 'install/leftnav.php');
?>
      
<div id="main">
	<a href="http://www.jinzora.com" target="_blank"><img src="<?php echo $include_path; ?>install/logo.gif" border="0" align="right" vspace="5" hspace="0"></a>
	<h1><?php echo $word_save_config; ?></h1>
	<p>
	<?php echo $word_save_config_note; ?>
	<div class="go">
		<span class="goToNext">
			<?php echo $word_saveing_config; ?>
		</span>
	</div>
	<br>
	<?php
		$mainError = false;
		// Now let's check to see if things are writeable
		$file = getcwd(). "/". $include_path. "settings.php";

		$error = true;
		if (!is_file($file)){
			if (@touch($file)){
				unlink($file);
				$error = false;
			}
		} else {
			if (is_writable($file)){
				$error = false;
			}
		}
		// Now let's see if it exists already and if so we'll know we're good
		if (isset($_POST['checkConfig'])){
			if (is_file($file)){$error = false; }
		}
		if ($error){
			$mainError = true;
			echo 'settings.php - <font color="red">'. $word_not_writable. '</font>';
			?>
			<a class="helpbox2" href="javascript:void(0);" onmouseover="return overlib('<?php echo $word_settings_perm_error; ?>');" onmouseout="return nd();">?</a>
			<?php
			echo '<br><br>';
		} else {		
			// Ok, now let's write out the settings file				
			createSettings(true);
		}
		
		// Now let's let them know
		if (!$mainError){
		
		} else {
			echo '<br><font color="red">'. $word_file_create_error. '</font>';
			?>
			<form action="<?php echo $form_action; ?>" name="setup8" method="post">
				<?php
					$PostArray = $_POST;
					foreach ($PostArray as $key => $val) {
					  if (!stristr($key,"submit") and !stristr($key,"save")){
						echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
					  }
				   }
				?>	
				<br><br><input type="submit" name="saveSettings" class="submit" value="<?php echo $word_download; ?> settings.php">
				<br><br><font color="red"><?php echo $word_download_and_continue; ?></font>
				<br><input type="submit" name="checkConfig" class="submit" value="<?php echo $word_check_config; ?>">
			</form>
			<?php
		}
		if (!$mainError){
			?>
				<br>
				<div class="go">
					<span class="goToNext">
						<form action="<?php echo $form_action2; ?>" name="setup8" method="post">
							<?php
								$PostArray = $_POST;
								foreach ($PostArray as $key => $val) {
								  if (!stristr($key,"submit")){
									echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
								  }
							   }
							?>	
							&nbsp; <input type="submit" name="submit_step8" class="submit" value="<?php echo $word_proceed_launch; ?>">
						</form>
					</span>
				</div>
			<?php   
		}
	?>	
	</div>
<?php
	// Now let's include the top
	include_once($include_path. 'install/footer.php');
?>
