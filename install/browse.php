<?php 
	// Let's allow access
	define('JZ_SECURE_ACCESS','true');
	
	// Now what is the form info?
	$prefix = "";
	$media_path_val = "media_path";
	$media_length_val = "media_length";
	if (isset($_GET['prefix'])){
		if ($_GET['prefix'] <> ""){
			$prefix = $_GET['prefix'];
			$media_path_val = "edit_media_path";
			$media_length_val = "edit_media_length";
		}
	}
?>
<script language="JavaScript">
<!--
function returnData(){
	window.opener.document.setup8.<?php echo $media_path_val; ?>.value = document.browserForm.directory.value;
	window.opener.document.setup8.<?php echo $media_length_val; ?>.value = document.browserForm.media_length.value;
	window.opener.document.setup8.submit();
	window.close();
}
function backData(){
	history.back();
}
// -->
</script>
<?php
	// Now let's include the language file
	include_once(getcwd(). '/lang/'. $_GET['lang']. '/lang.php');
	
	$audio_types = "mp3|ogg|wma|wav|aac|mp4|rm";
	$video_types = "avi|wmv|mpeg|mov|mpg|rv";
?>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<link rel="stylesheet" type="text/css" href="style.css">
<?php
	// Did they submit the form?
	if (isset($_POST['directory'])){
		echo "<br>". $word_analyzing_import. "<br>";
		echo "<strong>". $_POST['directory']. "</strong><br><br>";
		echo '<div id="status"></div>';
		function readAllDirs2($dirName, &$readCtr){
			global $audio_types, $video_types, $word_files_analyzed;
			
			// Let's up the max_execution_time
			ini_set('max_execution_time','6000');
			// Let's look at the directory we are in		
			if (is_dir($dirName)){
				$d = dir($dirName);
				if (is_object($d)){
					while($entry = $d->read()) {
						// Let's make sure we are seeing real directories
						if ($entry == "." || $entry == "..") { continue;}
						if ($readCtr % 100 == 0){ 
							?>
							<script language="javascript">
								p.innerHTML = '<b><?php echo $readCtr. " ". $word_files_analyzed; ?></b>';									
								-->
							</SCRIPT>
							<?php 
							@flush(); @ob_flush();
						}
						// Now let's see if we are looking at a directory or not
						if (@filetype($dirName. "/". $entry) <> "file"){
							// Ok, that was a dir, so let's move to the next directory down
							readAllDirs2($dirName. "/". $entry, $readCtr);
						} else {
							if (preg_match("/\.($audio_types|$video_types)$/i", $entry)){
								$readCtr++;
								$_SESSION['jz_full_counter']++;
							}							
						}			
					}
					// Now let's close the directory
					$d->close();
				}
			}		
		}
		
		// Ok, now let's figure out the
		$dirName = $_POST['directory'];
		$readCtr = 0; $_SESSION['jz_full_counter'] = 0;
		?>
		<script language="javascript">
			p = document.getElementById("status");							
			-->
		</SCRIPT>
		<?php
		readAllDirs2($dirName, $readCtr);
		// Now let's see how long we think it will take
		if ($_COOKIE['jz_read_tags'] == "false"){
			$takeTime = round((($_SESSION['jz_full_counter'] / 15)/350),2);
		} else {
			$takeTime = round((($_SESSION['jz_full_counter'] / 15)/60),2);
		}
		if ($_GET['readTags'] == "false"){
			$takeTime = round($takeTime / 10,2);
		}
		// Now let's import
		?>
		<script language="javascript">
			p.innerHTML = '&nbsp;';									
			-->
		</SCRIPT>
		<?php 
		@flush(); @ob_flush();
		echo str_replace("YYYY",$takeTime,str_replace("XXXXX",$_SESSION['jz_full_counter'],$word_import_message1));
		echo '<form name="browserForm"><input type="hidden" value="'. $_POST['directory']. '" name="directory"><input type="hidden" value="'. $_SESSION['jz_full_counter']. '" name="media_length"></form>';
		echo '<br><br><br><input type="button" value="'. $word_proceed. '" onClick="returnData();"> <input type="button" value="'. $word_back. '" onClick="backData();">';

		exit();
	}
?>
<body>
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td width="100%" class="td">
				<h2><?php echo $word_browse_for_media; ?>:</h2>
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td width="100%" class="td">
				<?php
					// Now let's get the dirs
					if (isset($_GET['dir'])){
						$dirName = $_GET['dir'];
						$os = "";
						if (is_dir("c:/")){
							$os = "win";							
						}
					} else {
						// Now let's see if we are on linux or Windows
						$os = "";
						if (is_dir("c:/")){
							$os = "win";							
						}
						if ($os !== "win"){
							$dirName = "/";
						} else {
							$dirName = "c:";
						}
					}
				?>
					<nobr><?php echo $word_server_directory; ?><br>
					<?php 
						if ($os == "win"){
							// Now let's let them select other drives
							echo '<form name="browserForm" action="browse.php?lang='. $_GET['lang']. '&prefix='. $prefix. '" method="get">';
							echo '<input type="hidden" name="lang" value="'. $_GET['lang']. '">';
							echo '<select name="dir" onChange="submit()">';
							$ctr=99;
							while($ctr<123){
								echo '<option ';
								if ($dirName == chr($ctr). ":"){ echo " selected "; }
								echo ' value="'. chr($ctr). ':">'. chr($ctr). ':</option>';
								$ctr++;
							}
							echo '</select> ';
							echo '</form>';
						}
					?>
					<form name="browserForm" action="browse.php?lang=<?php echo $_GET['lang']. '&prefix='. $prefix; ?>" method="post">
					<input type="text" value="<?php echo $dirName; ?>" name="directory" size="20"> 
					<input type="submit" value="<?php echo $word_analyze; ?>" name="rData"></nobr>
				</form>
				<br><br>
			</td>
		</tr>
		<tr>
			<td width="100%" class="td">
				<strong><?php echo $word_directories; ?>:</strong>
				<br>
				<?php
					if (is_dir($dirName) and is_readable($dirName)){
						$d = @dir($dirName);
						echo '<a href="browse.php?lang='. $_GET['lang']. "&prefix=". $prefix. '">'. $word_return_to_root. '</a><br>';
						while($entry = @$d->read()) {
							$dirArray[] = $entry;
						}
						$d->close();
						sort($dirArray);
						for ($i=0; $i < count($dirArray); $i++){
							// Let's make sure this isn't the local directory we're looking at
							if ($dirArray[$i] == "." || $dirArray[$i] == "..") { continue;}
							if (is_dir($dirName. "/". $dirArray[$i])){
								if ($dirName == "/"){ $dir = $dirName. $dirArray[$i]; } else { $dir = $dirName. "/". $dirArray[$i]; }
								echo '&nbsp; &nbsp;<a href="browse.php?lang='. $_GET['lang']. '&dir='. $dir. "&prefix=". $prefix. '">'. "/". $dirArray[$i]. "</a><br>";
							}
						}
					}
				?>
			</td>
		</tr>
	</table>
</body>