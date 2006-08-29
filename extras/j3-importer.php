<?php
	define('JZ_SECURE_ACCESS','true');
	/**
	* - JINZORA | Web-based Media Streamer -  
	* 
	* Jinzora is a Web-based media streamer, primarily desgined to stream MP3s 
	* (but can be used for any media file that can stream from HTTP). 
	* Jinzora can be integrated into a CMS site, run as a standalone application, 
	* or integrated into any PHP website.  It is released under the GNU GPL.
	* 
	* - Resources -
	* - Jinzora Author: Ross Carlson <ross@jasbone.com>
	* - Web: http://www.jinzora.org
	* - Documentation: http://www.jinzora.org/docs	
	* - Support: http://www.jinzora.org/forum
	* - Downloads: http://www.jinzora.org/downloads
	* - License: GNU GPL <http://www.gnu.org/copyleft/gpl.html>
	* 
	* - Contributors -
	* Please see http://www.jinzora.org/team.html
	* 
	* - Code Purpose -
	* - This is a base importer for use during the Jinzora 3.0 development cycle - UNSUPPORTED!
	*
	* @since 11.12.05
	* @author Ross Carlson <ross@jinzora.org>
	* @author Ben Dodson <ben@jinzora.org>
	*/
	
	$include_path = "c:/reactor/core/htdocs/jinzora2/";
	$dbhost = "localhost";
	$dbuser = "root";
	$dbpass = "password";
	$dbname = "jinzora3";

	if (!isset($_POST['media_dir'])){
		?>
		The purpose of this tool is to import data into the Jinzora 3.0 database for development purposes only.  
		Only use this tool if you understand it!<br><br>		
		Don't forget to set the include path in this file!<br><br>
		<form action="j3-importer.php" method="post" name="import">
			Media Directory: <input type="text" name="media_dir"> <input type="submit" name="import_media" value="Import">
		</form>
		<?php
		exit();
	}
	
	// Ok, let's get on with it
	// We'll need to include a few thigns
	include_once('../settings.php');
	include_once('../lib/general.lib.php');
	include_once('../services/class.php');
	
	// First let's clean up their media dir
	$media_dir = $_POST['media_dir'];
	$media_dir = str_replace("//","/",str_replace("\\","/",$media_dir));
	
	// Now let's read each file under there
	echo "Reading all files in: ". $media_dir. ", please wait this might take quite a while...<br>";
	
	$readCtr = 0;
	$retArray = readAllDirs($media_dir, &$readCtr, &$retArray, "false", "true");
	
	echo "<br>". count($retArray). " files read, processing...<br>";
	
	// Let's connect to the database
	mysql_connect($dbhost, $dbuser, $dbpass);
	mysql_select_db($dbname);
	
	// Let's load up our services
	$jzSERVICES = new jzServices();
	$jzSERVICES->loadStandardServices();
	
	foreach($retArray as $file){
		// Let's read the meta data from the file
		$meta = $jzSERVICES->getTagData($file);
		
		// Let's insert into Genres
		$query = 'insert into jz_genres (Name) values ("'. $meta['genre']. '")';
		mysql_query($query);
		// Now let's get the ID for that genre
		$query = 'select ID from jz_genres where Name = "'. $meta['genre']. '"';
		$result = mysql_query($query);
		while (list($ID) = mysql_fetch_row($result)) {
			$genreID = $ID;
		}
		
		// Let's insert into artists
		$query = 'insert into jz_artists (Name) values ("'. $meta['artist']. '")';
		mysql_query($query);
		// Now let's get the ID for that artists
		$query = 'select ID from jz_artists where Name = "'. $meta['artist']. '"';
		$result = mysql_query($query);
		while (list($ID) = mysql_fetch_row($result)) {
			$artistID = $ID;
		}
		
		// Let's insert into Albums
		$query = 'insert into jz_albums (Name,Year) values ("'. $meta['album']. '","'. $meta['year']. '")';
		mysql_query($query);
		// Now let's get the ID for that artists
		$query = 'select ID from jz_albums where Name = "'. $meta['album']. '"';
		$result = mysql_query($query);
		while (list($ID) = mysql_fetch_row($result)) {
			$albumID = $ID;
		}
		
		// Let's insert into tracks
		$query = 'insert into jz_tracks (Name,Filepath,Number,Bitrate,Frequency,Filesize,Length,Year,Extension,Description) values ('.
						 '"'. $meta['title']. '",'.
						 '"'. $file. '",'.
						 '"'. $meta['number']. '",'.
						 '"'. $meta['bitrate']. '",'.
						 '"'. $meta['frequency']. '",'.
						 '"'. $meta['size']. '",'.
						 '"'. $meta['length']. '",'.
						 '"'. $meta['year']. '",'.
						 '"'. $meta['extension']. '",'.
						 '"'. $meta['comment'].
						 '")';
		mysql_query($query);
		// Now let's get the ID for that artists
		$query = 'select ID from jz_tracks where Filepath = "'. $file. '"';
		$result = mysql_query($query);
		while (list($ID) = mysql_fetch_row($result)) {
			$trackID = $ID;
		}
		
		// Now let's do all our join inserts
		$query = 'insert into jz_artist_album (ArtistID,AlbumID) values ("'. $artistID. '","'. $albumID. '")';
		mysql_query($query);
		$query = 'insert into jz_genre_album (GenreID,AlbumID) values ("'. $genreID. '","'. $albumID. '")';
		mysql_query($query);
		$query = 'insert into jz_genre_artist (GenreID,ArtistID) values ("'. $genreID. '","'. $artistID. '")';
		mysql_query($query);
		$query = 'insert into jz_album_track (AlbumID,TrackID) values ("'. $albumID. '","'. $trackID. '")';
		mysql_query($query);
		
		
		// Now let's display
		echo ".";
		flushdisplay();
	}
	
	// Now let's close the connection to the database
	mysql_close();
?>