<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	// Ok, now that we are on step 3 let's lock the install
	@touch($include_path. "temp/install.lock");
	
	// Let's figure out the path stuff so we'll know how/where to include from$form_action = "index.php?install=step4";
	$form_action = setThisPage() . "install=step4";

	// Now let's include the left
	include_once($include_path. 'install/leftnav.php');
?>     
<div id="main">
	<a href="http://www.jinzora.com" target="_blank"><img src="<?php echo $include_path; ?>install/logo.gif" border="0" align="right" vspace="5" hspace="0"></a>
	<h1><?php echo $word_gpl; ?></h1>
	<p>
	<?php echo $word_must_agree; ?>
	<div class="go">
		<span class="goToNext">
			<?php echo $word_gpl; ?>
		</span>
	</div>
	<?php
		// Ok, now let's read in the GPL
		$filename = $include_path. "install/lang/". $_POST['jz_lang_file']. "/gpl.txt";
		$handle = fopen ($filename, "rb");
		$contents = fread ($handle, filesize ($filename));
		fclose ($handle);
		
	?>
	<form action="<?php echo $form_action; ?>" name="install_form" method="post">
		<?php
			$PostArray = $_POST;
			foreach ($PostArray as $key => $val) {
			  if (!stristr($key,"submit")){
			  	echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
			  }
		   }
		?>
		<textarea style="width: 480; height: 200px;" "rows="20"><?php echo $contents; ?></textarea><br>
		<input type="checkbox" onClick="enableForm(this.checked);" name="agree"> <?php echo $word_i_agree; ?>
		<br>
		<div class="go">
			<span class="goToNext">
				&nbsp; <input type="submit" name="submit_step3" class="submit">
			</span>
		</div>
	</form>
	</div>
<?php
	// Now let's include the top
	include_once($include_path. 'install/footer.php');
?>
<script language="JavaScript">
<!--
function disableFormStuff(){
	/* First let's hide the submit button until they type in AGREE and click the check box */
	document.install_form.submit_step3.value="<?php echo $word_you_must_agree; ?>";
	document.install_form.submit_step3.disabled=true;	
}

function enableForm(checked){
	if (checked){
		/* Now let's show them the submit button */
		document.install_form.submit_step3.value="<?php echo $word_proceed_to_install; ?>";
		document.install_form.submit_step3.disabled=false;		
	} else {
		document.install_form.submit_step3.value="<?php echo $word_you_must_agree; ?>";
		document.install_form.submit_step3.disabled=true;	
	}
}
disableFormStuff();
// -->
</script>
