<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
global $resampleRates, $frontend, $jinzora_skin, $include_path, $jzUSER, $jzSERVICES, $cms_mode;

$display = new jzDisplay();
$be = new jzBackend();
// First let's display the top of the page and open the main block
$this->displayPageTop("", word("User Manager"));
$this->openBlock();

if ($jzUSER->getSetting('admin') === false) {
	echo word("Insufficient permissions");
	$this->closeBlock();
	return;
}
// Make a menu button for later:
$urla = array ();
$urla['action'] = "popup";
$urla['ptype'] = "usermanager";

$MENU_BUTTON = '<form method="POST" action="' . urlize($urla) . '">';
$MENU_BUTTON .= '<input type="submit" class="jz_submit" name="menu" value="' . word('Menu') . '">';
$MENU_BUTTON .= '</form>';

// Different features:
if (!isset ($_GET['subaction'])) {
	$ucount = sizeof($jzUSER->listUsers());
	echo '<table>';

	echo '<tr><td>';
	$urla['subaction'] = "adduser";
	echo '<a href="' . urlize($urla) . '">' . word("Add a user") . '</a>';
	echo "</td></tr>";

	if ($ucount > 2) {
		echo '<tr><td>';
		$urla['subaction'] = "edituser";
		echo '<a href="' . urlize($urla) . '">' . word("Modify a user") . '</a>';
		echo "</td></tr>";
	}

	if ($ucount > 2) {
		echo '<tr><td>';
		$urla['subaction'] = "removeuser";
		echo '<a href="' . urlize($urla) . '">' . word("Remove a user") . '</a>';
		echo "</td></tr>";
	}

	echo '<tr><td>';
	$urla['subaction'] = "editclasses";
	echo '<a href="' . urlize($urla) . '">' . word("Modify user templates") . '</a>';
	echo "</td></tr>";

	echo '<tr><td>';
	$urla['subaction'] = "default_access";
	echo '<a href="' . urlize($urla) . '">' . word("Edit default access") . '</a>';
	echo "</td></tr>";

	echo '<tr><td>';
	$urla['subaction'] = "registration";
	echo '<a href="' . urlize($urla) . '">' . word("Edit self-registration") . '</a>';
	echo "</td></tr>";

	echo '</table>';
	$this->closeBlock();
	return;
}

// HANDLE USER CHANGES:
if ($_GET['subaction'] == "handleuser") {
	// Now, did they submit the form?
	if ($_POST['field1'] != $_POST['field2']) {
		echo word("Error: Password mismatch");
		return;
	}
}
if ($_GET['subaction'] == "handleuser" && ($_POST['templatetype'] == "customize") && !isset ($_POST['usr_interface'])) {
	if ($_GET['usermethod'] == "add") {
		$myid = $jzUSER->addUser($_POST['username'], $_POST['field1']);
		if ($myid === false) {
			echo word("Could not add user") . " " . $_POST['username'] . ".";
			return;
		}
	} else {
		$myid = $_POST['user_to_edit'];
		if ($_POST['field1'] != "jznoupd") {
			// update password
			$jzUSER->changePassword($_POST['field1'], $jzUSER->lookupName($myid));
		}
		// change name
		if (($oldname = $jzUSER->lookupName($myid)) != $_POST['username']) {
			$jzUSER->changeName($_POST['username'], $oldname);
		}
	}
	if ($_POST['userclass'] == "jznewtemplate") {
		$settings = array ();
		if (isset ($_POST['user_to_edit'])) {
			$settings = $jzUSER->loadSettings($_POST['user_to_edit']);
		} else {
			$settings = array ();
		}
	} else {
		$classes = $be->loadData('userclasses');
		$settings = $classes[$_POST['userclass']];
	}
	$post = $_POST;
	unset ($post['handleUser']);
	$this->userManSettings("custom", $settings, 'handleuser', $post);
	return;
}

if ($_GET['subaction'] == "handleuser") {
	if ($_GET['usermethod'] == "add") {
		$myid = $jzUSER->addUser($_POST['username'], $_POST['field1']);
		if ($myid === false) {
			echo word("Could not add user") . " " . $_POST['username'] . ".";
			return;
		}
	} else {
		$list = $jzUSER->listUsers();
		foreach ($list as $id => $name) {
			if ($name == $_POST['username']) {
				$myid = $id;
			}
		}
		if ($_POST['field1'] != "jznoupd") {
			// update password
			$jzUSER->changePassword($_POST['field1'], $jzUSER->lookupName($myid));
		}
		// change name
		if (($oldname = $jzUSER->lookupName($myid)) != $_POST['username']) {
			$jzUSER->changeName($_POST['username'], $oldname);
		}
	}
	// DETACH
	if ($_POST['templatetype'] == "detach") {
		if ($_POST['userclass'] == "jznewtemplate") {
			echo "Cannot base a user only on a blank template.";
			return;
		} else {
			$classes = $be->loadData('userclasses');
			$settings = $classes[$_POST['userclass']];
			$settings['template'] = "";
		}
		// STICKY
	} else
		if ($_POST['templatetype'] == "sticky") {
			if ($_POST['userclass'] == "jznewtemplate") {
				echo "Cannot stick user to a blank template.";
				return;
			} else {
				$settings = array ();
				$settings['template'] = $_POST['userclass'];
			}
			// CUSTOMIZE
		} else
			if ($_POST['templatetype'] == "customize") {
				$settings = $this->userPullSettings();
				$settings['template'] = "";
			} else {
				echo "Sorry, I don't know how to manage the user.";
				return;
			}

	$jzUSER->setSettings($settings, $myid);
	$un = ($_POST['username'] != "") ? $_POST['username'] : word('anonymous');

	$settings['home_dir'] = str_replace('USERNAME', $un, $settings['home_dir']);

	echo word("User") . ": " . $un . " " . word("updated");
	echo "<br><br><center>";
	echo $MENU_BUTTON . '&nbsp;';
	$this->closeButton();
	$this->closeBlock();
	return;

} else
	if ($_GET['subaction'] == "handleclass") {
		$settings = $this->userPullSettings();
		$classes = $be->loadData("userclasses");
		if (!is_array($classes)) {
			$classes = array ();
		}
		$classes[$_POST['classname']] = $settings;
		$be->storeData('userclasses', $classes);

		echo word("Template updated.");
		echo "<br><br><center>";
		echo $MENU_BUTTON . '&nbsp;';
		$this->closeButton();
		$this->closeBlock();
		return;
	}
/** ** ** ** **/
// USER CLASS MANAGER
//

if ($_GET['subaction'] == "editclasses") {
	$urla = array ();
	$urla['action'] = "popup";
	$urla['ptype'] = "usermanager";
	$urla['subaction'] = "editclasses";

	if (!isset ($_GET['subsubaction'])) {
		echo '<table>';
		echo '<tr><td>';
		$urla['subsubaction'] = "add";
		echo '<a href="' . urlize($urla) . '">' . word("Add a template") . '</a>';
		echo "</td></tr>";

		$classes = $be->loadData('userclasses');
		if (!(!is_array($classes) || sizeof($classes) == 0)) {
			echo '<tr><td>';
			$urla['subsubaction'] = "edit";
			echo '<a href="' . urlize($urla) . '">' . word("Edit a template") . '</a>';
			echo "</td></tr>";

			echo '<tr><td>';
			$urla['subsubaction'] = "remove";
			echo '<a href="' . urlize($urla) . '">' . word("Remove a template") . '</a>';
			echo "</td></tr>";
		}
		echo '</table>';

	} else
		if ($_GET['subsubaction'] == "edit") {
			$urla['subsubaction'] = "edit2";
			echo '<table>';
			echo '<form method="POST" action="' . urlize($urla) . '">';
?><input type="hidden" name="update_settings" value="true"><?php

			echo '<tr><td>' . word("Template:");
			echo '</td><td>';
			echo '<select name="classname" class="jz_select">';
			$classes = $be->loadData('userclasses');
			$keys = array_keys($classes);
			foreach ($keys as $key) {
				echo '<option value="' . $key . '">' . $key;
			}
			echo '</select>';
			echo '</td></tr>';
			echo '<tr colspan="2"><td>';
			echo '<input type="submit" class="jz_submit" name="submit" value="' . word('Go') . '">';
			echo '</td></tr></form></table>';

		} else
			if ($_GET['subsubaction'] == "add" || $_GET['subsubaction'] == "edit2") {
				if ($_GET['subsubaction'] == "add") {
					$settings = array ();
					$settings['view'] = true;
					$settings['stream'] = true;
					$settings['powersearch'] = true;
					$settings['edit_prefs'] = true;
					$settings['frontend'] = $frontend;
					$settings['theme'] = $jinzora_skin;
					$settings['language'] = "english";
					$settings['playlist_type'] = "m3u";
					$this->userManSettings("new", $settings);
				} else {
					$classes = $be->loadData('userclasses');
					if (!isset ($classes[$_POST['classname']])) {
						die("Invalid user template.");
					}
					$settings = $classes[$_POST['classname']];
					$this->userManSettings("update", $settings);
				}

			} else
				if ($_GET['subsubaction'] == "remove") {
					if (!isset ($_POST['class_to_remove'])) {
						$list = $jzUSER->listUsers();
						$url_array = array ();
						$url_array['action'] = "popup";
						$url_array['ptype'] = "usermanager";
						$url_array['subaction'] = "editclasses";
						$url_array['subsubaction'] = "remove";

						echo '<form action="' . urlize($url_array) . '" method="POST">';
						echo '<input type="hidden" name="update_settings" value="true">';
						echo word("Template:") . '&nbsp';
						echo '<select name="class_to_remove" class="jz_input">';
						$classes = $be->loadData('userclasses');
						$keys = array_keys($classes);
						foreach ($keys as $key) {
							if ($key != NOBODY) {
								echo '<option value="' . $key . '">' . $key . '</option>';
							}
						}
						echo "</select>";
						echo '&nbsp;<input type="submit" class="jz_submit" value="Go">';
						echo '</form>';
					} else {
						$classes = $be->loadData('userclasses');
						unset ($classes[$_POST['class_to_remove']]);
						$be->storeData('userclasses', $classes);
						echo $_POST['class_to_remove'] . word(" has been removed.");
						echo '<br><br><center>';
						echo $MENU_BUTTON;
						echo '&nbsp;';
						$this->closeButton();
					}
					$this->closeBlock();
					return;
				}

	$this->closeBlock();
	return;
}

// SELF-REGISTRATION:
if ($_GET['subaction'] == "registration") {
	$be = new jzBackend();
	$data = $be->loadData('registration');
	if (!is_array($data)) {
		$data = array ();
	}
	$classes = $be->loadData('userclasses');

	if (!is_array($classes)) {
		$urla = array ();
		$urla['action'] = "popup";
		$urla['ptype'] = "usermanager";
		$urla['subaction'] = "editclasses";
		$urla['subsubaction'] = "add";
		echo word("<p>You must set up a user template before enabling user registration.</p>");
		echo '<p><a href="' . urlize($urla) . '">' . word("Click here to do add a user template.") . '</a></p>';

		return;
	}

	if (isset ($_POST['update_postsettings'])) {
		echo word("Settings updated") . "<br>";
	}
	$page_array = array ();
	$page_array['action'] = "popup";
	$page_array['ptype'] = "usermanager";
	$page_array['subaction'] = "registration";
	$display->openSettingsTable(urlize($page_array));
	$display->settingsCheckbox(word("Allow Self-Registration") . ":", 'allow_registration', $data);

	$keys = array_keys($classes);
	$display->settingsDropdown(word("User Template:"), 'classname', $keys, $data);

	$display->closeSettingsTable(true);
	if (isset ($_POST['update_postsettings'])) {
		$be->storeData('registration', $data);
	}
	return;
}

// * * * * * * * * //
// ANONYMOUS USER SUBSECTION
// * * * * * * * * //
if ($_GET['subaction'] == "default_access") {
	$_GET['subaction'] = "edituser";
	$_POST['user_to_edit'] = $jzUSER->lookupUID(NOBODY);
}

// * * * * * * * * //
// EDIT USER SUBSECTION
// * * * * * * * * //
if ($_GET['subaction'] == "edituser") {
	if (!isset ($_POST['user_to_edit'])) {
		$list = $jzUSER->listUsers();
		$my_id = $jzUSER->getID();
		$url_array = array ();
		$url_array['action'] = "popup";
		$url_array['ptype'] = "usermanager";
		$url_array['subaction'] = "edituser";
		echo '<form action="' . urlize($url_array) . '" method="POST">';
		echo '<input type="hidden" name="update_settings" value="true">';
		echo word("User") . ": ";
		echo '<select name="user_to_edit" class="jz_input">';
		foreach ($list as $id => $name) {
			if ($name != NOBODY && $id != $my_id) {
				echo '<option value="' . $id . '">' . $name . '</option>';
			}
		}
		echo "</select>";
		echo '&nbsp;<input type="submit" class="jz_submit" value="Go">';
		echo '</form>';
		return;
	}
}

if ($_GET['subaction'] == "removeuser") {
	if (!isset ($_POST['user_to_remove'])) {
		$list = $jzUSER->listUsers();
		$url_array = array ();
		$url_array['action'] = "popup";
		$url_array['ptype'] = "usermanager";
		$url_array['subaction'] = "removeuser";
		echo '<form action="' . urlize($url_array) . '" method="POST">';
		echo '<input type="hidden" name="update_settings" value="true">';
		echo word("User") . ": ";
		echo '<select name="user_to_remove" class="jz_input">';
		foreach ($list as $id => $name) {
			if ($name != NOBODY) {
				echo '<option value="' . $id . '">' . $name . '</option>';
			}
		}
		echo "</select>";
		echo '&nbsp;<input type="submit" class="jz_submit" value="Go">';
		echo '</form>';
	} else {
		$name = $jzUSER->lookupName($_POST['user_to_remove']);
		$jzUSER->removeUser($_POST['user_to_remove']);
		echo $name . word(" has been removed.");
		echo '<br><br><center>';
		echo $MENU_BUTTON . '&nbsp;';
		$this->closeButton();
	}
	$this->closeBlock();
	return;
}

// * * * * * * * * * * * //
// ADD A USER SUBSECTION
// * * * * * * * * * * * //
// Let's show the form for this
$url_array = array ();
$url_array['action'] = "popup";
$url_array['ptype'] = "usermanager";
$url_array['subaction'] = "handleuser";

if (!isset ($_POST['user_to_edit'])) {
	$url_array['usermethod'] = "add";
	$edit_guest = false;
	$mid = $jzUSER->lookupUID(NOBODY);
} else {
	$url_array['usermethod'] = "update";
	if ($_POST['user_to_edit'] == $jzUSER->lookupUID(NOBODY)) {
		$edit_guest = true;
		$mid = $_POST['user_to_edit'];
	} else {
		$edit_guest = false;
		$mid = $_POST['user_to_edit'];
	}
}

$jzUSER2 = new jzUser(false, $mid);

if ($_GET['subaction'] == "adduser") {
	// set some settings manually.
	$jzUSER2->settings['view'] = true;
	$jzUSER2->settings['stream'] = true;
	$jzUSER2->settings['lofi'] = true;
}
echo '<form action="' . urlize($url_array) . '" method="POST">';

if (isset ($_POST['user_to_edit'])) {
	echo '<input type="hidden" name="user_to_edit" value="' . $_POST['user_to_edit'] . '">';
}
?>
		      <input type="hidden" name="update_settings" value="true">
			 <table width="100%" cellpadding="3">
			 <?php if ($edit_guest === false) { ?>
			 <tr>
			 <td width="30%" align="right">
			 Username:
		    </td>
			 <td width="70%">
			<?php

if ($_GET['subaction'] == "adduser") {
	// Now let's return our tooltip													
?>
				<input type="input" name="username" class="jz_input">
				<?php

} else {
?>
				<input type="input" name="username" class="jz_input" value="<?php echo $jzUSER2->getName(); ?>">
				<?php

}
?>
			 </td>
			 </tr><?php if ($cms_mode == "false") { ?>
			 <tr>
			 <td width="30%" valign="top" align="right">
			 <?php echo word("Password"); ?>:
		    </td>
			 <td width="70%">
			<?php

if ($_GET['subaction'] == "adduser") {
?>
				 <input type="password" name="field1" class="jz_input"><br>
				 <input type="password" name="field2" class="jz_input">
				 <?php

} else {
?>
				 <input type="password" name="field1" class="jz_input" value="jznoupd"><br>
				 <input type="password" name="field2" class="jz_input" value="jznoupd">
				 <?php

}
?>
			 </td>
			 </tr><?php } else { ?> <input type="hidden" name="field1" value="jznoupd"> <?php } ?>
			 <tr>
			 <td width="30%" valign="top" align="right">
			 <?php echo word("Full Name"); ?>:
			</td>
			 <td width="70%">
			 <input type="input" name="fullname" class="jz_input" value="<?php echo $jzUSER2->getSetting('fullname'); ?>">
			 </td>
			 </tr>
			 <tr>
			 <td width="30%" valign="top" align="right">
			 <?php echo word("Email Address"); ?>:
			</td>
			 <td width="70%">
			 <input type="input" name="email" class="jz_input" value="<?php echo $jzUSER2->getSetting('email'); ?>">
			 </td>
			 </tr>
			 <?php

}
?>


			 <tr>
			    <td width-"30%" valign="top" align="right">
			    <?php echo word("Template:"); ?>
			    </td>
			    <td width="70%">
						<?php

echo '<select name="userclass" class="jz_select">';
echo "<option value=\"jznewtemplate\">" . word('Blank Template');
$classes = $be->loadData('userclasses');
if (is_array($classes)) {
	$keys = array_keys($classes);
	$set = $jzUSER2->loadSettings();
	if (isset ($set['template'])) {
		$t = $set['template'];
	} else {
		$t = $keys[0];
	}
	foreach ($keys as $key) {
		echo "<option value=\"$key\"";
		if ($key == $t) {
			echo ' SELECTED';
		}
		echo ">$key";
	}
}
?>
		      </select>
			  </td></tr>
			  <tr><td width="30%" valign="top" align="right"><?php echo word('Management:'); ?></td><td>
		          <input type="radio" name="templatetype" value="sticky"><?php echo word('Update user when template is updated'); ?>
			  </td></tr>
			  <tr><td></td><td>
		          <!--<input type="radio" name="templatetype" value="detach"><?php echo word('Detach user from template'); ?>
			  </td></tr>
			  <tr><td></td><td>-->
			  <input type="radio" name="templatetype" value="customize" checked><?php echo word("Customize this user's settings"); ?>
			  </td></tr>
			  <tr>
			  <td width="30%" valign="top">
			  </td>
			  <td width="70%">
			  <input type="submit" name="handleUser" value="<?php echo word("Go"); ?>" class="jz_submit">
			  </td>
			  </tr>


			</table>
			<?php

echo '</form>';
$this->closeBlock();
?>
