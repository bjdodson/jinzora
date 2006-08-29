<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	echo '<body onLoad="setup6.db_username.focus();"></body>';
	
	// Let's figure out the path stuff so we'll know how/where to include from$form_action = "index.php?install=step7";
	$form_action = setThisPage() . "install=step7";

	// Now let's include the left
	include_once($include_path. 'install/leftnav.php');
?>
      
<div id="main">
	<a href="http://www.jinzora.com" target="_blank"><img src="<?php echo $include_path; ?>install/logo.gif" border="0" align="right" vspace="5" hspace="0"></a>
	<?php
		$backend = $_POST['backend'];
		$importer = $_POST['importer'];
		if (!file_exists($include_path. "backend/backends/$backend")) {
			die("Invalid backend.");
		}
		if (!file_exists($include_path. "services/services/importing/${importer}.php")) {
			die("Invalid importer.");
		}
		require_once($include_path. 'backend/backend.php');
		
		if (!isset($_POST['customhierarchy'])){$_POST['customhierarchy']="";}
		if ($_POST['hierarchysource'] == "custom") {
			if ($_POST['customhierarchy'] == "") {
				die ("Please specify your layout.");
			}
			$hierarchy = $_POST['customhierarchy'];
		} else if (strlen($_POST['customhierarchy']) > 1) {
			die("If you want a custom layout, please select 'custom'.");
		} else {
			$hierarchy = $_POST['hierarchy'];
		}
		
		$hierarchy = explode('/',$hierarchy);

		$mhierarchy = array(); $j = 0;
		for ($i = 0; $i < sizeof($hierarchy); $i++) {
			if ($hierarchy[$i] != "") {
				$mhierarchy[$j] = $hierarchy[$i];
				$j++;

				if (!validateLevel($mhierarchy[$j-1]))
					die("Invalid level '" . $mhierarchy[$j-1] . "' in your layout.");
			}
		}
		if ($mhierarchy[sizeof($mhierarchy)-1] != "track") {
			die("Your hierarchy must end with 'track'. Please try again.");
		}
		$_POST['hierarchy'] = implode("/",$mhierarchy);
	?>
	<h1><?php echo $word_backend_setup; ?></h1>
	<p>
	<?php echo $word_backend_setup_note; ?>
	
	<div class="go">
		<span class="goToNext">
			<?php echo $word_backend_setup; ?>
		</span>
	</div>
	<br>
	<form action="<?php echo $form_action; ?>" name="setup6" method="post">
		<?php
			$PostArray = $_POST;
			foreach ($PostArray as $key => $val) {
			  if (!stristr($key,"submit")) {
			  	echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
			  }
		   }
		?>
		<table width="100%" cellspacing="0" cellpadding="3" border="0">
			<tr>
				<td class="td" width="100%" align="left">
					<?php
						// we have $backend set
						// and we have $hierarchy set.
						$be = &new jzBackend();
						$retval = $be->install();
					?>
				</td>
			</tr>
		</table>
		<br>
		<div class="go">
			<span class="goToNext">
				<?php if ($retval > 0) { 
					if (isset($_POST['default_cms_access'])) {
						$_POST['default_access'] = $_POST['default_cms_access'];
 					}
					// install completed. Add our users to the table.
					$jzUSER = new jzUser(false);
					$ausr = stripSlashes($_POST['admin_user']);
					$apass = stripSlashes($_POST['admin_pass']);
					if (($id = $jzUSER->lookupUID('NOBODY')) !== false) {
						$settings = array();
						$settings['edit_prefs'] = "false";
						switch ($_POST['default_access']) {
						case "noaccess":
							$settings['ratingweight'] = 0;
							$settings['stream'] = "false";
							$settings['view'] = "false";
							$settings['lofi'] = "false";
							$settings['download'] = "false";
							break;
						case "viewonly":
						  $settings['ratingweight'] = 0;
						  $settings['stream'] = "false";
						  $settings['view'] = "true";
						  $settings['lofi'] = "false";
						  $settings['download'] = "false";
						  break;
						case "lofi":
							$settings['stream'] = "false";
							$settings['view'] = "true";
							$settings['lofi'] = "true";
							$settings['download'] = "true";
							$settings['discuss'] = "false";
							break;
						case "user":
							$settings['stream'] = "true";
							$settings['view'] = "true";
							$settings['lofi'] = "true";
							$settings['download'] = "true";
							$settings['discuss'] = "true";
							break;
							
						case "admin":
							$settings['ratingweight'] = 1;
							$settings['stream'] = "true";
							$settings['view'] = "true";
							$settings['lofi'] = "true";
							$settings['download'] = "true";
							$settings['discuss'] = "true";
							$settings['admin'] = "true";
							$settings['jukebox_admin'] = "true";
							$settings['jukebox_queue'] = "true";						
							break;
						default:
							die("invalid default access.");
							break;
						}
						$jzUSER->setSettings($settings,$id);	
					}
					if (($id = $jzUSER->addUser($ausr,$apass)) !== false) {
						// set admin properties.
						$settings = array();
						$settings['ratingweight'] = 1;
						$settings['stream'] = "true";
						$settings['view'] = "true";
						$settings['lofi'] = "true";
						$settings['download'] = "true";
						$settings['discuss'] = "true";
						$settings['admin'] = "true";
						$settings['jukebox_admin'] = "true";
						$settings['jukebox_queue'] = "true";						

						$jzUSER->setSettings($settings,$id);
						$jzUSER->id = $id;
						$jzUSER->name = $ausr;
											
						$jzUSER->login($ausr,$apass);
					}
					else if (($id = $jzUSER->lookupUID($ausr)) === false) {
						$retval = -1;
						echo "There was a problem setting up your user account.";
					}
					else {
						$jzUSER->login($ausr,$apass);
					}
					
				
				?>
				&nbsp; <input type="submit" name="submit_step6_done" class="submit" value="<?php echo $word_proceed_import_media; ?>">
				<?php } else if ($retval == 0) { ?>
				&nbsp; <input type="submit" name="submit_step6_more" class="submit" value="<?php echo $word_database_continue. " >>" ?>">
				<?php } else { /*error*/ ?>
				&nbsp;
				<?php } ?>
			</span>
		</div>
	</form>
	</div>
<?php
	// Now let's include the top
	include_once($include_path. 'install/footer.php');
?>
