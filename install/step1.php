<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	// Let's figure out the path stuff so we'll know how/where to include from
	$form_action = setThisPage() . "install=step2";

	// Now let's include the left TEST
	include_once($include_path. 'install/leftnav.php');

	// Let's set a session variable so we can test for session support
	$_SESSION['jz_sess_test'] = 0;
?>

<div id="main">
	<a href="http://www.jinzora.com" target="_blank"><img src="<?php echo $include_path; ?>install/logo.gif" border="0" align="right" vspace="5" hspace="0"></a>
	<h1>Welcome to Jinzora <?php echo $version; ?>!</h1>
	<p>
	Welcome to the Jinzora installer. This installer will guide you through the process of installing Jinzora on your webserver.
	The installer is documented throughout the entire process.
	<br>
	<?php
		// Now let's check to see if the install has begun
		if (is_file($include_path. "temp/install.lock")){
			echo "<br><br><strong>Attention!</strong><br><br>This Jinzora installation has already been started by someone else.  ";
			echo "You will not be able to access Jinzora until that installation is complete.  If you are seeing this message and are the ";
			echo "administrator of this site you need to remove the file 'install.lock' in your the directory ". getcwd(). "/temp Jinzora temp directory before proceeding";
			echo '</div>';
			include_once($include_path. 'install/footer.php');
			exit();
		}
	?>
	<?php
		// Let's reset our tracking session variables just in case the user restarts the install
		unset($_SESSION['all_media_paths']);

		// Now let's read the news from the Jinzora site IF we can
		$contents = "";
		$url = "http://wbi.jinzora.com/changelogs/3alpha1.html";
		$url_parsed = parse_url($url);
		$host = $url_parsed["host"];
		$path = $url_parsed["path"];
		$out = "GET $path HTTP/1.0\r\nHost: $host\r\n\r\n";
		$fp = @fsockopen($host, 80, $errno, $errstr, 30);
		if ($fp){
			fwrite($fp, $out);
			$body = false;
			while (!feof($fp)) {
			   $s = fgets($fp, 1024);
			   if ( $body )
				   $contents .= $s;
			   if ( $s == "\r\n" )
				   $body = true;
			}
			fclose($fp);
						
			// Now let's get the specific changelog for this build
			$needle = "<!-- ". $version. " -->";
			$needle2 = "<!-- /". $version. " -->";
			$contents = substr($contents,strpos($contents,$needle));
			$contents = substr($contents,0,strpos($contents,$needle2));

			// Ok, now let's get the build number
			$build="";
			$url = "http://wbi.jinzora.com/current-build.txt";
			$url_parsed = parse_url($url);
			$host = $url_parsed["host"];
			$path = $url_parsed["path"];
			$out = "GET $path HTTP/1.0\r\nHost: $host\r\n\r\n";
			$fp2 = @fsockopen($host, 80, $errno, $errstr, 30);
			if ($fp2){
				fwrite($fp2, $out);
				$body = false;
				while (!feof($fp2)) {
				   $s = fgets($fp2, 1024);
				   if ( $body )
					   $build .= $s;
				   if ( $s == "\r\n" )
					   $body = true;
				}
			}
			$build = trim($build);

			// Now let's make sure they are using the current build
			if ($version < $build){
				echo "<br><strong>WARNING: You are not using the latest Jinzora Release!</strong><br><br>";
				echo "You are using version <strong>". $version. "</strong>. The current Jinzora Release is <strong>". $build. "</strong>.<br><br>";
				echo 'It is highly recommended to abort this installation and to <a href="http://www.jinzora.com/download/release">download</a> the latest version!<br>';
			}
			echo $contents;
		} else {
			echo "<br>Sorry, we couldn't contact www.jinzora.com for the latest news about Jinzora.  Please make sure you are".
			   	 " installing the latest version of Jinzora by visiting <a href=http://www.jinzora.com>www.jinzora.com</a><br><br>";
		}
	?>
	<br />

	<div class="go">
		<span class="goToNext">
			Jinzora <?php echo $version; ?> Changelog - <a href="http://en.jinzora.com/development/changelog" target="_blank">SVN Changelog</a>
		</span>
	</div>
	<iframe src="http://wbi.jinzora.com/changelogs/3alpha1.html" height="150px" width="510px" frameborder="0"></iframe>
	<br><br>
	<div class="go">
		<span class="goToNext">
			Language
		</span>
	</div>
	<form action="<?php echo $form_action; ?>" name="setup2" method="post">
		Please select a language to use during installation. You can change to another language once the installer is finished.
		<br><br>
		<table width="100%" cellspacing="0" cellpadding="3" border="0">
			<tr>
				<td width="20%" align="left" class="td">
					Language:
				</td>
				<td width="1">&nbsp;</td>
				<td width="80%" align="left">
						<?php
							// Let's get all the possible language files
							$lang_dir = $include_path. "install/lang/";
							$retArray = readDirInfo($lang_dir,"dir");

							sort($retArray);
							$languages = array();

							for ($c=0; $c < count($retArray); $c++){
								$entry = $retArray[$c];
								// Let's make sure this isn't the local directory we're looking at
								if ($entry == "." || $entry == ".." || $entry == "master.php") { continue;}
								if (!stristr($entry,"-setup") and !stristr($entry,".html")){
									if (strrpos($entry,'-') !== false) {
										$languages[substr($entry,0,strrpos($entry,'-'))] = true;
									} else {
										$languages[$entry] = true;
									}
								}
							}

							$languages = array_keys($languages);

							echo '<select name="jz_lang_file">';
							foreach ($languages as $entry) {
								echo '<option ';
								if ($entry == "english"){echo 'selected'; }
								echo ' value="'. $entry. '">'. $entry. '</option>'. "\n";
							}
						?>
					</select>
				</td>
			</tr>
		</table>
		<br>
		<div class="go">
			<span class="goToNext">
				&nbsp; <input type="submit" name="submit_step2" value="Proceed to Requirements >>" class="submit">
			</span>
		</div>
	</form>
	</div>
<?php
	// Now let's include the footer
	include_once($include_path. 'install/footer.php');
?>