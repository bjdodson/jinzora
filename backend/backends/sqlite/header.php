<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	/**
	* - JINZORA | Web-based Media Streamer -  
	* 
	* Jinzora is a Web-based media streamer, primarily desgined to stream MP3s 
	* (but can be used for any media file that can stream from HTTP). 
	* Jinzora can be integrated into a CMS site, run as a standalone application, 
	* or integrated into any PHP website.  It is released under the GNU GPL.
	* 
	* - Resources -
	* - Jinzora Author: Ross Carlson <ross@jasbone.com>
	* - Web: http://www.jinzora.org
	* - Documentation: http://www.jinzora.org/docs	
	* - Support: http://www.jinzora.org/forum
	* - Downloads: http://www.jinzora.org/downloads
	* - License: GNU GPL <http://www.gnu.org/copyleft/gpl.html>
	* 
	* - Contributors -
	* Please see http://www.jinzora.org/modules.php?op=modload&name=jz_whois&file=index
	* 
	* - Code Purpose -
	* This is the header for the default XML cache adaptor.
	*
	* @since 05.10.04
	* @author Ross Carlson <ross@jinzora.org>
	*/

	@include_once($include_path. 'data/backend/db_constants.php');
	include_once($include_path. 'backend/backends/sqlite/abstraction.php');
		
	
	
	// This builds all our classes.	
	include_once($include_path. 'backend/primitives/database/header.php');
	include_once($include_path. 'backend/backends/db-common.php');
	
	
  	class jzBackend extends jzRawBackend { 
		function jzBackend() {
			// Set variables from parent class.
			$this->_constructor();
			// Overrrides here
			$this->details = "Database-driven backend.";
		}
		
		
		function install() {
		global $backend, $version, $include_path, $jzSERVICES, $sql_type, $sql_socket, $sql_db, $sql_usr, $sql_pw;

		// store version, details, and name (including SQL type)
		// in a database.
		// run the webpage found in install.php (move that code here.)

		$step = isset ($_POST["dbstep"]) ? $_POST["dbstep"] : "init";

		switch ($step) {
			case "init" :
				global $word_database_user, $word_database_pass, $word_database_name, $word_database_server, $word_database_type, $word_database_user_help, $word_database_pass_help, $word_database_name_help, $word_database_server_help, $word_database_type_help, $cms_type, $cms_mode, $word_cms_db_pick, $word_confirm_password, $word_create_database, $word_false, $word_true, $word_create_database_help, $word_drop, $word_drop_database_help;
?>
				    <input name="dbstep" value="db" type=hidden>
					<table width="100%" cellspacing="0" cellpadding="3" border="0">
						<tr>
							<td class="td" width="30%" align="left" valign="top">
								<?php echo $word_database_name; ?>
							</td>
							<td width="1">&nbsp;</td>
							<td width="70%" align="left" class="td">
								<?php

				// They might not be installing under the CMS they intend on using...
				$jzSERVICES->loadService("cms", $_POST['cms_type']);
				$value = $jzSERVICES->cmsDefaultDatabase();

				$jzSERVICES->loadService("cms", $cms_type);
?>
								<input name="database" value="<?php echo $value; ?>" type="text" onmouseover="return overlib('<?php echo $word_database_name_help; ?>', FGCOLOR, '#eeeeeee');" onmouseout="return nd();">
								<!--&nbsp;&nbsp; <a class="helpbox2" href="javascript:void(0);" onmouseover="return overlib('<?php echo $word_database_name_help; ?>');" onmouseout="return nd();">?</a>-->
								<br>
								<?php

				// If this is mambo we MUST use their db
				if ($cms_type <> "standalone" and $cms_type <> "") {
					echo $word_cms_db_pick;
				}
?>
							</td>
						</tr>
						<?php

				// Only show this in standalone mode
				if ($cms_type == "" || $cms_type == "standalone") {
?>
								<tr>
									<td class="td" width="30%" align="left" valign="top">
										<?php echo $word_create_database; ?>
									</td>
									<td width="1">&nbsp;</td>
									<td width="70%" align="left">
										<select name="create_db" style="width:115px" onmouseover="return overlib('<?php echo $word_create_database_help; ?>', FGCOLOR, '#eeeeeee');" onmouseout="return nd();">
											<option value="true"><?php echo $word_true; ?>
											<option value="false" selected><?php echo $word_false; ?>
											<option value="drop"><?php echo $word_drop; ?>
										</select>
										<!--&nbsp; <a class="helpbox2" href="javascript:void(0);" onmouseover="return overlib('<?php echo $word_drop_database_help; ?>');" onmouseout="return nd();">?</a>-->
									</td>
								</tr>
								<?php

				} else {
					echo '<input type="hidden" name="create_db" value="true">';
				}
?>
					</table>
				  <?php

				return 0;
				break;

			case "db" :
				// let's set the words we'll need
				global $word_creating_database, $word_creating_tables, $word_database_created, $word_successful, $word_dropping_database, $word_failed, $word_exists;

				// first write the constants file.
				$text = "";
				$text .= "<?php\n";
				$text .= "// settings for " . $backend . ":\n";
				$text .= "\$sql_db     = \"" . $_POST['database'] . "\";\n";
				$text .= "?>";

				$fname = $this->data_dir . '/db_constants.php';
				@ unlink($fname);

				if (!$handle = @ fopen($fname, 'w')) {
					touch($this->data_dir . '/db_constants.php');
				}
				if (!fopen($fname, 'w')) {
					echo "could not write $this->data_dir /db_constants.php";
					return -1;
				}

				fwrite($handle, $text);
				fclose($handle);

				// Now to use it.
				require_once ($fname);

				// Now should we drop first?
				if ($_POST['create_db'] == "drop") {
					jz_db_drop();
					echo $word_dropping_database . "<br>";
					echo "&nbsp;&nbsp;&nbsp; - " . $sql_db . ' - <font color="green">' . $word_successful . '</font><br><br>';
					// Now let's clean the cache
					@ unlink($include_path . "data/backend/backend");
				}

				if ($_POST['create_db'] == "drop" or $_POST['create_db'] == "true") {
					echo $word_creating_database . "<br>";
					if (jz_db_create()) {
						echo "&nbsp;&nbsp;&nbsp; - " . $sql_db . ' - <font color="green">' . $word_successful . '</font><br><br>';
					} else {
						@ $link = jz_db_connect();
						$results = @ jz_db_query($link, "show tables from " . $sql_db);
						if ($results->data[0][0] <> "") {
							echo "&nbsp;&nbsp;&nbsp; - " . $sql_db . ' - <font color="green">' . $word_exists . '</font><br><br>';
						} else {
							echo "&nbsp;&nbsp;&nbsp; - " . $sql_db . ' - <font color="red">' . $word_failed . '</font><br><br>';
						}
					}
				}

				if (!$link = jz_db_connect()) {
					die("could not connect to database.");
					return -1;
				}

				if ($_POST['create_db'] == "drop" or $_POST['create_db'] == "true" or $_POST['create_db'] == "false") {
					echo $word_creating_tables . "<br>";

					// Now let's see if we need to create each table if it does NOT exist
					$results = jz_db_query($link, "show tables from " . $sql_db);
						if ($results !== false && is_array($results) && $results != array ()) {
						foreach ($results as $data) {
							if (isset ($data[0])) {
								foreach ($data as $a) {
									$$a[0] = true;
								}
							}
						}
					}

					// Now let's open the sql script
					global $include_path, $sql_type;

					$data = file_get_contents($include_path . "install/jinzora.sql");

					// Now let's break it out per table
					$sqlArr = explode(";", $data);
					foreach ($sqlArr as $sql) {
						if (stristr($sql, "CREATE TABLE")) {
							$table = substr($sql, strpos($sql, "-- Table structure for table") + strlen("-- Table structure for table") + 2);
							$table = substr($table, 0, strpos($table, "`"));

							$res = jz_db_query($link, $sql);
							if ($res !== false) {
								echo '&nbsp;&nbsp;&nbsp; - Creating table: ' . $table . ' - <font color="green">' . $word_successful . '</font><br>';
							} else {
								if ($jz_nodes) {
									echo '&nbsp;&nbsp;&nbsp; - Creating table: ' . $table . ' - <font color="green">' . $word_exists . '</font><br>';
								} else {
									echo '&nbsp;&nbsp;&nbsp; - Creating table: ' . $table . ' - <font color="red">' . $word_failed . '</font><br>';
								}
							}
						}
					}

					echo "<br>" . $word_database_created;
				}
				echo '<input name="dbstep" value="pop" type=hidden>' . "\n";

				// Now did they want to manually create it?
				/*
				if ($_POST['create_db'] == "false"){
					// Now let's see if we need to create each table if it does NOT exist
					$results =  jz_db_query($link, "show tables from ". $sql_db);
					if ($results !== false) {
					  foreach ($results as $data) {
						if (count($data) == 5){
							echo "Database connection successful!";
							break;
						} else {
							?>
							Sorry, there was an error reading your database.  Please verify that you've created the database manually and click below to try again
							<?php
							exit();
						}
					  }
					}
				}
				*/
				// The above form needs work to integrate with the installer.
				// think about a keep_post_vars() function that keeps the values in hidden fields.
				// add our root node to the database.
				jz_db_query($link, "INSERT INTO jz_nodes(path,name,level,my_id) VALUES('','',0,'" . uniqid("T") . "')");

				// add backend details:
				parent :: install_be();
				parent :: install_users();

				return 1;
				break;
		}
	}
		
		
	}
 
 
 

	
?>