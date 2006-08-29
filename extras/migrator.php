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
	* - Helps migrate artists to genres
	* -- COMPLETELY UNSUPPORTED!!!!   -   Use at your own risk!
	*
	* @since 01/28/05
	* @author Ross Carlson <ross@jinzora.org>
	*
	*/
	
	// first let's include all the functions and stuff we'll need
	$include_path = str_replace("/extras","",dirname(__FILE__)). "/";
	include_once('../settings.php');
	include_once('../system.php');
	include_once('../lib/general.lib.php');
	include_once('../services/class.php');

	$jzSERVICES = new jzServices();
	$jzSERVICES->loadService("metadata", "amazon");
	
	// Now let's see if they submitted the form
	if (isset($_POST['migrate'])){
		echo '<br><strong>Beginning reorg of media, please stand by, this could take a while...</strong><br><br>';
		echo "Scanning files";
		flushdisplay();
		
		// Let's read all the files into a big array
		$readCtr = 0;
		$retArray = readAllDirs($_POST['path'], $readCtr, $retArray, "false", "true", "false");
		echo "<br>";

		$master = "|||";
		
		// Let's look at each file one by one to see what to do with it
		foreach($retArray as $item){
			// Let's strip the full path so we'll have just what we need
			$data = str_replace($_POST['path']. "/","",$item);
			
			// Now using our settings let's figure out what we're looking at
			// First we'll split the directories apart
			$info = explode("/",$data);
			
			
			
			
			
			$artist = $info[0];
			$album =  $info[1];
			$track =  $info[2];

			// Have we looked at this artist before?
			if (!strstr($master,$artist)){
				// Now let's create our array to pass to the amazon service
				$aData['artist'] = $artist;
				$aData['album'] = $album;
				
				// Ok, now that we have the data let's figure out the genres for each artist
				$genre = $jzSERVICES->getArtistMetadata($aData, "genre");
				
				if ($genre == ""){
					$genre = "Unknown";
				}
				
				echo $genre. " - ";
				echo $artist. "<br>";
			}
			flushdisplay();
			
			// Now let's add this artist to the master list so we don't do them twice
			$master .= $artist. "|||";	
		}
		
		exit();
	}
	
	
	
	?>
	<br>
	<strong>Jinzora Migration Tools</strong><br>
	Welcome to the Jinzora migration tools.  These tools can help you migrate your media collection
	into a better format for Jinzora to work with.
	<br><br><br>
	<form name="migrateForm" method="post">
		Path to Media: <br>
		<input type="text" size="40" name="path" class="jz_input"><br>
		<font size="1">(the FULL path to the media on your server)</font><br>
		<br>
		Current Folder Structure: <br>
		<input type="text" size="40" name="format" class="jz_input" value="%A/%a/%n - %t"><br>
		<font size="1">
			You may specify the current media structure using the following variables:<br>
			%A = Artist Name<br>
			%a = Album Name<br>
			%n = Track Number<br>
			%t = Track Name<br>
			/ = Directory Separator<br>
			NOTE: File Extensions are automatically removed
		</font>
		
		
		<br><br>
		<input type="submit" name="migrate" value="Reorganize Media" class="jz_submit">
	</form>
	
	
	


























	
