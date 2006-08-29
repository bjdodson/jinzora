<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	// Let's figure out the path stuff so we'll know how/where to include from$form_action = "index.php?install=step5";
	$form_action  = setThisPage() . "install=step5";
	$form_action2 = setThisPage() . "install=step8";
	$form_action3 = setThisPage() . "install=step4";

	// Now let's include the left
	include_once($include_path. 'install/leftnav.php');
	
	$_POST['cms_type'] = $cms_type;
?>
<div id="main">
	<a href="http://www.jinzora.com" target="_blank"><img src="<?php echo $include_path; ?>install/logo.gif" border="0" align="right" vspace="5" hspace="0"></a>
	<h1><?php echo $word_install_type; ?></h1>
	<p>
	<?php echo $word_install_type_note; ?>
	<div class="go">
		<span class="goToNext">
			<?php echo $word_install_type; ?>
		</span>
	</div>
	<br>
	<?php
		if ((isset($config_version) && $config_version <> "") and
			(!isset($_POST['newinstall']) || $_POST['newinstall'] == "")){
			?>
				<table width="100%" cellspacing="0" cellpadding="3" border="0">
					<tr>
						<td class="td" width="30%" align="left" valign="middle">
							<?php echo $word_install_type; ?>:
						</td>
						<td class="td" width="1">&nbsp;</td>
						<td class="td" width="70%" align="left">
							<form action="<?php echo $form_action2; ?>" name="setup5" method="post">
								<?php
									$PostArray = $_POST;
									foreach ($PostArray as $key => $val) {
										if (!stristr($key,"submit")){
											echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
										}
									 }
								?>
								<input type="submit" name="upgrade" class="submit" value="Upgrade"> &nbsp; 
							</form>
							<form action="<?php echo $form_action3; ?>" name="setup5" method="post">
								<?php
									$PostArray = $_POST;
									foreach ($PostArray as $key => $val) {
										if (!stristr($key,"submit")){
											echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
										}
									 }
								?>
								<input type="submit" name="newinstall" class="submit" value="New install">
							</form>
						</td>
					</tr>
				</table>
		<?php
		} else {
			?>
			<form action="<?php echo $form_action; ?>" name="setup5" method="post">
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
						<td class="td" width="30%" align="left" valign="top">
							<?php echo $word_install_type; ?>:
						</td>
						<td class="td" width="1">&nbsp;</td>
						<td class="td" width="70%" align="left">
							<select name="cms_type" onmouseover="return overlib('<?php echo $word_install_type_help; ?>', FGCOLOR, '#eeeeeee');" onmouseout="return nd();">
							<?php 
								foreach (getAllCMS() as $key => $label) {
									echo '<option value="' . htmlentities($key) . '"';
									if ($_POST['cms_type'] == $key) {
										echo ' selected';
									}
									echo '>' . htmlentities($label) . '</option>';
								}
							?>
							</select>
							<!--&nbsp; <a class="helpbox2" href="javascript:void(0);" onmouseover="return overlib('<?php echo $word_install_type_help; ?>');" onmouseout="return nd();">?</a>-->
							<?php
								if ($pn <> "" or $phpnuke <> "" or $mambo <> ""){
									echo "<br><br>". $word_cms_detect;
								}
							?>
							<br>
							<?php echo $word_install_type_clarify; ?>
						</td>
					</tr>
				</table>
				<br>
				<table width="100%" cellspacing="0" cellpadding="3" border="0">
					<tr>
						<td class="td" width="30%" align="left" valign="top">
							<?php echo $word_enable_jukebox; ?>:
						</td>
						<td class="td" width="1">&nbsp;</td>
						<td class="td" width="70%" align="left">
							<select name="jukebox" onmouseover="return overlib('<?php echo $word_enable_jukebox_help; ?>', FGCOLOR, '#eeeeeee');" onmouseout="return nd();">
								<option selected value="false">Streaming Only</option>
								<option value="true">Streaming & Jukebox</option>
							</select>
							<!--&nbsp; <a class="helpbox2" href="javascript:void(0);" onmouseover="return overlib('<?php echo $word_enable_jukebox_help; ?>');" onmouseout="return nd();">?</a>-->
							<?php
								if ($pn <> "" or $phpnuke <> "" or $mambo <> ""){
									echo "<br><br>". $word_cms_detect;
								}
							?>
							<br>
							<?php echo $word_enable_jukebox_note; ?>
						</td>
					</tr>
				</table>
				<br>
				<div class="go">
					<span class="goToNext">
						&nbsp; <input type="submit" name="submit_step5" class="submit" value="<?php echo $word_proceed_main_settings; ?>">
					</span>
				</div>
			</form>
		<?php
			}
		?>
	</div>
<?php
	// Now let's include the top
	include_once($include_path. 'install/footer.php');
?>
