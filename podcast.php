<?php define('JZ_SECURE_ACCESS','true');
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *    
	* JINZORA | Web-based Media Streamer  
	*
	* Jinzora is a Web-based media streamer, primarily desgined to stream MP3s 
	* (but can be used for any media file that can stream from HTTP). 
	* Jinzora can be integrated into a CMS site, run as a standalone application, 
	* or integrated into any PHP website.  It is released under the GNU GPL. 
	* 
	* Jinzora Author:
	* Ross Carlson: ross@jasbone.com 
	* http://www.jinzora.org
	* Documentation: http://www.jinzora.org/docs	
	* Support: http://www.jinzora.org/forum
	* Downloads: http://www.jinzora.org/downloads
	* License: GNU GPL <http://www.gnu.org/copyleft/gpl.html>
	* 
	* Contributors:
	* Please see http://www.jinzora.org/modules.php?op=modload&name=jz_whois&file=index
	*
	* Code Purpose: Takes a given path and generates a Podcast feed for it
	* Created: 9.24.03 by Ross Carlson
	*
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	
	$include_path = getcwd(). "/";
	
	@include_once('system.php');        
	@include_once('settings.php');  	
	include_once($include_path. "lib/general.lib.php");
	include_once($include_path. 'services/class.php');
	include_once('backend/backend.php');
	include_once('frontend/display.php');
	$jzSERVICES = new jzServices();
	$jzSERVICES->loadStandardServices();
	$display = new jzDisplay();
	
	// Now let's get the node so we can get the tracks
	$node = new jzMediaNode($_GET['jz_path']);
	$par = $node->getAncestor("artist");
	$artist = $par->getName();
	$tracks = $node->getSubNodes("tracks",-1);
	
	// Now let's display the header
	header("Content-type: application/xml");
	echo  '<?xml version="1.0" encoding="utf-8"?>'. "\n".
			 '<rss xmlns:itunes="http://www.itunes.com/DTDs/Podcast-1.0.dtd" version = "2.0">'. "\n".
			 '<channel>'. "\n".
			 '  <atom:link rel="self" type="application/rss+xml" title="Jinzora - '. str_replace("&","&amp;",$artist. " - ". $node->getName()). '" href="'. $this_site. $_SERVER['REQUEST_URI']. '" xmlns:atom="http://purl.org/atom/ns#" />'.   "\n".
			 '  <title>Jinzora - '. str_replace("&","&amp;",$artist. " - ". $node->getName()). '</title>'. "\n".
			 '  <link>http://www.jinzora.com/</link>'. "\n".
			 '  <language>en-us</language>'. "\n".
			 '  <generator>Jinzora http://www.jinzora.com/</generator>'. "\n";
	
	if (($art = $node->getMainArt("200x200")) <> false) {
		echo '  <itunes:image rel="image" type="video/jpeg" href="'. str_replace("&","&amp;",$display->returnImage($art,false, false, false, "limit", false, false, false, false, false, "0", false, true)). '">'. $node->getName(). '</itunes:image>'. "\n";
		echo '  <itunes:link rel="image" type="video/jpeg" href="'. str_replace("&","&amp;",$display->returnImage($art,false, false, false, "limit", false, false, false, false, false, "0", false, true)). '">'. $node->getName(). '</itunes:link>'. "\n";
	}
	if (($desc = $node->getDescription()) <> false) {
		echo '  <description><![CDATA['. $desc. ']]></description>'. "\n";
	}

	// Now let's loop through the tracks
	$i=0;
	foreach($tracks as $track){
		// Let's get the tracks path
		$meta = $track->getMeta();
		$artist = $track->getAncestor("artist");
		
		// Now let's create the URL		
		$tpArr = explode("?",$track->getFileName("user"));
		$path = $this_site. $root_dir. "/mediabroadcast.php/". $tpArr[1]. "/". $track->getName(). ".mp3";
		$path = str_replace("&","&amp;",$path);
		
		$tName = $track->getName();		
		if ($meta['number'] <> ""){
			$tName = $meta['number']. " - ". $tName;
		}

		// Now let's create the data
		$i++;
		//$path = "http://localhost/track". $i. ".php?track.mp3";
		echo '<item>'. "\n";
		echo '<title>'. str_replace("&","&amp;",$tName). '</title>'. "\n";
		echo '<enclosure url="'. $path. '" length="'. round((($meta['size'] * 1024) * 1024)). '" type="audio/mpeg"/>'. "\n";
		echo '</item>'. "\n";
	}
	
	echo '</channel>'. "\n";
	echo '</rss>';
?>
