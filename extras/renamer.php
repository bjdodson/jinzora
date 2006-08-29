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
	* - Renaming Functions for a media library
	* -- COMPLETELY UNSUPPORTED!!!!   -   Use at your own risk!
	*
	* @since 10.04.04
	* @author Ross Carlson <ross@jinzora.org>
	*
	*/
	
	// first let's include all the functions and stuff we'll need
	echo '<link rel="stylesheet" href="../style/sandstone/default.css" type="text/css">';
	include_once('../lib/general.lib.php');
	
	// Let's set up some variables
	$srcDir = "C:/Documents and Settings/rcarlson/My Documents/My Music/temp";
	$destDir = "C:/Documents and Settings/rcarlson/My Documents/My Music";
	$badArray = explode(";",';?;";/;\;|;*;<;>');	
	
	// Let's make it look a little pretty
	//echo $css;
	
	// Now let's see if they pressed any buttons
	if (isset($_POST['stripAlbum']) or isset($_POST['stripAlbumTest']) or isset($_POST['stripArtistTest']) or isset($_POST['stripArtist']) or isset($_POST['stripAllTest']) or isset($_POST['stripAll'])){
		stripName();
	}
	
	// Did they want to rename from that music service?
	if (isset($_POST['rename'])){		
		// Ok, first let's clean up the list of new track names
		$tArray = explode("\n",$_POST['tracks']);
		$c=0;
		for ($i=0;$i<count($tArray);$i++){
			// Now let's blow this out
			if (strlen($tArray[$i]) > 3){
				// Now let's get the number
				$dArray = explode(". ",trim($tArray[$i]));
				// Now let's fix the number to make it 2 digits
				$tNum = $dArray[0];
				if ($tNum < 10){ $tNum = "0". $tNum; }
				// Now let's return
				$trackArray[$c]['num'] = $tNum;
				// Now let's clean up the track name
				$trackName =  stripslashes($dArray[1]);
				for ($e=0;$e<count($badArray);$e++){
					$trackName = str_replace($badArray[$e],"",$trackName);
				}
				$trackArray[$c]['track'] = $trackName;
				$c++;
			}
		}
		
		// Now let's get all the tracks that are in our directory
		$d = dir($srcDir);
		while($entry = $d->read()) {
			// Let's make sure this isn't the local directory we're looking at
			if ($entry == "." || $entry == "..") { continue;}
			// Now let's create an arry with the times and names so we can sort it
			$vArray[] = filemtime($srcDir. "/". $entry). "|". $entry;
		}
		$d->close();
		
		// Now let's sort that array
		sort($vArray);
		
		// First let's clean up the directory names
		$artist = $_POST['artist'];
		$album = $_POST['album'];
		for ($i=0;$i<count($badArray);$i++){
			$artist = str_replace($badArray[$i],"",$artist);
			$album = str_replace($badArray[$i],"",$album);
		}
				
		// Now let's create the new folders IF we need to
		$destArray = explode("/",$destDir. "/". $_POST['artist']. "/". $_POST['album']);
		$dir = "";
		for ($i=0;$i<count($destArray);$i++){
			$dir .= $destArray[$i]. "/";
			// Now let's see if that exists
			if (!is_dir($dir)){
				mkdir($dir);
			} 
		}

		// Now let's compare the two and rename
		for ($i=0;$i<count($vArray);$i++){
			$nArray = explode("|",$vArray[$i]);
			echo $nArray[1]. " &nbsp; -->> &nbsp; ". $trackArray[$i]['num']. " - ". $trackArray[$i]['track']. ' - ';
			// Now let's rename
			if (rename($srcDir. "/". $nArray[1], $destDir. "/". $artist. "/". $album. "/". $trackArray[$i]['num']. " - ". $trackArray[$i]['track']. ".mp3")){
				echo '<font color="green">Success!</font>';
			} else {
				echo '<font color="red">Failed!</font>';
			}
			echo '<br>';
		}
		echo '<br>';
		echo 'Renaming complete, please wait...';
		flushdisplay();
		sleep(2);
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT"><!--\
			history.back();
		-->
		</SCRIPT>
		<?php
		exit();
	}
	
	// Now let's show them what they can do
	echo '<center><form action="renamer.php" method="post" name="toolsForm">'. "\n";
	echo '<input type="submit" name="stripAlbumTest" value="Strip Album Names (Test Only)" class="jz_submit">'. "\n";
	echo '<input type="submit" name="stripAlbum" value="Strip Album Names" class="jz_submit">'. "\n";	
	echo '<br>Stips the name of the ablum from the file name IF found<br>'. "\n";
	
	echo '<br><input type="submit" name="stripArtistTest" value="Strip Artist Names (Test Only)" class="jz_submit">'. "\n";
	echo '<input type="submit" name="stripArtist" value="Strip Artist Names" class="jz_submit">'. "\n";	
	echo '<br>Stips the name of the artist from the file name IF found<br>'. "\n";
	
	echo '<br><input type="submit" name="stripAllTest" value="Strip Artist & Album Names (Test Only)" class="jz_submit">'. "\n";
	echo '<input type="submit" name="stripAll" value="Strip Artist & Album Names" class="jz_submit">'. "\n";	
	echo '<br>Stips the name of the artist from the file name IF found<br>'. "\n";
	
	echo '<br>Root dir to start search from<br>';
	// Now let's get all the possibles
	$retArray = readDirInfo($web_root. $root_dir. $media_dir,"dir");
	sort($retArray);
	echo '<select name="rootDir" class="jz_select">';
	echo '<option value="">All Directories</option>';
	for ($i=0;$i<count($retArray);$i++){
		echo '<option value="'. $retArray[$i]. '">'. $retArray[$i]. '</option>';
	}
	echo '</select>';
	
	// Now let's setup the form for renaming from that music service
	echo '<br><br><br>Artist<br><input type="text" class="jz_input" name="artist" size="30">';
	echo '<br><br>Album<br><input type="text" class="jz_input" name="album" size="30">';
	echo '<br><br>Tracks<br><textarea class="jz_input" name="tracks" cols="60" rows="12"></textarea>';
	
	// Now let's see how many files are here
	$d = dir($srcDir);
	$c=0;
	while($entry = $d->read()) {
		// Let's make sure this isn't the local directory we're looking at
		if ($entry == "." || $entry == "..") { continue;}
		// Now let's create an arry with the times and names so we can sort it
		$c++;
	}
	$d->close();
	echo "<br>". $c. " tracks found";
		
	echo '<br><input type="submit" class="jz_submit" name="rename" value="Rename Tracks">';
	echo '</form></center>';
	
	// Now let's setup all our functions
	// -------------------------------------------------------------------
	
	function stripName(){
		global $web_root, $root_dir, $media_dir;
		
		// Let's let them know what we are doing
		echo "Please wait while we retrieve the listing of ALL files in your collection...<br>This may take a while so just sit back and relax....<br><br>";
		flushdisplay();
		
		// First let's get ALL the data back about each and every track
		$dirName = $web_root. $root_dir. $media_dir. "/". $_POST['rootDir'];
		$readCtr = 0;
		$retArray = readAllDirs($dirName, $readCtr, $retArray, "false", "true", "false");
		
		echo '<br><br>';
		
		// Now let's sort that and loop through it
		sort($retArray);
		for ($i=0;$i<count($retArray);$i++){
			// Now let's get the path WITHOUT the root stuff
			$path = str_replace($web_root. $root_dir. $media_dir,"",$retArray[$i]);
			// Now, let's split that into an array so we can get the album/artist/track data
			$dataArray = explode("/",$path);
			$track = $dataArray[count($dataArray)-1];
			$album = $dataArray[count($dataArray)-2];
			$artist = $dataArray[count($dataArray)-3];
			
			// Ok, now we've got the data let's do some find and replace
			$newTrack = $track;
			// Now let's see what to stip
			if (isset($_POST['stripAlbum']) or isset($_POST['stripAlbumTest']) or isset($_POST['stripAllTest']) or isset($_POST['stripAll'])){
				$newTrack = str_replace(" - ". $artist. "-","",$newTrack);
				$newTrack = str_replace(" - ". $artist,"",$newTrack);
				$newTrack = str_replace($artist,"",$newTrack);
			}
			if (isset($_POST['stripArtist']) or isset($_POST['stripArtistTest']) or isset($_POST['stripAllTest']) or isset($_POST['stripAll'])){
				$newTrack = str_replace(" - ". $album. "-","",$newTrack);
				$newTrack = str_replace(" - ". $album,"",$newTrack);
				$newTrack = str_replace($album,"",$newTrack);		
			}
			$ext = returnFileExt($newTrack);
			// Now let's make sure we didn't totally mangle the name
			if (substr_count($track,$album) > 1){
				// Ok, that means that the track name should be the album name
				$newTrack = str_replace(".". $ext,"",$newTrack). " - ". $album. ".". $ext;
			}
			
			// Now let's make sure the first 3 characters aren't " - "
			if (substr($newTrack,0,3) == " - "){
				$newTrack = substr($newTrack,3,strlen($newTrack));
			}
			
			// Now let's replace _ with space
			$newTrack = str_replace("_"," ",$newTrack);
			
			// Now let's strip some funky characters
			$newTrack = str_replace(" [-]", "", $newTrack);
			$newTrack = str_replace("[-]", "", $newTrack);
			$newTrack = str_replace(" [#]", "", $newTrack);
			$newTrack = str_replace("[#]", "", $newTrack);
			$newTrack = str_replace(" [ ]", "", $newTrack);
			$newTrack = str_replace("[ ]", "", $newTrack);
			$newTrack = str_replace(" .". $ext, ".". $ext, $newTrack);
			
			// Now if the file name is really short let's just stick with the old one
			if (strlen($newTrack) < 8){
				$newTrack = $track;
			}
			
			// Now let's trim newtrack
			$newTrack = trim($newTrack);
			
			// Now let's see if there was a change needed
			if ($track <> $newTrack){
				// Was this a test or for real?
				if (isset($_POST['stripAlbum']) or isset($_POST['stripArtist']) or isset($_POST['stripAll'])){
					echo 'Renaming:<br>'. $track. "<br>". $newTrack. "<br>";
					$newFile = str_replace($track,$newTrack,$retArray[$i]);
					if (rename($retArray[$i],$newFile)){
						echo '<font color="green">Success!</font><br><br>';
					} else {
						echo '<font color="red">Failed!</font><br><br>';
					}
				} else {
					echo 'Simulating:<br>'. $track. "<br>". $newTrack. "<br><br>";
				}
				flushdisplay();
			}
	
			
			//echo $track. "<br>". $newTrack. "<br><br>";
			
			//echo $artist. '<br>'. $album. '<br>'. $track. '<br><br>';
			
		}
		
		exit();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
?>