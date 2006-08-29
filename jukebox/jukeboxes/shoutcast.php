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
	* Contains the Shoutcast Jukebox code
	*
	* @since 2/9/05
	* @author Ross Carlson <ross@jinzora.org>
	*/
        global $SHOUT_CMD;
        $SHOUT_CMD = "nice -5 -- ". $jbArr[$_SESSION['jb_id']]['sc_trans_linux_path']. " ". $jbArr[$_SESSION['jb_id']]['sc_trans_linux_conf']. " > /dev/null 2>&1 &";

	/*
	
	NOTES FOR THIS JUKEBOX
	
	THIS JUKEBOX IS ONLY SUPPORTED ON LINUX!!!
	You MUST use the sc_trans_linux and sc_serv tools with this jukebox only they are supported
	YOU MUST also have Jinzora start the streaming using the GUI tool or it will NOT be able to control sc_trans_linux
	sc_trans_linux MUST also be executable by the webserver user
	
	This Jukebox requires the following settings:
	
	server
	port
	password
	description
	type
	sc_trans_linux_path
	sc_trans_linux_conf
	
	An example would be:
	$jbArr[0]['server'] = "localhost";
	$jbArr[0]['port'] = "8000";
	$jbArr[0]['password'] = "jinzora";
	$jbArr[0]['description'] = "Shoutcast";
	$jbArr[0]['type'] = "shoutcast";
	$jbArr[0]['sc_trans_linux_path'] = "/full/path/to/sc_trans_linux"; // THIS IS THE FULL PATH INCLUDING THE EXE
	$jbArr[0]['sc_trans_linux_conf'] = "/full/path/to/sc_trans.conf"; // THIS IS THE FULL PATH INCLUDING THE CONF FILE
	
	*/	
	
	
	/**
	* Returns the stats of the jukebox
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param return Returns a keyed array of the jukeboxe's abilities
	*/
	function retJBStats(){
		global $jbArr;
		
		$serv1 = new SCXML;
		$serv1->set_host($jbArr[$_SESSION['jb_id']]['server']);
		$serv1->set_port($jbArr[$_SESSION['jb_id']]['port']);
		$serv1->set_password($jbArr[$_SESSION['jb_id']]['password']);
		$serv1->retrieveXML();
		
		// Now let's get the stats
		$cur_listen=$serv1->fetchMatchingTag("CURRENTLISTENERS");
		$max_listen=$serv1->fetchMatchingTag("MAXLISTENERS");
		$peak_listen=$serv1->fetchMatchingTag("PEAKLISTENERS");
		if ($cur_listen == ""){$cur_listen = 0;}
		
		echo "<nobr>Current Streamers: ". $cur_listen. "</nobr>";
		echo "<br>";
		echo "<nobr>Peak Streamers: ". $peak_listen. "</nobr>";
		echo "<br>";
		echo "<nobr>Max Streamers: ". $max_listen. "</nobr><br>";
	}	
	
	/**
	* Returns a keyed array showing all the functions that this jukebox supports
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param return Returns a keyed array of the jukeboxe's abilities
	*/
	function returnJBAbilities(){
		
		$retArray['playbutton'] = false;
		$retArray['pausebutton'] = false;
		$retArray['stopbutton'] = false;
		$retArray['nextbutton'] = false;
		$retArray['prevbutton'] = false;
		$retArray['shufflebutton'] = false;
		$retArray['clearbutton'] = false;
		$retArray['status'] = true;
		$retArray['progress'] = false;
		$retArray['volume'] = false;
		$retArray['addtype'] = true;
		$retArray['nowplaying'] = true;
		$retArray['nexttrack'] = true;
		$retArray['fullplaylist'] = true;
		$retArray['refreshtime'] = true;
		$retArray['status'] = true;
		$retArray['jump'] = false;
		$retArray['stats'] = true;
		
		return $retArray;
	}
	
	/**
	* Returns the connection status of the player true or false
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param return Returns true or false
	*/
	function playerConnect(){
		global $jbArr;
		
		return true;
	}
	
	/**
	* Returns Addon tools for MPD - namely refresh jukebox database
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param return Returns a link to start sc_trans_linux
	*/
	function getAllAddOnTools(){
		
		$arr = array();
		$arr['action'] = "jukebox";
		$arr['subaction'] = "jukebox-command";
		$arr['command'] = "startsc";
		$arr['ptype'] = "jukebox";
		echo ' - <a href="javascript:void(0)" onClick="sendJukeboxRequest(\'startsc\')">'.word('Start Shoutcast').'</a>';
	}
	
	/**
	* Returns the currently playing tracks path so we can get the node
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param return Returns the currently playling track's path
	*/
	function getCurTrackPath(){
		global $jbArr;
		
		$serv1 = new SCXML;
		$serv1->set_host($jbArr[$_SESSION['jb_id']]['server']);
		$serv1->set_port($jbArr[$_SESSION['jb_id']]['port']);
		$serv1->set_password($jbArr[$_SESSION['jb_id']]['password']);
		$serv1->retrieveXML();
		$song_title=$serv1->fetchMatchingTag("SONGTITLE");
		
		$trackNum = getCurPlayingTrack();
		$pArray = getCurPlaylist();
		if (!isNothing($song_title)) {
		  for ($i=0; $i < count($pArray); $i++){
		    // Are we on it?
		    if (stristr($pArray[$i],$song_title)){
		      $retVal = $pArray[$i];
		    }
		  }
		}
		
		return $retVal;
	}
	
	/**
	* Returns the currently playing track number
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param return Returns the currently playling track number
	*/
	function getCurPlayingTrack(){
		global $jbArr;
		
		$serv1 = new SCXML;
		$serv1->set_host($jbArr[$_SESSION['jb_id']]['server']);
		$serv1->set_port($jbArr[$_SESSION['jb_id']]['port']);
		$serv1->set_password($jbArr[$_SESSION['jb_id']]['password']);
		$serv1->retrieveXML();
		$song_title=$serv1->fetchMatchingTag("SONGTITLE");

		if (isNothing($song_title)) return false;
		// Ok we need to find the current track then get that out of the playlist so we'll know where we are
		$pArray = getCurPlaylist();
		for ($i=0; $i < count($pArray); $i++){
			// Are we on it?
			if (stristr($pArray[$i],$song_title)){
				$retVal = $i;
			}
		}
		return $retVal;
	}
	
	/**
	* Returns the currently playing playlist
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param return Returns the currently playling playlist
	*/
	function getCurPlaylist(){
		global $jbArr, $include_path;
		
		$filename = $include_path. "temp/shoutcast.lst";
		$contents = @file_get_contents($filename);
		$pArray = explode("\n",$contents);
		for ($i=0; $i < count($pArray); $i++){
			// Now let's add this
			if ($pArray[$i] <> ""){
				$retArr[] = $pArray[$i];
			}
		}
		return $retArr;
	}

	/**
	* Passes a playlist to the jukebox player
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param $playlist The playlist that we are passing
	*/
	function playlist($playlist){
		global $include_path, $jbArr,$SHOUT_CMD,$jzSERVICES;

		$playlist = $jzSERVICES->createPlaylist($playlist,"jukebox");
		$plfile = $include_path. "temp/shoutcast.lst";
		$add = $_SESSION['jb-addtype'];
		// ADD TYPES: current, begin, end, replace
		// Interpretations:
		// CURRENT- Add the items to be played next, and add the remaining items after these.
		// BEGIN - Do the same, but also skip to the next track (the first in the new list)
		// END - Add these items after the remaining items. Do not restart playback.
		// REPLACE - put in the new playlist and restart playback.

		$pArray = explode("\n",$playlist);
		$data = "";

		if ($add == "end") {
		  // TODO: Add back unplayed playlist into $data.
		}

		for ($i=0; $i < count($pArray); $i++){
			// Now let's add this
			if ($pArray[$i] <> ""){
				$data .= $pArray[$i]. "\n";
			}
		}

		if ($add == "current" || $add == "begin") {
		  // TODO: Add back unplaed playlist into $data.
		}

		// Now let's write out the playlist
		$handle = fopen($plfile, "w");
		fwrite($handle,$data);	
		fclose ($handle);

		// Now let's restart sc_trans_linux
		// Check their 'add-type', too:		
		exec ("killall sc_trans_linux -USR1 > /dev/null 2>&1 &");
		if ($add == "replace" || $add == "begin") {
		  exec ("killall sc_trans_linux -WINCH > /dev/null 2>&1 &");  
		}
		
		writeLogData("messages","Reloading Shoutcast playlist");
		//exec ($SHOUT_CMD);	

		if (defined('NO_AJAX_JUKEBOX')) {
		?>
		<script>
			history.back();
		</script>
		<?php
		    }
		exit();
	}
		
	/**
	* Passes a command to the jukebox player
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param $command The command that we passed to the player
	* @param $goBack Should we go back after executing (default is true)
	*/
	function control($command, $goBack = true){
		global $jbArr,$SHOUT_CMD;

		// Now let's execute the command
		switch ($command){
			case "startsc":
			  $cmd = $SHOUT_CMD;
			  writeLogData("messages","Starting Shoutcast with: " . $cmd);
			  exec($cmd);
			break;
			case "play":
				return;
			break;
			case "stop":
				return;
			break;
			case "pause":
				return;
			break;
			case "previous":
				return;
			break;
			case "next":
				return;
			break;
			case "volume":
				// Now we have to set the value based on 0-255
				$vol = $_POST['jbvol'];
				return;
			break;
			case "playwhere":
				// Ok, let's set where they are playing
				$_SESSION['jb_playwhere'] = $_POST['jbplaywhere'];
				// Now let's figure out it's ID
				for ($i=0; $i < count($jbArr); $i++){
					if ($jbArr[$i]['description'] == $_SESSION['jb_playwhere']){
						$_SESSION['jb_id'] = $i;
					}
				}
			break;
			case "jumpto":
				// We need to add 1 so we don't start at 0
				$pos = $_POST['jbjumpto'];
				return;
			break;
			case "clear":
				return;
			break;
			case "random_play":
				return;
			break;
			case "addwhere":
				$_SESSION['jb-addtype'] = $_POST['addplat'];
			break;
		}
		if ($goBack && defined('NO_AJAX_JUKEBOX')){
			?>
			<script>
				history.back();
			</script>
			<?php
		}
	}
	
	/**
	* Returns the players current status
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	*/
	function getStatus(){
		global $jbArr;
		
		$serv1 = new SCXML;
		$serv1->set_host($jbArr[$_SESSION['jb_id']]['server']);
		$serv1->set_port($jbArr[$_SESSION['jb_id']]['port']);
		$serv1->set_password($jbArr[$_SESSION['jb_id']]['password']);
		$serv1->retrieveXML();
		
		switch($serv1->fetchMatchingTag("STREAMSTATUS")){
			case "1":
				return "playing";
			break;
		}	
		return "stopped";
	}
	
	/**
	* Returns the current playing track
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @return Returns the name of the current playing track
	*/
	function getCurTrackName(){
		global $jbArr;

		$serv1 = new SCXML;
		$serv1->set_host($jbArr[$_SESSION['jb_id']]['server']);
		$serv1->set_port($jbArr[$_SESSION['jb_id']]['port']);
		$serv1->set_password($jbArr[$_SESSION['jb_id']]['password']);
		$serv1->retrieveXML();
		$song_title=$serv1->fetchMatchingTag("SONGTITLE");
		
		return $song_title;
	}
	
	/**
	* Returns how long is left in the current track (in seconds)
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @return Returns the name of the current playing track
	*/
	function getCurTrackRemaining(){
		global $jbArr;
		
		// Ok, we'll have to guess this, we'll need to get the full path of the track and assume that it just started playing
		return getCurTrackLength();
	}
	
	/**
	* Gets the length of the current track
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @param return returns the amount of time remaining in seconds
	*/
	function getCurTrackLength(){
		global $jbArr, $media_dirs;

		// Ok, now we need to get the length of this track by reading it's meta data
		$path = getCurTrackPath();
		$mArr = explode("|",$media_dirs);
		for ($i=0; $i < count($mArr); $i++){
			$path = str_replace($mArr[$i]. "/","",$path);
		}
		$track = new jzMediaTrack($path);
		$meta = $track->getMeta();
		return $meta['length'];
	}
	
	/**
	* Returns how long is left in the current track (in seconds)
	* 
	* @author Ross Carlson
	* @version 2/9/05
	* @since 2/9/05
	* @return Returns the name of the current playing track
	*/
	function getCurTrackLocation(){
		global $jbArr;
		
		return 0;
	}
	
	class SCXML {

	/* DO NOT CHANGE ANYTHING FROM THIS POINT ON - THIS MEANS YOU !!! */
	
	  var $depth = 0;
	  var $lastelem= array();
	  var $xmlelem = array();
	  var $xmldata = array();
	  var $stackloc = 0;
	
	  var $parser;
	
	  function set_host($sc_host) {
		$this->host=$sc_host;
	  }
	
	  function set_port($sc_port) {
		$this->port=$sc_port;
	  }
	
	  function set_password($sc_password) {
		$this->password=$sc_password;
	  }
	
	  function startElement($parser, $name, $attrs) {
		$this->stackloc++;
		$this->lastelem[$this->stackloc]=$name;
		$this->depth++;
	  }
	
	  function endElement($parser, $name) {
		unset($this->lastelem[$this->stackloc]);
		$this->stackloc--;
	  }
	
	  function characterData($parser, $data) {
		$data=trim($data);
		if ($data) {
		  $this->xmlelem[$this->depth]=$this->lastelem[$this->stackloc];
		  $this->xmldata[$this->depth].=$data;
		}
	  }
	
	  function retrieveXML() {
		$rval=1;
	
		$sp=@fsockopen($this->host,$this->port,&$errno,&$errstr,10);
		if (!$sp) $rval=0;
		else {
	
		  set_socket_blocking($sp,false);
	
		  // request xml data from sc server
	
		  fputs($sp,"GET /admin.cgi?pass=$this->password&mode=viewxml HTTP/1.1\nUser-Agent:Mozilla\n\n");
	
		  // if request takes > 15s then exit
	
		  for($i=0; $i<30; $i++) {
		if(feof($sp)) break; // exit if connection broken
		$sp_data.=fread($sp,31337);
		usleep(500000);
		  }
	
		  // strip useless data so all we have is raw sc server XML data
	
		  $sp_data=ereg_replace("^.*<!DOCTYPE","<!DOCTYPE",$sp_data);
	
		  // plain xml parser
	
		  $this->parser = xml_parser_create();
		  xml_set_object($this->parser,&$this);
		  xml_set_element_handler($this->parser, "startElement", "endElement");
		  xml_set_character_data_handler($this->parser, "characterData");
	
		  if (!xml_parse($this->parser, $sp_data, 1)) {
		$rval=-1;
		  }
	
		  xml_parser_free($this->parser);
	
		}
		return $rval;
	  }
	
	  function debugDump(){
		reset($this->xmlelem);
		while (list($key,$val) = each($this->xmlelem)) {
		  echo "$key. $val -> ".$this->xmldata[$key]."\n";
		}
	
	  }
	
	  function fetchMatchingArray($tag){
		reset($this->xmlelem);
		$rval = array();
		while (list($key,$val) = each($this->xmlelem)) {
		  if ($val==$tag) $rval[]=$this->xmldata[$key];
		}
		return $rval;
	  }
	
	  function fetchMatchingTag($tag){
		reset($this->xmlelem);
		$rval = "";
		while (list($key,$val) = each($this->xmlelem)) {
		  if ($val==$tag) $rval=$this->xmldata[$key];
		}
		return $rval;
	  }
  }
?>