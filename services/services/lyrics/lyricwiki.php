<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
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
* - This is the LyricsWiki lyrics service.
*
* @since 04.14.08
* @author Jan Kleinhans
*/

$jzSERVICE_INFO = array();
$jzSERVICE_INFO['name'] = "LyricWiki";
$jzSERVICE_INFO['url'] = "http://www.lyricwiki.org";


define('SERVICE_LYRICS_lyricwiki','true');

/*
* Gets the lyrics via LyricWiki
*
* @author Jan Kleinhans
* @version 04/14/08
* @since 04/14/08
* @param $track a jzMediaTrack
**/

function SERVICE_GETLYRICS_lyricwiki($track) {
   global $include_path;
   
   include_once($include_path. "lib/snoopy.class.php");
   $meta = $track->getMeta();
   $artist = $meta['artist'];
   $name = $meta['title'];
   
   // Let's up the max execution time here
   ini_set('max_execution_time','60000');
   
   // Now let's see if we can get close...
   $snoopy = new Snoopy;
   $snoopy->fetch("http://lyricwiki.org/api.php?artist=". urlencode($artist). '&song='. urlencode($name). '&fmt=xml');
   $contents = $snoopy->results;
   unset($snoopy);
   // Now let's see if we got an exact match
   if  (!stristr($contents,'<lyrics>Not found')
      //or (strstr($contents,'SUCCESS')
      and (stristr($contents,iconv("UTF-8","ISO-8859-1",$artist)) and stristr($contents,iconv("UTF-8","ISO-8859-1",$name)))
      ){
      $lyrics = "";
      // Ok, now let's get the ID number
         $lyrics = substr($contents,strpos($contents,"<lyrics>")+8,999999);
         $lyrics = stripslashes(substr($lyrics,0,strpos($lyrics,"</lyrics>")));
      }
   
   
   if ($lyrics == "") {
      return false;
   } 
   $lyrics2=iconv("ISO-8859-1","UTF-8",$lyrics);
   return $lyrics2;
}

?>