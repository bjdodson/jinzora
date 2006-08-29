<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	echo '<body onLoad="setup7.admin_user.focus();"></body>';
	
	// Let's figure out the path stuff so we'll know how/where to include from$form_action = "index.php?install=step6";
	$form_action = setThisPage() . "install=step6";

	// Now let's include the left
	include_once($include_path. 'install/leftnav.php');
?>
<script language="JavaScript">
<!--

var firstPass;
var secondPass;
function verifyPass(){
	firstPass = document.setup7.admin_pass.value;
	secondPass = document.setup7.admin_pass2.value;
	proceed = false;
	if (firstPass != secondPass){
		alert('Error: admin passwords do not match!');
	} else {
		if (firstPass != ""){
			proceed = true;
		} else {
			alert('Error: admin password can not be blank!');
		}
	}
	// Now let's check the frontend
	if (document.setup7.frontend.value == ""){
		alert("<?php echo $word_frontend_select; ?>");
		proceed = false;
	}
	
	if (proceed){
		document.setup7.submit();
	}
}
	
function checkaccess(){
	if (document.setup7.default_access.value == "admin"){
		alert('<?php echo $word_admin_alert; ?>');
	}
	if (document.setup7.default_access.value == "user"){
		alert('<?php echo $word_user_alert; ?>');
	}
	if (document.setup7.default_access.value == "poweruser"){
		alert('<?php echo $word_user_alert; ?>');
	}
	if (document.setup7.default_access.value == "lofi"){
		alert('<?php echo $word_user_alert; ?>');
	}
}
function checkcmsaccess(){
	if (document.setup7.default_cms_access.value == "admin"){
		alert('<?php echo $word_admin_alert; ?>');
	}
	if (document.setup7.default_cms_access.value == "user"){
		alert('<?php echo $word_user_alert; ?>');
	}
	if (document.setup7.default_cms_access.value == "poweruser"){
		alert('<?php echo $word_user_alert; ?>');
	}
	if (document.setup7.default_cms_access.value == "lofi"){
		alert('<?php echo $word_user_alert; ?>');
	}
}

function checkEnableTagScan() {
	if (document.setup7.importer.value == "filesystem") {
		document.setup7.readTags.disabled = false;
	} else {
		document.setup7.readTags.disabled = true;
	}
}

function checkReadTagData(){
	if (document.setup7.readTags.value == "true"){
		alert("<?php echo $word_read_tags_note; ?>");
	}
}

function enableCustom(){
	document.setup7.customhierarchy.disabled = false;
	document.setup7.hierarchy.disabled = true;
}
function enableStandard(){
	document.setup7.customhierarchy.disabled = true;
	document.setup7.hierarchy.disabled = false;
}


// -->
</script>     
<div id="main">
	<a href="http://www.jinzora.com" target="_blank"><img src="<?php echo $include_path; ?>install/logo.gif" border="0" align="right" vspace="5" hspace="0"></a>
	<h1><?php echo $word_main_settings; ?></h1>
	<p>
	<?php echo $word_main_settings_note; ?>
	<div class="go">
		<span class="goToNext">
			<?php echo $word_main_settings; ?>
		</span>
	</div>
	<form action="<?php echo $form_action; ?>" name="setup7" method="post">
		<?php
			$PostArray = $_POST;
			foreach ($PostArray as $key => $val) {
			  if (!stristr($key,"submit")){
			  	echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
			  }
		   }
		?>	
		<table width="100%" cellspacing="0" cellpadding="3" border="0">
			<tr>
				<td class="td" width="40%" align="left">
					<?php echo $word_admin_user; ?>
				</td>
				<td class="td" width="1">&nbsp;</td>
				<td class="td" width="60%" align="left">
					<input type="text" name="admin_user" value="admin" onmouseover="return overlib('<?php echo $word_admin_user_help; ?>', FGCOLOR, '#eeeeeee');" onmouseout="return nd();">
					<!--&nbsp; <a class="helpbox2" href="javascript:void(0);" onmouseover="return overlib('<?php echo $word_admin_user_help; ?>');" onmouseout="return nd();">?</a>-->
				</td>
			</tr>
			<?php
				// If we are in CMS mode we don't need the password fields				
				if ($cms_type == "standalone"){
			?>
			<tr>
				<td class="td" width="40%" align="left" valign="top">
					<?php echo $word_admin_pass; ?>
				</td>
				<td class="td" width="1">&nbsp;</td>
				<td class="td" width="60%" align="left">
					<input type="password" name="admin_pass" onmouseover="return overlib('<?php echo $word_admin_pass_help; ?>', FGCOLOR, '#eeeeeee');" onmouseout="return nd();">
					<!--&nbsp; <a class="helpbox2" href="javascript:void(0);" onmouseover="return overlib('<?php echo $word_admin_pass_help; ?>');" onmouseout="return nd();">?</a>-->
				</td>
			</tr>
			<tr>
				<td class="td" width="40%" align="left" valign="top">
					<?php echo $word_confirm_password; ?>
				</td>
				<td class="td" width="1">&nbsp;</td>
				<td class="td" width="60%" align="left" onmouseover="return overlib('<?php echo $word_admin_pass_help; ?>', FGCOLOR, '#eeeeeee');" onmouseout="return nd();">
					<input type="password" name="admin_pass2">
				</td>
			</tr>
			<?php
				} else {
					$admin_pass = time();
					echo '<input type="hidden" name="admin_pass" value="'. $admin_pass. '"><input type="hidden" name="admin_pass2" value="'. $admin_pass. '">';
				}
			?>
			<tr>
				<td class="td" width="40%" align="left" valign="top">
					<?php echo $word_default_access; ?>
				</td>
				<td class="td" width="1">&nbsp;</td>
				<td class="td" width="60%" align="left">
					<select name="default_access" onChange="checkaccess();" style="width:125px;" onmouseover="return overlib('<?php echo $word_access_level_help; ?>', FGCOLOR, '#eeeeeee');" onmouseout="return nd();">
						<option value="noaccess"><?php echo $word_no_access; ?></option>
						<option value="viewonly"><?php echo $word_viewonly; ?></option>
						<option value="lofi"><?php echo $word_lofi; ?></option>					
						<option value="user"><?php echo $word_user; ?></option>
						<option value="admin"><?php echo $word_admin; ?></option>
					</select>
					<!--&nbsp; <a class="helpbox2" href="javascript:void(0);" onmouseover="return overlib('<?php echo $word_access_level_help; ?>', FGCOLOR, '#eeeeeee');" onmouseout="return nd();">?</a>-->
				</td>
			</tr>
			<?php
				// Now let's see if they are in CMS mode
				if ($_POST['cms_type'] <> "standalone"){
					?>
					<tr>
						<td class="td" width="40%" align="left" valign="top">
							<?php echo $word_cms_default_access; ?>
						</td>
						<td class="td" width="1">&nbsp;</td>
						<td class="td" width="60%" align="left">
							<select name="default_cms_access" onChange="checkcmsaccess();" style="width:125px;" onmouseover="return overlib('<?php echo $word_access_level_help; ?>', FGCOLOR, '#eeeeeee');" onmouseout="return nd();">
								<option value="noaccess"><?php echo $word_no_access; ?></option>
								<option value="lofi"><?php echo $word_lofi; ?></option>					
								<option value="user"><?php echo $word_user; ?></option>
								<option value="admin"><?php echo $word_admin; ?></option>
							</select>
							<!--&nbsp; <a class="helpbox2" href="javascript:void(0);" onmouseover="return overlib('<?php echo $word_access_level_help; ?>', FGCOLOR, '#eeeeeee');" onmouseout="return nd();">?</a>-->
						</td>
					</tr>
					<?php
				}
			?>
			<tr>
				<td class="td" width="40%" align="left" valign="top">
					<?php echo $word_backend_type; ?>
				</td>
				<td class="td" width="1">&nbsp;</td>
				<td class="td" width="60%" align="left">
					<select onChange="setBackend();" name="backend" style="width:125;" onmouseover="return overlib('<?php echo $word_backend1_note; ?>', FGCOLOR, '#eeeeeee');" onmouseout="return nd();">
					<?php if (function_exists('mysql_query')) { ?>
						<option value="mysql"><?php echo 'MySQL ' . $word_database; ?></option>
						<?php } if (function_exists('sqlite_query')) { ?>
						<option value="sqlite"><?php echo 'SQLite ' . $word_database; ?></option>
						<?php } if (function_exists('mssql_query')) { ?>
						<option value="mssql"><?php echo 'Microsft SQL ' . $word_database; ?></option>
						<?php } if (function_exists('pg_query')) { ?>
						<option value="pgsql"><?php echo 'Postgres ' . $word_database; ?></option>
						<?php } if (function_exists('dbx_query')) { ?>
						<option value="dbx"><?php echo 'DBX ' . $word_database; ?></option>
						<?php } ?>
						<option value="cache"><?php echo $word_cache; ?></option>
					</select>
					<!--&nbsp; <a class="helpbox2" href="javascript:void(0);" onmouseover="return overlib('<?php echo $word_backend1_note; ?>', FGCOLOR, '#eeeeeee');" onmouseout="return nd();">?</a>-->
				</td>
			</tr>
				<td class="td" width="40%" align="left" valign="top">
					<?php echo $word_frontend; ?>
				</td>
				<td class="td" width="1">&nbsp;</td>
				<td class="td" width="20%" align="left"  valign="top">
				    <?php
				        // Now let's set the frontend to Andro if we're in CMS mode
				        $sel = "";
				        if ($cms_mode == "true"){
				            $sel = " selected ";
				        }
				    ?>
					<select name="frontend" id="frontend" style="width:125;" onchange="javascript:change_image();">
						<option value=""> - </option>
						<option value="slick">Slick</option>
						<option <?php echo $sel; ?> value="andro">Andro</option>
						<option value="classic">Jinzora Classic</option>
						<option value="medialibrary">Media Library</option>
						<option value="gina">Gina</option>
						<option value="netjuke">Netjuke</option>
					</select>
				</td>
			</tr>
			<?php
				// Now let's see if they are in CMS mode
				if ($_POST['cms_type'] == "standalone"){
					?>
					<tr>
						<td class="td" width="40%" align="left" valign="top">
							<?php echo $word_style; ?>
						</td>
						<td class="td" width="1">&nbsp;</td>
						<td class="td" width="20%" align="left">
								<?php
										// Now let's set the frontend to Andro if we're in CMS mode
										$sel = "";
										if ($cms_mode == "true"){
												$sel = " selected ";
										}
								?>
							<select name="style" id="style" style="width:125;" onchange="javascript:change_image();">
								<option value=""> - </option>						
								<option value="all-american">all-american</option>
								<option value="bluegray">bluegray</option>
								<option value="darklights">darklights</option>
								<option value="goldmine">goldmine</option>
								<option value="netjuke">netjuke</option>
								<option value="sandstone">sandstone</option>
								<option value="slamp">slamp</option>
								<option value="slick">slick</option>
								<option value="slicklime">slicklime</option>
								<option value="slicksilver">slicksilver</option>
								<option value="steel">steel</option>
								<option value="sunflower">sunflower</option>
								<option value="vampire">vampire</option>
							</select>
							<br /><br />
							<span id="imageName"></span>
							<script>
								function change_image(action){
									s = document.getElementById("style");
									f = document.getElementById("frontend");
									if (s.value !== ""){
										if (f.value !== ""){
											i = document.getElementById("imageName");
											i.innerHTML = '<img width="120" src="<?php echo $include_path; ?>install/thumbs/' + f.value + '/' + s.value + '.gif" border="0" />';									
										}
									}
								}
							</script>
						</td>
					</tr>
					<?php
				} else {
					echo '<input type="hidden" name="style" value="cms-theme">';
				}
			?>
		</table>
		<div class="go">
			<span class="goToNext">
				<?php echo $word_import_settings; ?>
			</span>
		</div>
		The way you import your media is perhaps the most important step during the installation process.  This tells Jinzora how it should read your
		existing media then how you would like it displayed when the installation is complete.  It is VERY important that you understand the settings
		below before proceeding.
		<br><br>
		<table width="100%" cellspacing="0" cellpadding="3" border="0">
			<tr>
				<td class="td" width="40%" align="left" valign="top">
					<?php echo $word_data_structure; ?>
				</td>
				<td class="td" width="1">&nbsp;</td>
				<td class="td" width="60%" align="left">
					<select onChange="checkEnableTagScan(); document.setup7.nextbutton.disabled=false;" name="importer" style="width:125;" onmouseover="return overlib('<?php echo $word_backend2_note; ?>', FGCOLOR, '#eeeeeee');" onmouseout="return nd();">
						<option value=""> - </option>
						<option value="filesystem"><?php echo $word_filesystem; ?></option>
						<option value="id3tags"><?php echo $word_id3; ?></option>
					</select>
					<!--&nbsp; <a class="helpbox2" href="javascript:void(0);" onmouseover="return overlib('<?php echo $word_backend2_note; ?>', FGCOLOR, '#eeeeeee');" onmouseout="return nd();">?</a>-->
				</td>
			</tr>		
			<tr>
				<td class="td" width="40%" align="left" valign="top">
					<?php echo $word_read_tag_data; ?>
				</td>
				<td class="td" width="1">&nbsp;</td>
				<td class="td" width="60%" align="left">
					<select onChange="checkReadTagData();"  disabled name="readTags" style="width:125;" onmouseover="return overlib('<?php echo $word_read_tag_data_note; ?>', FGCOLOR, '#eeeeeee');" onmouseout="return nd();">
						<option value="false"><?php echo $word_false; ?></option>
						<option value="true"><?php echo $word_true; ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="td" width="40%" align="left" valign="top" rowspan=2>
					<?php echo $word_layout; ?>
				</td>
				<td class="td" width="1" rowspan=2>&nbsp;</td>
				<td class="td" width="60%" align="left" >
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="10%" class="td">
								<nobr><input onClick="enableStandard();" type="radio" name="hierarchysource" value="standard" checked><i><?php echo $word_standard; ?></i>&nbsp;&nbsp;&nbsp;</nobr>
							</td>
							<td width="90%" class="td">
								<select name="hierarchy" style="width:115;" onmouseover="return overlib('<?php echo $word_layout_help; ?>', FGCOLOR, '#eeeeeee');" onmouseout="return nd();">
									<option value="genre/artist/album/track">Genre</option>
									<option value="artist/album/track">Artist</option>
									<option value="album/track">Album</option>
								</select>
								<!--&nbsp; <a class="helpbox2" href="javascript:void(0);" onmouseover="return overlib('<?php echo $word_layout_help; ?>', FGCOLOR, '#eeeeeee');" onmouseout="return nd();">?</a>-->
							</td>
						</tr>
						<tr>
							<td width="10%" class="td">
								<nobr><input onClick="enableCustom();" type="radio" name="hierarchysource" value="custom"><i><?php echo $word_custom; ?></i>&nbsp;&nbsp;&nbsp;</nobr>
							</td>
							<td width="90%" class="td">
								<input disabled type="text" name="customhierarchy" value="genre/artist/album/track" onmouseover="return overlib('<?php echo $word_layout_custom_help; ?>', FGCOLOR, '#eeeeeee');" onmouseout="return nd();">
								<!--&nbsp; <a class="helpbox2" href="javascript:void(0);" onmouseover="return overlib('<?php echo $word_layout_custom_help; ?>', FGCOLOR, '#eeeeeee');" onmouseout="return nd();">?</a>-->
							</td>
						</tr>
					</table>
				</td></tr>
				<tr><td class ="td" width="60%" align="left">
					
				</td>
			</tr>
		</table>
		<br>
		
		
		<div class="go">
			<span class="goToNext">
				&nbsp; <input name="nextbutton" disabled onClick="javascript:verifyPass();" type="button" class="submit" value="<?php echo $word_proceed_to_backend; ?>">
			</span>
		</div>
		</form>
	</div>
<?php
	// Now let's include the top
	include_once($include_path. 'install/footer.php');
?>
