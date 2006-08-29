<?php define('JZ_SECURE_ACCESS','true');
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
	* - This page generate all RSS feeds
	* - It need the 'type' GET paramter
	*
	* @since 02.02.04 
	* @author Laurent Perrin <laurent@la-base.org>
	*/
        $jz_lang_file = "";
	include ('lib/general.lib.php');
        include_once('system.php');
	include ('system.php');
	include ('settings.php');
	@include_once("lang/${jz_lang_file}-simple.php");
	@include_once("lang/${jz_lang_file}-extended.php");
	include_once('backend/backend.php');
        include_once('services/class.php');
	include_once('frontend/display.php');
	$display = new jzDisplay();
	$jzSERVICES = new jzServices();
	$jzSERVICES->loadStandardServices();
	// let's get every top list they want
	$types = explode(':', $_GET['type']);
	foreach ($types as $type){
		switch ($type){
			case 'most-played':		
				$func = "getMostPlayed";
				$title = "Top Played Albums";
				$distance = "album";	
				$showPlays = true;
				$showDownload = false;	
				break;
			case 'most-played-artist':		
				$func = "getMostPlayed";
				$title = "Top Played Artists";
				$distance = "artist";	
				$showPlays = true;
				$showDownload = false;	
				break;
		        case 'most-played-tracks':		
				$func = "getMostPlayed";
				$title = "Top Played Tracks";
				$distance = "track";	
				$showPlays = true;
				$showDownload = false;	
				break;
			case 'last-added':
				$func = "getRecentlyAdded";
				$title = "New Albums";
				$distance = "album";
				$showPlays = false;
				$showDownload = false;
				break;
		        case 'last-added-artists':
		                $func = "getRecentlyAdded";
				$title = "New Artists";
				$distance = "artist";
				$showPlays = false;
				$showDownload = false;
				break;
			case 'last-added-tracks':
		                $func = "getRecentlyAdded";
				$title = "New Tracks";
				$distance = "track";
				$showPlays = false;
				$showDownload = false;
				break;
			case 'most-downloaded':
				$func = "getMostDownloaded";
				$title = "Top Downloaded Albums";
				$distance = "album";
				$showPlays = false;
				$showDownload = true;
				break;
		case 'recentplayed-album':
		  $func = "getRecentlyPlayed";
		  $title = "Recently Played Albums";
		  $distance = "album";
		  $showPlays = false;
		  $showDownload = false;
		  break;
		case 'recentplayed-artist':
		  $func = "getRecentlyPlayed";
		  $title = "Recently Played Artists";
		  $distance = "artist";
		  $showPlays = false;
		  $showDownload = false;
		  break;

		case 'recentplayed-track':
		  $func = "getRecentlyPlayed";
		  $title = "Recently Played Tracks";
		  $distance = "track";
		  $showPlays = false;
		  $showDownload = false;
		  break;

		case 'toprated-album':
		  $func = "getTopRated";
		  $title = "Top Rated Albums";
		  $distance = "album";
		  $showPlays = false;
		  $showDownload = false;
		  break;
		case 'toprated-artist':
		  $func = "getTopRated";
		  $title = "Top Rated Artists";
		  $distance = "artist";
		  $showPlays = false;
		  $showDownload = false;
		  break;
		case 'topviewed-artist':
		  $func = "getMostViewed";
		  $title = "Most Viewed Artists";
		  $distance = "artist";
		  $showPlays = false;
		  $showDownload = false;
		  break;

			default:
				echo 'Usage : rss.php?type=most-played:most-played-artist:most-played-tracks:last-added:last-added-artists:last-added-tracks:<br>most-downloaded:recentplayed-album:recentplayed-artist:recentplayed-track:toprated-album:toprated-artist:topviewed-artist';
				return;
		} // switch 
		
		// Now let's display the data
		header("Content-type: text/xml");
		echo '<?xml version="1.0" encoding="ISO-8859-1"?>'. "\n".
				'<rdf:RDF'. "\n".
				 'xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"'. "\n".
				 'xmlns="http://purl.org/rss/1.0/"'. "\n".
				 'xmlns:dc="http://purl.org/dc/elements/1.1/"'. "\n".
				 'xmlns:slash="http://purl.org/rss/1.0/modules/slash/"'. "\n".
				 'xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/"'. "\n".
				 'xmlns:admin="http://webns.net/mvcb/"'. "\n".
				 'xmlns:syn="http://purl.org/rss/1.0/modules/syndication/"'. "\n".
				'>'. "\n".
				'<channel rdf:about="http://www.jinzora.com">' . "\n" . 
				'<title>Jinzora RSS</title>' . "\n" . 
				'<link>http://www.jinzora.com</link>' . "\n" . 
				"<description>Jinzora ". $title. "</description>\n" . 
				"<items>\n" .
				"</items>\n" .
				'</channel>' . "\n";
		if (isset($_GET['root'])) {
		  $node = new jzMediaNode(stripSlashes($_GET['root']));
		} else {
		  $node = new jzMediaNode();
		}
		if ($distance == "track") {
		  $returnType = "tracks";
		} else {
		  $returnType = "nodes";
		}
		$arr = $node->$func($returnType,distanceTo($distance,$node),5);
		for ($i=0;$i<count($arr);$i++){
			// Now let's create the display
			$art = $arr[$i]->getMainArt();
			$imgUrl = jzCreateLink($art,"image");
			$urlArr = array();
			$urlArr['action'] = "playlist";
			$urlArr['jz_path'] = $arr[$i]->getPath("String");
			if ($distance == "track") {
			  $urlArr['type'] = "track";
			}
			$title_add = "";
			if ($showPlays){
				$title_add = ' ('. $arr[$i]->getPlayCount(). ')';
			} 
			if ($showDownload){
				$title_add = ' ('. $arr[$i]->getDownloadCount(). ')';
			}
			
			// Now that we have the data let's echo it
			echo '<item rdf:about="'. $this_site.  str_replace("rss.php","index.php",str_replace("&","&amp;",urlize($urlArr))). '">'. "\n";
			echo '     <title>'. htmlnumericentities($arr[$i]->getName(). $title_add) . '</title>'. "\n";
			echo '     <link>'. $this_site. str_replace("rss.php","index.php",str_replace("&","&amp;",urlize($urlArr))). '</link>'. "\n";
			echo '     <description><![CDATA['. nl2br($arr[$i]->getDescription()). ']]></description>'. "\n";
			echo '</item>'. "\n";	
		}
	}
	
	// Now let's close out...
	echo '</rdf:RDF>';
?>