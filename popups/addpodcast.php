<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');

/**
* Displays the Add Podcast Subscribe tool
*
* @author Ross Carlson
* @since 11/02/05
* @version 11/02/05
* @param $node The node that we are viewing
**/
global $include_path, $podcast_folder;

// Let's start the page header
$this->displayPageTop("", word("Subscribe to Podcast"));
$this->openBlock();

$display = new jzDisplay();
$be = new jzBackend();

// Did they want to update a podcast
if (isset ($_GET['feed_path'])) {
	if ($_GET['sub_action'] == "update") {
		$_POST['edit_podcast_add'] = "TRUE";
		$_POST['edit_podcast_path'] = $_GET['feed_path'];
		$_POST['edit_podcast_title'] = $_GET['feed_title'];
		$_POST['edit_podcast_url'] = $_GET['feed_url'];
		$_POST['edit_podcast_max'] = $_GET['feed_number'];
	}
}

// Did the subscribe?
if (isset ($_POST['edit_podcast_add'])) {
	// Let's track this podcast
	$pData = $be->loadData("podcast");
	$i = count($pData) + 1;
	$pArr[$i]['title'] = $_POST['edit_podcast_title'];
	$pArr[$i]['url'] = $_POST['edit_podcast_url'];
	$pArr[$i]['path'] = $_POST['edit_podcast_path'];
	$pArr[$i]['number'] = $_POST['edit_podcast_max'];

	if (is_array($pData)) {
		$add = true;
		foreach ($pData as $data) {
			if ($data['title'] == $_POST['edit_podcast_title']) {
				$add = false;
			}
		}
		if ($add) {
			$nArr = array_merge($pData, $pArr);
			$be->storeData("podcast", $nArr);
		}
	} else {
		$be->storeData("podcast", $pArr);
	}

	// Now let's get the data
	$retArray = parsePodcastXML($_POST['edit_podcast_url']);

	$title = $retArray['title'];
	unset ($retArray['title']);
	$desc = $retArray['desc'];
	unset ($retArray['desc']);
	$desc = str_replace("]]>", "", str_replace("<![CDATA[", "", $desc));
	$pubDate = $retArray['pubDate'];
	unset ($retArray['pubDate']);
	$image = $retArray['image'];
	unset ($retArray['image']);

	// Now let's import
	echo '<div id="track"></div>';
	echo '<div id="pbar"></div>';
?>
			<script language="javascript">
				t = document.getElementById("track");
				p = document.getElementById("pbar");
				p.innerHTML = '<?php echo "<br><br><center><img src=${include_path}style/images/progress-bar.gif><br>". word("Please wait"). "...</center>"; ?>';									
				-->
			</SCRIPT>
			<?php


	if (stristr($image, "http://")) {
		if (substr($podcast_folder, 0, 1) <> "/") {
			$dir = str_replace("\\", "/", getcwd()) . "/" . $podcast_folder . "/" . $title;
		} else {
			$dir = $podcast_folder . "/" . $title;
		}
		// Now let's create the directory we need
		makedir($dir);
		$imgFile = $dir . "/" . $title . ".jpg";
		$iData = file_get_contents($image);
		$handle = fopen($imgFile, "w");
		fwrite($handle, $iData);
		fclose($handle);
	}

	// Now let's create the node in the backend and assign it some values
	$newNode = new jzMediaNode($node->getPath("string") . "/" . $_POST['edit_podcast_path']);
	$newNode->addDescription($desc);
	$newNode->addMainArt($imgFile);

	// Now let's loop and look at each enclosure		
	$i = 1;
	foreach ($retArray as $item) {
		// Let's grab it
		$track = getPodcastData($item, $title);

		if (stristr($track, ".mp3")) {
			// Now that we've got the link we need to add it to the backend
			$ext = substr($item['file'], strlen($item['file']) - 3, 3);
			$nTrack = trim(cleanFileName($item['title'] . "." . $ext));

			$pArr = explode("/", $_POST['edit_podcast_path']);
			$path = array ();
			foreach ($pArr as $p) {
				$path[] = $p;
			}
			$path[] = $nTrack;
			$tr = $node->inject($path, $track);
			if ($tr !== false) {
				$meta = $tr->getMeta();
				$meta['title'] = $item['title'];
				$tr->setMeta($meta);
			}
		}

		// Now should we stop?
		if ($_POST['edit_podcast_max'] <> "ALL" and $_POST['edit_podcast_max'] <> "") {
			if ($_POST['edit_podcast_max'] == $i) {
				break;
			}
		}
		$i++;
	}
?>
			<script language="javascript">
				p.innerHTML = '&nbsp;';									
				t.innerHTML = '<br><center><?php echo word("Updates Complete!"); ?></center>';									
				-->
			</SCRIPT>
			<?php

}

$arr = array ();
$arr['action'] = "popup";
$arr['ptype'] = "addpodcast";
$arr['sub_action'] = "addmediadir";
$arr['jz_path'] = $node->getPath('String');
?>
		<form action="<?php echo urlize($arr); ?>" method="POST" name="setup8">
			<table>
				<tr>
					<td>
						Title:
					</td>
					<td>
						<input type="text" name="edit_podcast_title" class="jz_input" size="40">
					</td>
				</tr>
				<tr>
					<td>
						URL:
					</td>
					<td>
						<input type="text" name="edit_podcast_url" class="jz_input" size="40">
					</td>
				</tr>
				<tr>
					<td>
						New Path:
					</td>
					<td>
						<input type="text" name="edit_podcast_path" value="New Podcast" class="jz_input" size="40">
					</td>
				</tr>
				<tr>
					<td>
						Max Tracks:
					</td>
					<td>
						<select name="edit_podcast_max" class="jz_select">
							<option value="ALL">All</option>
							<option value="1">1</option>
							<option value="5">5</option>
							<option value="10">10</option>
							<option value="25">25</option>
						</select>
					</td>
				</tr>
			</table>
			<br>
			<center>
				<input type="submit" name="edit_podcast_add" value="<?php echo word("Subscribe"); ?>" class="jz_submit">
			</center>
		</form>		
		<br><br>
		<strong><?php echo word("Managing Existing Podcasts"); ?></strong><br>
		<?php

$pData = $be->loadData("podcast");
if (is_array($pData)) {
?>
				<table>
					<tr>
						<td>
							<strong><?php echo word("Title"); ?></strong>
						</td>
						<td>
							<strong><?php echo word("Location"); ?></strong>
						</td>
						<td>
							<strong><?php echo word("Function"); ?></strong>
						</td>
					</tr>
					<?php

	foreach ($pData as $data) {
		echo '<tr><td>';
		echo $data['title'] . " &nbsp; ";
		echo '</td><td>';
		echo $data['path'] . " &nbsp; ";
		echo '</td><td>';

		$arr['action'] = "popup";
		$arr['ptype'] = "addpodcast";
		$arr['feed_path'] = $data['path'];
		$arr['feed_title'] = $data['title'];
		$arr['feed_url'] = $data['url'];
		$arr['feed_number'] = $data['number'];
		$arr['jz_path'] = $node->getPath('String');

		$arr['sub_action'] = "update";
		echo '<a href="' . urlize($arr) . '">' . word("Update") . '</a>';
		/*
		echo " | ";
		
		$arr['sub_action'] = "delete";
		echo '<a href="'. urlize($arr). '">'. word("Delete"). '</a>';
		*/
		echo '</td></tr>';
		unset ($arr);
	}
?>
				</table>
			<?php

} else {
	echo " - " . word("None exist");
}
$this->closeBlock();
?>
