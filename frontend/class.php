<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	/*
	* - JINZORA | Web-based Media Streamer -  
	* 
	* Jinzora is a Web-based media streamer, primarily desgined to stream MP3s 
	* (but can be used for any media file that can stream from HTTP). 
	* Jinzora can be integrated into a CMS site, run as a standalone application, 
	* or integrated into any PHP website.  It is released under the GNU GPL.
	* 
	* - Ressources -
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
	* - Primary start page for Jinzora
	*
	* @since 11/3/04
	* @author Ross Carlson <ross@jinzora.org>, Ben Dodson <bdodson@seas.upenn.edu>
	*/
	
	// define the default frontend class.
	class jzFrontendClass {
	
		var $name;
		var $width;
		var $standardFooter;

		/**
		* Constructor wrapper for jzFrontend.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 11/3/04
		* @since 11/3/04
		*/
		function jzFrontendClass() {
			$this->_constructor();
		}
		
		/**
		* Constructor wrapper for jzMediaNode.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 11/3/04
		* @since 11/3/04
		*/
		function _constructor() {
			global $my_frontend,$include_path;

			$this->name = $my_frontend;
			$this->width = "100%";
			$this->align = "left";
			$this->ajax_list = array();
			$this->standardFooter = true;
		}
		
		/**
		* Draws the registration page
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 3/11/05
		* @since 3/11/05
		*/
		function registrationPage() {
		  $display = &new jzDisplay();
		  $be = new jzBackend();
		  $display->preHeader('Register',$this->width,$this->align);
		  $urla = array();

		  if (isset($_POST['field5'])) {
		    $user = new jzUser(false);
		    if (strlen($_POST['field1']) == 0 ||
					strlen($_POST['field2']) == 0 ||
					strlen($_POST['field3']) == 0 ||
					strlen($_POST['field4']) == 0 ||
					strlen($_POST['field5']) == 0) {
		      echo "All fields are required.<br>";
		    }
		    else if ($_POST['field2'] != $_POST['field3']) {
		      echo "The passwords do not match.<br>";
		    }
		    else if (($id = $user->addUser($_POST['field1'],$_POST['field2'])) === false) {
		      echo "Sorry, this username already exists.<br>";
		    } else {
		      // success!
		      $stuff = $be->loadData('registration');
		      $classes = $be->loadData('userclasses');
		      $settings = $classes[$stuff['classname']];

		      $settings['fullname'] = $_POST['field4'];
		      $settings['email'] = $_POST['field5'];
		      $un = $_POST['field1'];
		      $settings['home_dir'] = str_replace('USERNAME',$un,$settings['home_dir']);
		      $user->setSettings($settings,$id);

		      echo "Your account has been created. Click <a href=\"" . urlize($urla);
		      echo "\">here</a> to login.";
		      $this->footer();
		      return;
		    }
		  }

		    ?>
			<form method="POST" action="<?php echo urlize($urla); ?>">
			<input type="hidden" name="<?php echo jz_encode('action'); ?>" value="<?php echo jz_encode('login'); ?>">
			<input type="hidden" name="<?php echo jz_encode('self_register'); ?>" value="<?php echo jz_encode('true'); ?>">
			<table width="100%" cellpadding="5" style="padding:5px;" cellspacing="0" border="0">
			<tr>
			<td width="50%" align="right"><font size="2">
			<?php echo word("Username"); ?>
			</font></td><td width="50%">
			<input type="text" class="jz_input" name="field1" value="<?php echo $_POST['field1']; ?>">
			</td></tr>
			<tr><td width="50%" align="right"><font size="2">
			<?php echo word("Password"); ?>
			</font></td>
			<td width="50%">
			<input type="password" class="jz_input" name="field2" value="<?php echo $_POST['field2']; ?>"></td></tr>
			    <tr><td width="50%" align="right"><font size="2">
			    &nbsp;
			</td>
			<td width="50%">
			<input type="password" class="jz_input" name="field3" value="<?php echo $_POST['field3']; ?>"></td></tr>
			<tr><td width="50%" align="right"><font size="2">
			<?php echo word("Full name"); ?>
			</font></td><td width="50%">
			<input type="text" class="jz_input" name="field4" value="<?php echo $_POST['field4']; ?>">
			</td></tr>
			<tr><td width="50%" align="right"><font size="2">
			<?php echo word("Email"); ?>
			</font></td><td width="50%">
			<input type="text" class="jz_input" name="field5" value="<?php echo $_POST['field5']; ?>">
			</td></tr>
			<tr><td width="100%" colspan="2" align="center">
			<input class="jz_submit" type="submit" name="<?php echo jz_encode('submit_login'); ?>" value="<?php echo word("Register"); ?>">
			</td></tr></table><br>
			</form>
			<?php
		    $this->footer();
	        }

		/**
		* Draws the login page.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 11/3/04
		* @since 5/13/04
		*/
		function loginPage($failed = false) {
		
			$display = &new jzDisplay();
			//$display->preHeader('Login',$this->width,$this->align);
			
			echo '<body onLoad="document.getElementById(\'loginform\').field1.focus();"></body>';
			
			$urla = array();			
			$urla['jz_path'] = isset($_GET['jz_path']) ? $_GET['jz_path'] : '';
			?>
				<style>
					body {
						background: #000000;
						margin: 1 1 1 1
						font-family: Verdana, Sans;
						font-size: 10px;
						color: #9c9b9b;
					}
					td {
						font-family: Verdana, Sans;
						font-size: 10px;
					}
					submit {
						border: 1px solid black;
						background: #1d1d1d;
						color: #9c9b9b;
						font-size: 11px;
						border-width: 1px;
					}
					input {
						font-family: Verdana, Sans;
						color: #9c9b9b;
						background-color: #1d1d1d;
						font-size: 11px;
						border-width: 1px;
					}
					checkbox {
						font-family: Verdana, Sans;
						color: #9c9b9b;
						background-color: #1d1d1d;
						font-size: 11px;
						border-width: 1px;
					}
				</style>
				    <script language="javascript" src="lib/md5.js"></script>
				    <script language="javascript">
				    function submitLogin() {
				      if (document.getElementById("loginform").doregister.value == 'true') {
					return true;
				      } else {
					// submit the other form
					// so we can submit a non-cleartext PW without changing browser's stored PW.
					document.getElementById("loginSecureForm").field1.value = 
					         document.getElementById("loginform").field1.value;

					document.getElementById("loginSecureForm").field2.value = 
					hex_md5(document.getElementById("loginform").field2.value);

					document.getElementById("loginSecureForm").remember.value =
					document.getElementById("loginform").remember.value;

					document.getElementById("loginSecureForm").submit();
					return false;
				      }
				    }
				    </script>
				<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td background="style/images/login-background.gif" height="363" width="49%" style="border-bottom:1px solid #474747; border-left:1px solid #474747; border-top:1px solid #474747;">&nbsp;</td>
						<td height="363" align="center" width="146" style="border-bottom:1px solid #474747; border-top:1px solid #474747; background: url('style/images/login-logo.gif');">
							<?php
								if ($failed) {
									echo "<center><strong><font color=white>Incorrect password</font></strong></center>";
								}
							?>
																										    <form name="loginSecureForm" id="loginSecureForm" method="POST" action="<?php echo urlize($urla); ?>">
							<input type="hidden" name="field1" value="">
							<input type="hidden" name="field2" value="">
                                                        <input type="hidden" name="remember" value="">
							<input type="hidden" name="<?php echo jz_encode('action'); ?>" value="<?php echo jz_encode('login'); ?>">
                                                        </form>
							<form name="loginform" id="loginform" method="POST" action="<?php echo urlize($urla); ?>" onsubmit="return submitLogin()">
								<input type="hidden" name="<?php echo jz_encode('action'); ?>" value="<?php echo jz_encode('login'); ?>">
								<br><br><br><br><br><br>
								<br><br><br><br><br><br>
								<br>
								<?php
									if (!$failed) {
										echo "<br><br><br>";
									}
								?>
								<?php echo word("Username"); ?><br>
								<input size="18" type="text" class="jz_input" name="field1" style="width:146px;">
								<br>
								<?php echo word("Password"); ?><br>
								<input size="18" type="password" class="jz_input" name="field2" style="width:146px;">
								<br>
								<input type="checkbox" class="jz_checkbox" name="remember"> <?php echo word("Remember me"); ?>
								<br><br>
								<input class="jz_submit" type="submit" name="<?php echo jz_encode('submit_login'); ?>" value="<?php echo word("Login"); ?>">
								   <input type="hidden" name="doregister" value="false" />
								<?php $be = new jzBackend();
									$data = $be->loadData('registration');
									if ($data['allow_registration'] == "true") {
									?>
										&nbsp;<input class="jz_submit" type="submit" name="<?php echo jz_encode('self_register'); ?>" value="<?php echo word("Register"); ?>" onclick="document.getElementById('loginform').doregister.value='true'">
									<?php 
									} 
									?>

							</form>
						</td>
						<td height="363" background="style/images/login-background.gif" width="49%" style="border-bottom:1px solid #474747; border-right:1px solid #474747; border-top:1px solid #474747;">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3" height="100%" valign="bottom" align="center" style="border-bottom:1px solid #474747; border-left:1px solid #474747; border-right:1px solid #474747; background-color: #242424;">
							<img src="style/images/login-footer-logo.gif" border="0">
							<br><br><br>
						</td>
					</tr>
				</table>
			<?php
			//this->footer();
		}
		
		
		function pageTop($title = false, $endBreak = "true", $ratingItem = ""){
		        global $this_page, $img_home, $quick_list_truncate, $img_random_play, $cms_mode, 
				$random_play_amounts, $directory_level, $img_up_arrow, $header_drops, $genre_drop, $artist_drop, 
				$album_drop, $quick_drop, $root_dir, $web_root, $song_drop, $audio_types, $video_types, $media_dir, 
				$img_more,$img_random_play_dis, $url_seperator, $help_access, $jukebox, $jukebox_num,
				$disable_random, $jz_lang_file, $show_slimzora, $img_slim_pop, $allow_resample, $resampleRates, $default_random_type, 
				$default_random_count, $display_previous, $echocloud, $display_recommended, $enable_requests, $enable_ratings, 
				$enable_search, $enable_meta_search, $user_tracking_display, $user_tracking_admin_only, $site_title, $node,
			        $jzUSER, $allow_filesystem_modify,$jukebox_display,$jbArr,$include_path;
			
			// Let's see if they wanted to pass a title
			if (!$title) { $title = $site_title; }				

			// Let's setup our objects
			$root = &new jzMediaNode();
			$display = &new jzDisplay();
			$blocks = new jzBlocks();
			?>
			<table class="jz_header_table" width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr class="jz_header_table_tr">
					<td width="80" align="left" valign="top" class="jz_header_table_outer" >
						<table class="" width="80" cellpadding="0" style="padding:5px;" cellspacing="0" border="0">
							<tr class="jz_header_table_tr">
								<td width="80" align="left" valign="top" class="" >
									<nobr>
									<?php									
									// Now let's make sure they can see this
									if ($jzUSER->getSetting("view") === true){
										// Let's display the home icon
									  $display->homeButton();
									  // Let's setup the link for the help docs IF they have access to it
										if ($help_access == $_SESSION['jz_access_level'] or $help_access == "all"){
											$item_url = $root_dir. '/docs/'. $jz_lang_file. '/index.html';
											?>
											<a href="<?php echo $item_url; ?>" onClick="openPopup(this, 500, 500, false, 'Help'); return false;" target="_blank"><?php echo $img_more; ?></a>
											<?php
										}
										
										// Now let's show them the Slimzora popup
										if ($show_slimzora && $jzUSER->getSetting('view') !== false){
										  $display->popupLink("slimzora");
										}	
									} else {
										echo "&nbsp;";
									} if (checkPermission($jzUSER,"play")) {
									  echo '&nbsp';
									  $display->popupLink('plmanager');
									}
									
									// Now let's see if they get the tools menu
									if ($_SESSION['jz_access_level'] == "admin"){
										global $skin, $jz_MenuItemLeft, $jz_MenuSplit, $jz_MenuItemHover, $jz_MainItemHover, $main_img_dir, $jz_MenuItem;	
										//include_once($web_root. $root_dir. '/lib/menu/tools-menu.php');
									}
								?>
								</nobr>
								</td>
							</tr>
						</table>
					</td>
					<td width="100%" valign="top" class="jz_header_table_outer">
						<table width="100%" class="jz_header_table" border="0" cellpadding="0" cellspacing="0" style="padding:5px;">
							<tr class="jz_header_table_tr">
								<td width="50%" valign="top" class="jz_header_table_td">
									<?php
									// Now let's set the header text
									if ($_SESSION['jz_access_level'] <> "noaccess"){
									  echo '<span class="jz_headerTitle">'. jzstripslashes($title). '</span>';
									}
									// Now let's show the rating
									if ($enable_ratings == "true" and $ratingItem <> ""){
										echo "&nbsp;". displayRating($ratingItem, false);
									}
									// Now let's make sure they deleted the "install" directory
									if (is_dir($include_path. "install") and !is_dir($include_path. "CVS")){
										echo "<br><br><strong>";
										echo word("You're Jinzora installation is NOT secure!!!  You need to delete the 'install' directory to secure your installation!!! - Once you delete the 'install' directory this message will go away");
										echo "</strong><br>";
									}
									// Let's see if there is a file that we want to put in the header here
									//echo returnHeaderText();
								?>
								</td>
								<td width="50%" valign="top" class="jz_header_table_td" align="right">
									<div align="right">
									
									<?php
								    $display->loginLink();
									echo " | ";
									// Let's see if the user has logged in, and if not let's show that link
									if ($jzUSER->getSetting('admin') and $allow_filesystem_modify == "true") {
										$url_array = array();
										$url_array['jz_path'] = $node->getPath("String");
										$url_array['action'] = "popup";
										$url_array['ptype'] = "uploadmedia";
										echo '<a class="jz_header_table_href" onClick="openPopup(' . "'". urlize($url_array) ."'" . ',450,400); return false;" href="javascript:;">'. word("Add Media"). '</a> | ';
									}
                                                                                if (false !== $display->popupLink("preferences", word("Preferences"))) {
                                                                                    echo ' | ';
                                                                                }
									// Now let's show them the search box
									if ($_SESSION['jz_access_level'] <> "noaccess" and $_SESSION['jz_access_level'] <> "viewonly" and $_SESSION['jz_access_level'] <> "lofi" and $enable_search <> "false"){
										$url = array();
										$url['action'] = 'powersearch';
										echo "<a href=\"" . urlize($url) . "\" class=\"jz_header_table_href\">" . word("Search"). "</a>";
										// Now let's see if there is a value for the box
										$value = "";
										if (isset($_POST['search_query'])){
											$value = $_POST['search_query'];
										}
										if (isset($_GET['song_title'])){
											$value = $_GET['song_title'];
										}
										?>
										<?php
										$onSubmit = "";
										if ($jukebox == "true" && !defined('NO_AJAX_JUKEBOX')) {
										  $onSubmit = 'onSubmit="return searchKeywords(this,\'' . htmlentities($this_page) . '\');"';
										}
										if ($cms_mode == "true") {
											$method = "GET";
										} else {
											$method = "POST";
										}
										  ?>
										  
											<form action="<?php echo $this_page  ?>" method="<?php echo $method; ?>" name="searchForm" <?php echo $onSubmit; ?>>
											<?php foreach (getURLVars($this_page) as $key => $val) { echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($value) . '">'; } ?>
											<input class="jz_input" type="text" name="search_query" size="15" value="<?php echo $value; ?>">
											<select class="jz_select" name="search_type">
												<option value="ALL">All Media</option>
												
												<?php
													if (distanceTo("artist") !== false){
														echo '<option value="artists">'. word("Artists"). '</option>'. "\n";
													}
													if (distanceTo("album") !== false) {
													  echo '<option value="albums">' . word("Albums"). '</option>'. "\n";
													}
												?>
												<option value="tracks"><?php echo word("Tracks"); ?></option>
 											    <option value="lyrics"><?php echo word("Lyrics"); ?></option>
											</select>
											<input type="hidden" name="doSearch" value="true">
											<input class="jz_submit" type="submit" name="doSearch" value="<?php echo word("Go"); ?>">
											</form>
											</nobr>
										<?php
									}
									
									// Let's show them the up arrow, unless they are viewing the first page
										?>
										<table width="100%" cellpadding="0"><tr><td width="100%" align="right"><div align="right"><nobr>
										   <?php $bcrumbs = $blocks->breadCrumbs();
											
											// Now let's display the header for the block
											$title = "Browse";
											if ($node->getName() <> ""){
												$parent = $node->getParent();
												if ($parent->getName() <> ""){
													$title .= " :: ". $parent->getName();
												}
												$title .= " :: ". $node->getName();
											}
											echo $bcrumbs;
										?>
										</nobr></div></td></tr></table>
										<?php
										    
								?>
								</div>
								</td>
							</tr>
						</table>
						<?php
						// Now, do they want to display where the other users are?
						if ($user_tracking_display == "true"){
							// Now do they only want admins to see this?
							if ($user_tracking_admin_only == "true"){
								if ($_SESSION['jz_access_level'] == "admin"){
									displayUserTracking();
								}
							} else {
								displayUserTracking();
							}
						}
						?>
						</td>
					</tr>
				<?php
				// Now let's see if they are in Jukebox mode, but are NOT an admin they can only stream
				if (checkPermission($jzUSER,"jukebox_queue") && $jukebox_display != "small" && $jukebox_display != "off"){
				    jzTableClose();
				    echo '<div id="jukebox">' . "\n";
				    $blocks->jukeboxBlock();
				    echo '</div>' , "\n";
				    jzTableOpen("100","0","jz_header_table");
				}
				// Let's see if they wanted to turn the drop down boxes off 
				if ($header_drops == "true"){
					?>
					<tr class="jz_header_table_tr">
						<td width="100%" align="right" valign="top" class="jz_header_table_outer" colspan="2" style="padding:5px;">
							<table width="100%" cellpadding="0" cellspacing="0" border="0">
								<tr class="jz_header_table_tr">
								<?php				
					   if (checkPermission($jzUSER,"jukebox_queue") && ($jukebox_display == "small" or $jukebox_display == "minimal")) {
					     ?>
					     <td width="15%" valign="top" class="jz_header_table_td"><div id="smallJukebox">
					     <?php
					     $blocks->smallJukebox(false,'top');
					     ?>					     
					     </div></td>
					     <?php
					   }

	
									// Let's make sure they wanted to see the Genre drop down
									if ($genre_drop != "false" && distanceTo("genre") !== false){
										?>
											<td width="15%" valign="top" class="jz_header_table_td">
												<?php $display->popupLink("genre"); ?>
										<br>
										<?php if ($genre_drop == "true") { ?>
										  <form action="<?php echo $this_page; ?>" method="GET">
										     <?php $display->hiddenPageVars(); $display->dropdown("genre"); ?>
												</form>
												    <?php } ?>
											</td>
										<?php
									}
									
									// Let's see if they are looking at 2 levels or 3 and show them the artists select box
									if ($artist_drop != "false" && distanceTo("artist") !== false){
										?>
											<td width="15%" valign="top" class="jz_header_table_td">
												<?php $display->popupLink("artist"); ?>
											   <br><?php if ($artist_drop == "true") { ?>
												<form action="<?php echo $this_page; ?>" method="GET">
												<?php $display->hiddenPageVars(); $display->dropdown("artist"); ?>
												</form> <?php } ?>
											</td>
										<?php
									}
									
									// Let's see if they are looking at 2 levels or 3 and show them the artists select box
									if ($album_drop != "false" && distanceTo("album") !== false){
										?>
											<td width="15%" valign="top" class="jz_header_table_td">
												<?php $display->popupLink("album"); ?>
											   <br><?php if ($album_drop == "true") { ?>
												<form action="<?php echo $this_page; ?>" method="GET">
												<?php $display->hiddenPageVars(); $display->dropdown("album"); ?>
												</form><?php } ?>
											</td>
										<?php
									}
					
									if ($song_drop != "false"){
										?>
											<td width="15%" valign="top" class="jz_header_table_td">
												<?php $display->popupLink("track"); ?>
											   <br><?php if ($song_drop == "true") { ?>
												<form action="<?php echo $this_page; ?>" method="GET">
												<?php $display->hiddenPageVars(); $display->dropdown("track"); ?>
												</form><?php } ?>
											</td>
										<?php
									}
									// Now let's display the random playlist generator
									if ($quick_drop == "true" and $_SESSION['jz_access_level'] <> "viewonly" and $_SESSION['jz_access_level'] <> "lofi"){
										jzTDOpen("15","left","top","jz_header_table_td","0");
										echo '<nobr>';
										$blocks->randomGenerateSelector($node);
										echo '</nobr>';
										jzTDClose();
									}							
		
									// Now let's display the resampler
									if ($display->wantResampleDropdown($node)){
										jzTDOpen("10","left","top","jz_header_table_td","0");
										$display->displayResampleDropdown($node);
										jzTDClose();
									}			
								jzTRClose();
					
							// Now let's close out
							jzTableClose();
						jzTDClose();
					jzTRClose();
				} 
			// This closes our big table above		
			jzTableClose();
		}
	
	
		function footer($node=false){
			global $jinzora_url, $this_pgm, $version, $allow_lang_choice,
			$this_page, $web_root, $root_dir, $allow_theme_change, $cms_mode, $skin, $show_loggedin_level, 
			$jz_lang_file, $shoutcast, $sc_refresh, $sc_host, $sc_port, $sc_password, $url_seperator, $jukebox, $show_jinzora_footer, 
			$hide_pgm_name, $media_dir, $img_sm_logo, $show_page_load_time, $allow_speed_choice, $img_play, $img_random_play, $img_playlist,
			$raw_img_play, $raw_img_random_play, $raw_img_download, $show_page_load_time,$allow_interface_choice,$allow_style_choice,
			$jzUSER,$jzSERVICES, $cms_mode; 

			$display = &new jzDisplay();

			// First let's make sure they didn't turn the footer off
			if ($show_jinzora_footer){
				$blocks = new jzBlocks();

				$blocks->blockBodyOpen();
				?>
				<table width="100%" cellpadding="5"  style="padding:5px;" cellspacing="0" border="0">
					<tr>
						<td width="20%" align="left">
							<?php
								if ($allow_interface_choice == "true"){
								  $display->interfaceDropdown();
								}
								if ($allow_style_choice == "true"){
								  echo '<br>';
								  $display->styleDropdown();
								}
							?>
						</td>
						<td width="60%" align="center">
							<center>
							<?php jzHREF($jinzora_url,"","","",'<img title="'. $this_pgm. " ". $version. '" alt="'. $this_pgm. " ". $version. '" src="'. $root_dir. '/style/'. $skin. '/powered-by-small.gif" border="0">'); ?>
							</center>
						</td>
						<td width="20%" align="right" valign="middle" nowrap>
							<?php
								if ($show_page_load_time == "true" and $_SESSION['jz_load_time'] <> ""){
									// Ok, let's get the difference
									$diff = round(microtime_diff($_SESSION['jz_load_time'],microtime()),3);
									if ($cms_mode == "false"){
										echo '<span class="jz_artistDesc">';
									}
									echo word("Page generated in"). ": ". $diff. " ". word("seconds");
									if ($cms_mode == "false"){
										echo "</span>";
									}
									echo "<br>";
								}
								if ($jzUSER->getSetting("admin") == true && $node !== false) {
									$display->mediaManagementDropdown($node);
									echo "&nbsp;<br>";
									$display->systemToolsDropdown($node);
									echo "&nbsp;";
								}
							?>
						</td>
					</tr>
				</table></td></tr></table>
				<?php
				$blocks->blockBodyClose();
			}
			$jzSERVICES->cmsClose();
		}

		/**
		* Draws the search results page.
		* 
		* @author Ben Dodson
		* @version 12/18/04
		* @since 11/20/04
		*/
		function searchResults($string, $type, $power_search = false) {
			global $cms_type,$jzSERVICES; 
			
			$display = &new jzDisplay();
			$blocks = &new jzBlocks();
			$tracks = array();
			$nodes = array();
			
			// remember, $this is a frontend.
			// This has to go before SQL querries.
			// If our keywords say to play the results
			// we cannot print any HTML.
			if ($power_search === false) {
			  $check = splitKeywords($string);
			  if (!muteOutput($check['keywords'])) {
			    $display->preheader('Search Results',$this->width,$this->align);
			    $this->pageTop('Search Results');
			  }
			  
			  $results = handleSearch($string, $type);
			  if (sizeof($results) == 0 && muteOutput($check['keywords'])) {
			    $display->preheader('Search Results',$this->width,$this->align);
			    $this->pageTop('Search Results');
			  }
			} else {
			  // Power search:
			  $display->preheader('Search Results',$this->width,$this->align);
			  $this->pageTop('Search Results');
			  $root = new jzMediaNode();
			  $results = $root->powerSearch();
			}
			
			echo '<table width="100%" cellpadding="3"><tr><td>';

			foreach ($results as $val) {
				if ($val->isLeaf()) {
					$tracks[] = $val;	
				}
				else {
					$nodes[] = $val;
				}
			}
			// show the page
			if (sizeof($tracks) > 0) {
				$blocks->blockHeader(sizeof($tracks). " ". word("Matching Tracks"). " ". word("for search"). ' "'. $_POST['search_query']. '"');
				$blocks->blockBodyOpen();
				$blocks->trackTable($tracks, "search");
				$blocks->blockBodyClose();
				$blocks->blockSpacer();
			}
			if (sizeof($nodes) > 0) {
				$blocks->blockHeader(sizeof($nodes). " ". word("Other Matches"). " ". word("for search"). ' "'. $_POST['search_query']. '"');
				$blocks->blockBodyOpen();
				$blocks->nodeTable($nodes,"search");
				$blocks->blockBodyClose();
				$blocks->blockSpacer();
			}
			if (sizeof($nodes) == 0 && sizeof($tracks) == 0) {
				$blocks->blockHeader(word("No matches found"));
			}
			$this->footer();
			$jzSERVICES->cmsClose();
		}
		
		/**
		* Power search page.
		* 
		* @author
		* @version
		* @since
		*/
		function powerSearch() {
			global $this_page, $audio_types, $video_types, $directory_level, $root_dir; 
			
			
			$blocks = &new jzBlocks();
			
			$showHeadFoot = true;
			$display = new jzDisplay();
			// First let's show the header
			$display->preHeader(word("Power Search"),$this->width,$this->align);
			if ($showHeadFoot){ $this->pageTop(word("Power Search")); }
			
			
			echo '<table width="100%" cellpadding="5" style="padding:5px;"><tr><td valign="top" width="10%" valign="top">';
			$blocks->blockHeader("Power Search");
			$blocks->blockBodyOpen();
			
			// Now let's show the power search form
			?>
				<form action="<?php echo $this_page; ?>" method="POST">
				   <input type="hidden" name="powersearch" value="true">
				<table width="100%" cellpadding="3" style="padding:3px;">
					<tr>
						<td width="38%">&nbsp;</td>
						<td width="7%">
							<nobr><?php echo word("Operator"); ?>:</nobr>
						</td>
						<td width="55%">
							<input type="radio" name="operator" checked value="and"> <?php echo word("and"); ?> 
							<input type="radio" name="operator" value="or"> <?php echo word("or"); ?>
						</td>
					</tr>
					<!-- This is the song title line -->
					<tr>
						<td width="38%">&nbsp;</td>
						<td width="7%">
							<nobr><?php echo word("Track title"); ?>:</nobr>
						</td>
						<td width="55%">
							<input type="input" name="song_title" class="jz_input" size="30">
						</td>
					</tr>
					<!-- This is the artists title line -->
					<tr>
						<td width="38%">&nbsp;</td>
						<td width="7%">
							<nobr><?php echo word("Artist"); ?>:</nobr>
						</td>
						<td width="55%">
							<input type="input" name="artist" class="jz_input" size="20">
						</td>
					</tr>
					<!-- This is the album title line -->
					<tr>
						<td width="38%">&nbsp;</td>
						<td width="7%">
							<nobr><?php echo word("Album"); ?>:</nobr>
						</td>
						<td width="55%">
							<input type="input" name="album" class="jz_input" size="20">
						</td>
					</tr>
					<!-- This is the genre title line -->
					<tr>
						<td width="38%">&nbsp;</td>
						<td width="7%">
							<nobr><?php echo word("Genre"); ?>:</nobr>
						</td>
						<td width="55%">
							<input type="input" name="genre" class="jz_input" size="20">
						</td>
					</tr>
					<!-- This is the song duration line -->
					<tr>
						<td width="38%">&nbsp;</td>
						<td width="7%">
							<nobr><?php echo word("Duration"); ?>:</nobr>
						</td>
						<td width="55%">
							<select class="jz_select" name="length_operator">
								<option value="=">=</option>
								<option value=">">></option>
								<option value="<"><</option>
							</select>
							<input type="input" name="length" class="jz_input" size="10">
							<select style="width: 75px;" class="jz_select" name="length_type">
								<option value="minutes"><?php echo word("minutes"); ?></option>
								<option value="seconds"><?php echo word("seconds"); ?></option>
							</select>
						</td>
					</tr>
					<!-- This is the track number line -->
					<tr>
						<td width="38%">&nbsp;</td>
						<td width="7%">
							<nobr><?php echo word("Track number"); ?></nobr>
						</td>
						<td width="55%">
							<select class="jz_select" name="number_operator">
								<option value="=">=</option>
								<option value=">">></option>
								<option value="<"><</option>
							</select>
							<input type="input" name="number" class="jz_input" size="10">
						</td>
					</tr>
					<!-- This is the track year line -->
					<tr>
						<td width="38%">&nbsp;</td>
						<td width="7%">
							<nobr><?php echo word("Year"); ?>:</nobr>
						</td>
						<td width="55%">
							<select class="jz_select" name="year_operator">
								<option value="=">=</option>
								<option value=">">></option>
								<option value="<"><</option>
							</select>
							<input type="input" name="year" class="jz_input" size="10">
						</td>
					</tr>
					<!-- This is the track bit rate line -->
					<tr>
						<td width="38%">&nbsp;</td>
						<td width="7%">
							<nobr><?php echo word("Bitrate"); ?></nobr>
						</td>
						<td width="55%">
							<select class="jz_select" name="bitrate_operator">
								<option value="=">=</option>
								<option value=">">></option>
								<option value="<"><</option>
							</select>
							<?php
								$bit_rates = "32,40,48,56,64,80,96,112,128,160,192,224,256,320";
								$bitArray = explode(',',$bit_rates);
								rsort($bitArray);
								echo ' <select style="width: 60px;" class="jz_select" name="bitrate">';
								echo '<option value=""> - </option>'. "\n";
								for ($c=0; $c < count($bitArray); $c++){
									echo '<option value="'. $bitArray[$c]. '">'. $bitArray[$c]. '</option>'. "\n";
								}
							?>
							</select> kbps
						</td>
					</tr>
					<!-- This is the track sample rate line -->
					<tr>
						<td width="38%">&nbsp;</td>
						<td width="7%">
							<nobr><?php echo word("Sample Rate"); ?></nobr>
						</td>
						<td width="55%">
							<?php
								echo '<select class="jz_select" name="frequency_operator"><option value="=">=</option><option value=">">></option><option value="<"><</option></select>';
								$sample_rates = "48,44.1,32,24,22.05,16,12,11.025,8";		
								$sampleArray = explode(',',$sample_rates);
								echo ' <select style="width: 60px;" class="jz_select" name="frequency">';
								echo '<option value=""> - </option>'. "\n";
								for ($c=0; $c < count($sampleArray); $c++){
								echo '<option value="'. $sampleArray[$c]. '">'. $sampleArray[$c]. '</option>'. "\n";
								}
								echo '</select> kHz';
							?>
						</td>
					</tr>
					<!-- This is the track size line -->
					<tr>
						<td width="38%">&nbsp;</td>
						<td width="7%">
							<nobr><?php echo word("File size"); ?></nobr>
						</td>
						<td width="55%">
							<select class="jz_select" name="size_operator">
								<option value="=">=</option>
								<option value=">">></option>
								<option value="<"><</option>
							</select>
							<input type="input" name="size" class="jz_input" size="10"> Mb
						</td>
					</tr>
					<!-- This is the track type line -->
					<tr>
						<td width="38%">&nbsp;</td>
						<td width="7%">
							<nobr><?php echo word("File type"); ?></nobr>
						</td>
						<td width="55%">
							<?php
								echo ' <select style="width: 60px;" class="jz_select" name="type">';
								echo '<option value=""> - </option>'. "\n";
								$sampleArray = explode('|',$audio_types);
								for ($c=0; $c < count($sampleArray); $c++){
								echo '<option value="'. $sampleArray[$c]. '">'. $sampleArray[$c]. '</option>'. "\n";
								}
								$sampleArray = explode('|',$video_types);
								for ($c=0; $c < count($sampleArray); $c++){
								echo '<option value="'. $sampleArray[$c]. '">'. $sampleArray[$c]. '</option>'. "\n";
								}
								echo '</select>';
							?>
						</td>
					</tr>
					<!-- This is the comments title line -->
					<tr>
						<td width="38%">&nbsp;</td>
						<td width="7%">
							<nobr><?php echo word("Comments"); ?></nobr>
						</td>
						<td width="55%">
							<input type="input" name="comment" class="jz_input" size="30">
						</td>
					</tr>
					<!-- This is the lyrics line -->
					<tr>
						<td width="38%">&nbsp;</td>
						<td width="7%">
							<nobr><?php echo word("Lyrics"); ?></nobr>
						</td>
						<td width="55%">
							<input type="input" name="lyrics" class="jz_input" size="30">
						</td>
					</tr>
					<!-- This is the button line -->
					<tr>
						<td width="38%">&nbsp;</td>
						<td width="7%">

						</td>
						<td width="55%">
							<br><input type="submit" name="doSearch" class="jz_submit" value="<?php echo word("Search"); ?>"> &nbsp;
							<?php
								$url_array = array();
								$url_array['action'] = "popup";
								$url_array['ptype'] = "mozilla";
								echo '<input type="button" onClick="openPopup(' . "'". urlize($url_array) ."'" . ',350,150);" class="jz_submit" value="Install Mozilla Search Plugin"><br>';
							?>
						</td>
					</tr>
				</table>
			</form><br>
			<?php
			
			$blocks->blockBodyClose();
			echo '</td></tr></table>';
			
			// Now let's show the footer
			if ($showHeadFoot){ $this->footer(); }
		
		}		
				
		/**
		* Draws a 'standard' page of type:
		* root, genre, artist, album, disk, generic
		* A frontend doesn't have to support all (or any) of these pagetypes,
		* but should understand they exist.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 11/3/04
		* @since 5/13/04
		*/
		function standardPage($node, $maindiv = false) {
		  global $hierarchy, $include_path,$gzip_handler,$cms_type;
		  // If they have a 'loose' hierarchy, use the genre page for now.
		  $me = $this->name;
		  $display = new jzDisplay();

		  // THIS **HAS** to go before any database calls
		  // or else CMS's break!!
		  if ($node->getName() <> ""){
		    $title = implode(" :: ",$node->getPath());
		    if ($maindiv === false) {
		      $display->preHeader($title,$this->width,$this->align);
		      $this->pageTop($title);
		    }
		  } else {
		  	switch ($hierarchy[0]){
				case "genre";
					$title = word("Genres");
				break;
				case "artist";
					$title = word("Artists");
				break;
				case "album";
					$title = word("Albums");
				break;
			}
			if ($maindiv === false) {
			  $display->preHeader($title,$this->width,$this->align);
			  $this->pageTop($title);
			}
		  }
			
		  if ($maindiv) {
		    if ($gzip_handler == "true" && $cms_type <> "mambo" && $cms_type <> "cpgnuke") {
		      @ob_start('ob_gzhandler');
		    }
		  }
		  // Once preheader is done, we can call to the database.

		  $type = $node->getPType();
		  if (!validateLevel($type)) {
		    $level = $node->getLevel();
		    if ($level == 0) {
		      $type = "root";
		    }
		    else {
		      $type = $hierarchy[$level-1];
		    }
		  }

		  // Disks are really albums:
		  if ($type == "disk") { $type = "album"; }

		  $pageInclude = $include_path. "frontend/frontends/${me}/${type}.php";
		  // if it doesn't exist, check the 'default' config for it.
		  if (!is_file($pageInclude)) {
		    $pageInclude = $include_path. "frontend/frontends/classic/${type}.php";
				
				if (!is_file($pageInclude)) {
					$pageInclude = $include_path. "frontend/frontends/${me}/generic.php";
				}				
				if (!is_file($pageInclude)) {
					$pageInclude = $include_path. "frontend/frontends/classic/generic.php";
				}
		    // maybe don't do this-just crash.
		    if (!is_file($pageInclude)) {
		      die("Invalid level '${type}' in your hierarchy.");
		    }
		  }
			
		  require_once($pageInclude);
		  // each of those php files has a drawPage() function
		  // that takes a node as input.
		  // It draws the page from the given node.
		  // The root node is passed for the home page.
		  drawPage($node);

		  if ($maindiv === false && $this->standardFooter) {
		    $this->footer($node);
		  }
		}

	  }
?>
