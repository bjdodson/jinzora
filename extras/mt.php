<?php 
	define('JZ_SECURE_ACCESS','true');
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
	* - Feeds data to Movable Type
	* -- COMPLETELY UNSUPPORTED!!!!   -   Use at your own risk!
	*
	* @since 10.19.04
	* @author Ross Carlson <ross@jinzora.org>
	*
	*/
	
	// Now let's include what we'll need for settings
	include_once(dirname(__FILE__).'/../settings.php');
	include_once($web_root. $root_dir. '/lang/english.php');
	include_once($web_root. $root_dir. '/system.php');
	
	$user = $_GET['user'];
	$numItems = $_GET['numItems'];
	$img_width = $_GET['img_width'];
	$resample = $_GET['resample'];
	$allow_plays = $_GET['allow_plays'];
	$truncate = $_GET['truncate'];
	
	// Now let's read in the played file of this user
	$dataArray = file($web_root. $root_dir. "/data/users/". $user. ".played");
	// Ok, now let's parse out what we read
	for ($c=0; $c < count($dataArray); $c++){
		// Now let's split out the time from the path
		if (stristr($dataArray[$c],"|")){
			$valArray = explode("|",$dataArray[$c]);
			$retArray[$c]['path'] = $valArray[0];
			$retArray[$c]['time'] = $valArray[1];
		}
	}
	
	// Now let's look at the entries here and see if this 
	$finalData = "";
	for ($c=0; $c < count($retArray); $c++){
		$pathArray = explode("/",$retArray[$c]['path']);
		unset($pathArray[count($pathArray)-1]);
		$path = implode("/",$pathArray);
		// Ok, now let's see if we have that already
		if (!stristr($finalData, $path)){
			$finalData .= $path. "|". $retArray[$c]['time']. "\n";
		}
	}
	
	// Ok, now we have the items lets display them
	$finalArray = explode("\n",$finalData);
	echo '<h2>Now Playing</h2>';
	$nowArray = explode("|",$finalArray[0]);
	$nowVal = explode("/",$nowArray[0]);
	
	// Now let's see if there is art for this item
	$dirName = $web_root. $root_dir. $media_dir. "/". $nowArray[0];
	$d = dir($dirName);
	$img = "";
	while($entry = $d->read()) {
		// Let's make sure this isn't the local directory we're looking at
		if (preg_match("/\.($ext_graphic)$/i", $entry)) {
			$img = $entry;
		}		
	}
	$d->close();
	
	// Now let's echo out the item name
	$item = $nowVal[count($nowVal)-1];
	if (strlen($item) > $truncate){
		$item = substr($item,0,$truncate). "...";
	}
	if ($allow_plays == "true"){
		$link = $root_dir. "/playlists.php?d=1&r=". $resample. "&style=normal&info=". base64_encode(str_replace("%2F","/",urlencode($nowArray[0])));
		echo '<A title="Play '. str_replace("/"," - ",$nowArray[0]). '" href="'. $link. '">'. $item. "</a><br>";
	} else {
		echo $item. "<br>";
	}
	
	// Now if we have an image let's display it
	if ($img <> ""){
		if ($allow_plays == "true"){
			$link = $root_dir. "/playlists.php?d=1&r=". $resample. "&style=normal&info=". base64_encode(str_replace("%2F","/",urlencode($nowArray[0])));
		} else {
			$link = 'javascript:void(0)';
			//echo '<img title="'. str_replace("/"," - ",$nowArray[0]). '" width="'. $img_width. '" src="'. $this_site. $root_dir. $media_dir. "/". $nowArray[0]. "/". $img. '" border="0"><br>';
		}
		echo '<A href="'. $link. '" title="'. str_replace("/"," - ",$nowArray[0]). '" width="'. $img_width. '"><img title="'. str_replace("/"," - ",$nowArray[0]). '" width="'. $img_width. '" src="'. $this_site. $root_dir. $media_dir. "/". $nowArray[0]. "/". $img. '" border="0"></a><br>';
	}
	echo "<br>";
	// Now let's display the previously played items
	if (count($finalArray) > $numItems){
		$i=0;
		echo "<h2>Previously Played</h2>";
		for ($c=1; $c < count($finalArray); $c++){
			$nowArray = explode("|",$finalArray[$c]);
			$nowVal = explode("/",$nowArray[0]);
			if ($nowVal[count($nowVal)-1] <> ""){
				$item = $nowVal[count($nowVal)-1];
				if (strlen($item) > $truncate){
					$item = substr($item,0,$truncate). "...";
				}
				if ($allow_plays == "true"){
					$link = $root_dir. "/playlists.php?d=1&r=". $resample. "&style=normal&info=". base64_encode(str_replace("%2F","/",urlencode($nowArray[0])));
					$title = 'Play '. str_replace("/"," - ",$nowArray[0]);
				} else {
					$link = 'javascript:void(0)';	
					$title = str_replace("/"," - ",$nowArray[0]);
					//echo $item. "<br>";
				}
				echo '<A title="'. $title. '" href="'. $link. '">'. $item. "</a><br>";
				$i++;
				if ($i > $numItems-1){ break; }
			}
		}
	}
	echo ' - <A href="http://www.jinzora.org">Powered by Jinzora</a> - <br>';
?>