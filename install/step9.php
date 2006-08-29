<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	// Ok, let's kill the lock file
	@unlink($include_path. "temp/install.lock");
	
	// Ok, they got to step 9, so let's track it
	$url = "http://www.jinzora.com/jinzora-installer-finished.php";
	$url_parsed = parse_url($url);
	$host = $url_parsed["host"];
	$port = 80;
	$path = $url_parsed["path"];
	$out = "GET $path HTTP/1.0\r\nHost: $host\r\n\r\n";
	if ($fp = @fsockopen($host, $port, $errno, $errstr, 30)){
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
	}
	
	$root_page = setThisPage();
	$form_action = setThisPage() . "install=step9";
		
	// Now did they want to proceed?
	if (isset($_POST['share_stats'])){
		// Ok, did they want to share stats?
		if ($_POST['stats'] == "true"){
			// Now let's get the data
			$backend = $_POST['backend'];
			$hierarchy = $_POST['hierarchy'];
			$frontent =  $_POST['hierarchy'];
			$default_access = $_POST['default_access'];
			$username = $_POST['your_name'];
			$site = $_POST['your_url'];
			$comments = $_POST['comments'];
			$media_dir_count = sizeof(explode("|",$_SESSION['all_media_paths'])) - 1;
			$mcms_type = $_POST['cms_type'];
			
			// Now let's get the backend data
			require_once($include_path. 'backend/backend.php');
			$root_node = &new jzMediaNode();
			
			if (distanceTo('genre') !== false)
				$genres = $root_node->getSubNodeCount('nodes',distanceTo('genre'));
			if (distanceTo('artist') !== false)
				$artists = $root_node->getSubNodeCount('nodes',distanceTo('artist'));
			if (distanceTo('album') !== false)
				$albums = $root_node->getSubNodeCount('nodes',distanceTo('album'));
			if (distanceTo('track') !== false)
				$disks = $root_node->getSubNodeCount('nodes',distanceTo('track'));
			$tracks = $root_node->getSubNodeCount('tracks',-1);
				
			$length = "";
			
			$sql_type = isset($sql_type) ? $sql_type : "false";
			$genres = isset($genres) ? $genres : "false";
			$artists = isset($artists) ? $artists : "false";
			$albums = isset($albums) ? $albums : "false";
			$tracks = isset($tracks) ? $tracks : "false";
			$disks = isset($disks) ? $disks : "false";
			
			$user_agent = "";
			if (isset($_SERVER['HTTP_USER_AGENT'])){
				$user_agent = $_SERVER['HTTP_USER_AGENT'];
			}
			$webserver = "";
			if (isset($_SERVER['SERVER_SOFTWARE'])){
				$webserver = $_SERVER['SERVER_SOFTWARE'];
			}			
			$phpVersion = phpversion();
			
			// Now let's get the build
			include_once($include_path. 'system.php');
			
			// Now let's connect to the stats page
			$host = "www.jinzora.com";
			$path = "useragent=". urlencode($user_agent). 
					"&webserver=". urlencode($webserver). 
					"&phpversion=". urlencode($phpVersion). 
					"&hierarchy=" . urlencode(implode("/",$hierarchy)) . 
					"&backend=" . urlencode($backend) . 
					"&dbtype=" . urlencode($sql_type) . 
					"&genres=${genres}".
					"&artists=${artists}".
					"&albums=${albums}".
					"&tracks=${tracks}".
					"&disks=${disks}".
					"&length=${length}".
					"&frontend=${frontend}".
					"&default_access=${default_access}".
					"&username=". urlencode($username). 
					"&site=". urlencode($site). 
					"&comments=". urlencode($comments). 
					"&media_dir=". urlencode($media_dir_count). 
					"&build=". urlencode($version).
					"&installtype=". urlencode($mcms_type).
					"&jukebox=". urlencode($_POST['jukebox']);
		
			$url = "http://www.jinzora.com/jinzora-stats.php?". $path;		
			
			$url_parsed = parse_url($url);
			$host = $url_parsed["host"];
			$port = $url_parsed["port"];
			if ($port==0)
			   $port = 80;
			$path = $url_parsed["path"];
			if ($url_parsed["query"] != "")
			   $path .= "?".$url_parsed["query"];
			$out = "GET $path HTTP/1.0\r\nHost: $host\r\n\r\n";
			@$fp = fsockopen($host, $port, $errno, $errstr, 30);
			@fwrite($fp, $out);			
			@fclose($fp);

			header("Location: ". $root_page);
			exit();
		}
	}

	if (isset($_POST['share'])){
		if ($_POST['stats'] == "true"){
			// Now let's include the left
			include_once($include_path. 'install/leftnav.php');
			?>
			<div id="main">
			<h1><?php echo "Share Stats"; ?></h1>
			<p>
			<?php echo "Thank you for sharing anonymous information with us.  Is there anything else you'd like to share with our development team?"; ?>
			<div class="go">
				<span class="goToNext">
					<?php echo "Share Stats"; ?>
				</span>
			</div>
			<form action="<?php echo $form_action; ?>" name="setup" method="post">
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
						<td class="td" width="25%" align="left">
							<?php echo $word_your_name; ?>
						</td>
						<td class="td" width="1">&nbsp;</td>
						<td class="td" width="75%" align="left">
							<input type="text" name="your_name" value="anonymous">
						</td>
					</tr>
					<tr>
						<td class="td" width="25%" align="left">
							<?php echo $word_your_site; ?>
						</td>
						<td class="td" width="1">&nbsp;</td>
						<td class="td" width="75%" align="left">
							<?php 
								if (isset($_SERVER['HTTP_HOST'])){
									$host = $_SERVER['HTTP_HOST'];
								} else {
									$host = "";
								}
							?>
							<input type="text" name="your_url" value="anonymous">
						</td>
					</tr>
					<tr>
						<td class="td" width="25%" align="left" valign="top">
							<?php echo $word_comments; ?>
						</td>
						<td class="td" width="1">&nbsp;</td>
						<td class="td" width="75%" align="left">
							<textarea cols="42" rows="10" name="comments"></textarea>
						</td>
					</tr>
					<tr>
						<td class="td" width="25%" align="left" valign="top">
						</td>
						<td class="td" width="1">&nbsp;</td>
						<td class="td" width="75%" align="left">
							<input type="submit" name="share_stats" value="<?php echo $word_launch; ?>" class="submit">
						</td>
					</tr>
				</table>
			</form>
			</div>
			<?php
			include_once($include_path. 'install/footer.php');
			exit();
		} else {
			// Now let's redirect
			header("Location: ". $root_page);
		}
		exit();
	}

	// Now let's include the left
	include_once($include_path. 'install/leftnav.php');
?>
<div id="main">
	<a href="http://www.jinzora.com" target="_blank"><img src="<?php echo $include_path; ?>install/logo.gif" border="0" align="right" vspace="5" hspace="0"></a>
	<h1><?php echo $word_launch_jinzora; ?></h1>
	<p>
	<?php echo $word_launch_jinzora_note; ?>
	<div class="go">
		<span class="goToNext">
			<?php echo $word_resources; ?>
		</span>
	</div>
	<a target="_blank" href="http://www.jinzora.com/donate.htm"><?php echo $word_donations; ?></a>	
	<br>
	<a target="_blank" href="http://www.jinzora.com"><?php echo $word_website; ?></a>
	<br>
	<a target="_blank" href="http://www.jinzora.com/forums"><?php echo $word_forums; ?></a>
	<br>
	<a target="_blank" href="http://www.jinzora.com/docs"><?php echo $word_docs; ?></a>	
	<div class="go">
		<span class="goToNext">
			<?php echo $word_usage_stats; ?>
		</span>
	</div>
	<?php
		// Now let's see if they posted data for the anonymous stats
		echo '<form action="'. $form_action. '" name="setup" method="post">';
		$PostArray = $_POST;
		foreach ($PostArray as $key => $val) {
		  if (!stristr($key,"submit")){
			echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
		  }
		}
		echo $word_anon_stat_note;
		echo '<br>'.
			'<br><input checked onclick="javascript:document.setup.share.value=\''. $word_share_stats. '\';" class="jz_radio" type="radio" name="stats" value="true"> '. $word_share_stats. 
			' <input  onclick="javascript:document.setup.share.value=\''. $word_launch_jinzora. '\';"  class="jz_radio" type="radio" name="stats" value="false"> '. $word_no_thanks. '<br>'.
			'<div class="go">'.
				'<span class="goToNext">'.
					'&nbsp; '.
					'<input type="submit" name="share" value="'. $word_share_stats. '" class="submit">'.
				'</span>'.
			'</div>';
		echo '</form>';
	?>
	</div>
<?php
	// Now let's include the top
	include_once($include_path. 'install/footer.php');
?>
