<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');


/**
* Displays the page to allow the user to select the type of transcoded download they want
*
* @author Ross Carlson
* @since 06/10/05
* @version 06/10/05
* @param $node object The node we are viewing
*
**/
global $cache_resampled_tracks, $jzSERVICES, $resample_cache_size, $status_blocks_refresh, $allow_resample, $root_dir, $include_path;

// Let's not time out...
set_time_limit(0);

// Can they resample??
if ($allow_resample == "false") {
	echo '<body onLoad="document.dlForm.submit();"></body>';
}

$this->displayPageTop("", word("Downloading"));
$this->openBlock();

// Let's get the meta data from the track or first track in this album
// First we need to create a track object from the node
if ($node->getPType() == "track") {
	$track = new jzMediaTrack($node->getPath('String'));
} else {
	// Now we need to grab the first track as a sample
	$tracks = $node->getSubNodes("tracks", -1);
	$track = $tracks[0];
}
// Now let's pull the meta data
$meta = $track->getMeta();

// Ok, now based on the input file let's create the beginning of the command
$extArr = explode(".", $node->getPath('String'));
$ext = $extArr[count($extArr) - 1];

// Did they submit the form?
if (isset ($_POST['edit_dlformat'])) {
	// Ok, first we need to get ALL the files in the cache dir
	// And see how big it all is to see if we need to do some cleanup first
	$retArray = readDirInfo($include_path . "data/resampled", "file");
	$size = 0;
	foreach ($retArray as $track) {
		// Let's get the total size first
		$size = $size +round(filesize($include_path . "data/resampled/" . $track) / 1024000);
		flushdisplay();
	}
	// Now are we too big?
	if ($size > $resample_cache_size) {
		// Ok, we'll have to loop through and delete until we get small enough
		foreach ($retArray as $track) {
			$size = $size -round(filesize($include_path . "data/resampled/" . $track) / 1024000);
			@ unlink($include_path . "data/resampled/" . $track);
			flushdisplay();
			if ($size < $resample_cache_size) {
				break;
			}
		}
	}

	echo word("Beginning download") . "...<br><br>";
	flushdisplay();

	// First we need to know if this was a single file or an album
	if ($node->getPType() <> "track") {
		// Ok, we need to create ALL the tracks for this album
		// Let's setup our display
		echo '<div id="status"></div>';
		echo '<br><br>';
		echo '<div id="status2"></div>';
?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					s = document.getElementById("status");
					s2 = document.getElementById("status2");
					s2.innerHTML = '<?php echo '<center><img src="'. $root_dir. '/style/images/progress-bar.gif" border="0"><br>'. word("Please Wait"). '</center>'; ?>';					
					-->
				</SCRIPT>
				<?php

		flushdisplay();

		// Now, are they transcoding or are they downloading the native version
		if ($_POST['edit_dlformat'] <> "native" or $_POST['edit_dlbitrate'] <> "native") {

			// Now let's get all the tracks
			$tracks = $node->getSubNodes("tracks", -1);
			$fileArray = array ();
			for ($i = 0; $i < count($tracks); $i++) {
?>
						<SCRIPT LANGUAGE=JAVASCRIPT><!--\
							s.innerHTML = '<nobr><?php echo word("Transcoding"). ": ". $tracks[$i]->getName(); ?></nobr>';					
							-->
						</SCRIPT>
						<?php

				flushdisplay();

				// Now let's set the format
				$dl_format = $_POST['edit_dlformat'];
				if ($dl_format == "native") {
					$dl_format = substr($tracks[$i]->getDataPath("string"), -3);
				}

				// Now let's transcode this track for them
				$meta = $tracks[$i]->getMeta();
				$fileArray[] = $jzSERVICES->createResampledTrack($tracks[$i]->getDataPath("string"), $dl_format, $_POST['edit_dlbitrate'], $meta);
				unset ($meta);
			}
			// Now let's clean up
?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						s.innerHTML = '&nbsp;';			
						s2.innerHTML = '&nbsp;';					
						-->
					</SCRIPT>
					<?php

			flushdisplay();

			// Now we need to put the refreshing back to normal
			$url = array ();
			$url['action'] = "nowstreaming";
			$url['refresh_int'] = $status_blocks_refresh;

			// Now we need to send this bundled file
			$dlarr = array ();
			$dlarr['action'] = "popup";
			$dlarr['ptype'] = "downloadtranscodedbundle";
			$dlarr['jz_files'] = serialize($fileArray);
			$dlarr['jz_path'] = $node->getPath("string");

			$var = word("If your download doesn't begin click") . " ";
			$var .= '<a href="' . urlize($dlarr) . '">' . word('here') . '.</a>';
			echo '<META HTTP-EQUIV=Refresh CONTENT="1; URL=' . urlize($dlarr) . '">';
?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						s.innerHTML = '<?php echo $var; ?>';			
						s2.innerHTML = '&nbsp;';					
						-->
					</SCRIPT>
					<?php

			flushdisplay();

			echo "<br><br><br><br><center>";
			$this->closeButton();
			$this->closeBlock();
		} else {

			// Ok, they want the native file format
			$tracks = $node->getSubNodes("tracks", -1);
			$fileArray = array ();
			for ($i = 0; $i < count($tracks); $i++) {
				$fileArray[] = $tracks[$i]->getDataPath("string");
			}

			// Now we need to send this bundled file
			$dlarr = array ();
			$dlarr['action'] = "popup";
			$dlarr['ptype'] = "downloadtranscodedbundle";
			$dlarr['jz_files'] = serialize($fileArray);
			$dlarr['jz_path'] = $node->getPath("string");

			$var = word("If your download does not begin click") . " ";
			$var .= '<a href="' . urlize($dlarr) . '">HERE</a>';
			echo '<META HTTP-EQUIV=Refresh CONTENT="1; URL=' . urlize($dlarr) . '">';
?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						s.innerHTML = '<?php echo $var; ?>';			
						s2.innerHTML = '&nbsp;';					
						-->
					</SCRIPT>
					<?php

			flushdisplay();
			echo "<center>";
			$this->closeButton();
			$this->closeBlock();
		}

		exit ();
	} else {
		// Let's create the resampled track IF we need to
		if ((($_POST['edit_dlformat'] <> $ext) or ($_POST['edit_dlbitrate'] <> $meta['bitrate'])) and $_POST['edit_dlformat'] <> "native") {
			echo word("Transcoding tracks, please stand by") . "...<br><br><br>";
			flushdisplay();
			$filename = $jzSERVICES->createResampledTrack($_POST['edit_dl_filename'], $_POST['edit_dlformat'], $_POST['edit_dlbitrate'], $meta);
		} else {
			// Ok, use the standard filename
			$filename = $node->getDataPath('String');
		}
	}

	// Ok, now we need to send them the file
	$fileArray[] = $filename;
	$dlarr = array ();
	$dlarr['action'] = "popup";
	$dlarr['ptype'] = "downloadtranscodedbundle";
	$dlarr['jz_files'] = serialize($fileArray);
	$dlarr['jz_path'] = $filename;

	echo word("If your download doesn't begin click") . " ";
	echo '<a href="' . urlize($dlarr) . '">HERE</a>';
	echo '<META HTTP-EQUIV=Refresh CONTENT="1; URL=' . urlize($dlarr) . '">';

	echo "<br><br><br><br><center>";
	$this->closeButton();
	$this->closeBlock();

	exit ();
}

// Now we need to set the refresh time on the "Now Streaming" and "Who is where" blocks VERY
// high - if they refresh while transcoding it'll kill the transcoding
// Let's setup the link for the ifram
$url = array ();
$url['action'] = "nowstreaming";
$url['refresh_int'] = "0";
?>
		<script type="text/javascript">
		<!--
		window.opener.document.getElementById("NowStreamingFrame").src = "<?php echo urlize($url); ?>";
		window.opener.document.getElementById("WhoIsWhereFrame").src = "<?php echo urlize($url); ?>";
		-->
		</script>
		<?php


// Let's setup the form for the user to choose from
$arr = array ();
$arr['action'] = "popup";
$arr['ptype'] = "downloadtranscode";
$arr['jz_path'] = $node->getPath('String');
?>
		<form action="<?php echo urlize($arr); ?>" method="POST" name="dlForm">
			<input type="hidden" name="edit_dl_filename" value="<?php echo $node->getDataPath('String'); ?>">
			<?php

// Can they resample?
if ($allow_resample == "true") {
?>
					<strong>
					<?php

	echo word("Original File");
	if ($node->getPType() <> "track") {
		echo " (" . word("Sample") . ")";
	}
?>
					</strong>
					<table width="100%" cellpadding="2">
						<tr>
							<td width="25%" nowrap>
								<?php echo word("Format"); ?>:
							</td>
							<td width="75%" nowrap>
								<?php echo ucwords($meta['type']); ?>
							</td>
						</tr>
						<tr>
							<td width="25%" nowrap>
								<?php echo word("Bitrate"); ?>:
							</td>
							<td width="75%" nowrap>
								<?php echo $meta['bitrate']; ?> Kbps
							</td>
						</tr>
						<tr>
							<td width="25%" nowrap>
								<?php echo word("Frequency"); ?>:
							</td>
							<td width="75%" nowrap>
								<?php echo $meta['frequency']; ?> Khz
							</td>
						</tr>
						<tr>
							<td width="25%" nowrap>
								<?php echo word("Size"); ?>:
							</td>
							<td width="75%" nowrap>
								<?php echo $meta['size']; ?> MB
							</td>
						</tr>
					</table>
					<br>
					<strong><?php echo word("Download Format"); ?></strong>
					<table width="100%" cellpadding="2">
						<tr>
							<td width="25%" nowrap>
								<?php echo word("Format"); ?>:
							</td>
							<td width="75%" nowrap>
								<select name="edit_dlformat" class="jz_select" style="width: 100px;">
									<option value="native">Native</option>
									<option value="mp3">MP3</option>
									<option value="wav">WAV</option>
									<option value="flac">Flac</option>
									<option value="mpc">Musepack</option>
									<option value="wv">Wavpack</option>
									<!--<option value="ogg">OGG</option>-->
								</select>	
							</td>
						</tr>
						<tr>
							<td width="25%" nowrap>
								<?php echo word("Quality"); ?>:
							</td>
							<td width="75%" nowrap>
								<select name="edit_dlbitrate" class="jz_select" style="width: 100px;">
									<option selected value="native">Native</option>
									<option value="32"><?php echo word("Spoken word"); ?> (32kbps)</option>
									<option value="64"><?php echo word("Low quality"); ?> (64kbps)</option>
									<option value="96"><?php echo word("Medium quality"); ?> (96kbps)</option>
									<option value="128"><?php echo word("Low quality"); ?> (128kbps)</option>
									<option value="192"><?php echo word("Good quality"); ?> (192kbps)</option>
									<option value="320"><?php echo word("Highest quality"); ?> (320kbps)</option>
								</select>
							</td>
						</tr>
					</table>
					<br><br>
					<input type="submit" name="edit_download_tc_file" value="<?php echo word("Download"); ?>" class="jz_submit">
					<?php

} else {
	echo '<input type="hidden" name="edit_dlformat" value="native">';
	echo '<input type="hidden" name="edit_dlbitrate" value="native">';
	echo '<body onLoad="document.dlForm.submit();"></body>';
}
?>			
		</form>
		<?php


$this->closeBlock();
exit ();
?>