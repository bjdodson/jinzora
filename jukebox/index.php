<?php
define ('JZ_SECURE_ACCESS','true');
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
	* Builds a zero-configuration jukebox
	*
	* @since 2/9/05
	* @author Ben Dodson <bjdodson@gmail.com>
	*/

  // Quickbox instance
$POLL_TIME=2; // blech
$include_path='../';
require_once('../jzBackend.php');
$be = new jzBackend();

// Get a semi-unique ID
$id = getMyId();
$boxes = $be->loadData('quickboxes');

if (!isset($boxes[$id])) {
  $box = array('id'=>$id);
 } else {
  $box = $boxes[$id];
 }

$box['active_time']=time();
$box['poll_time']=$POLL_TIME;
if (isset($_REQUEST['update_pos'])) {
  $box['pos'] = $_REQUEST['update_pos'];
 }
$boxes[$id]=$box;
$be->storeData('quickboxes',$boxes,1);
//$be->storeData('quickboxes',array(),1); // clear



if (isset($_REQUEST['update']) && !isset($_REQUEST['update_pos'])) {
  require_once(dirname(__FILE__).'/../lib/json.php');
  $obj = array();
  $obj['command'] = '';
  $obj['time'] = '';


  if (isset($box['command'])) {
    $obj['command'] = $box['command'];
    $obj['time'] = $box['command_time'];
  

    if ($box['command'] == 'playlist') {
      $obj['playlist'] = $box['playlist'];
      $obj['addtype'] = $box['addtype'];
    }

    if ($box['command'] == 'jumpto') {
      $obj['pos'] = $box['pos'];
    }
  }

  $json = new Services_JSON();
  

  print $json->encode($obj);
  return;
 }



function getMyId() {
  if (isset($_REQUEST['id'])) {
    return $_REQUEST['id'];// preg_replace("/[^a-zA-Z0-9s]/", "",$_REQUEST['id']);
  }
return uniqid();
// lame:
  $adjectives = array('broad',
		      'chubby',
		      'flat',
		      'green',
		      'red',
		      'eager',
		      'late',
		      'quick',
		      'tame',
		      'ugly',
		      'scary',
		      'plain',
		      'swift',
		      'lovely',
		      'loud');
  $nouns = array('fox',
		 'bird',
		 'house',
		 'man',
		 'one',
		 'zoo',
		 'barn',
		 'monkey',
		 'arm',
		 'leg',
		 'tower');

  return $adjectives[array_rand($adjectives)].$nouns[array_rand($nouns)];
}


################################################################
#### START HTML ################################################
?>

<html>
<head>
<script type="text/javascript" src="/music/lib/jquery/jquery.js"></script>
<script type="text/javascript" language="javascript" src="jukeboxes/quickbox/niftyplayer.js"></script>
<script type="text/javascript">
  var qb = new Object();
  qb.lastcommand = 0;
  qb.pos = 0;
  qb.loaded = false;

function npNext() {
  if (qb.pos == qb.playlist.length-1) {
    qb.pos=0;
    qb.np.reset();
    return;
  }
  qb.pos++;
  qb.np.loadAndPlay(qb.playlist[qb.pos]);

  // server update
  $.getJSON('index.php', { id: '<?php echo $id; ?>', update: 'true', update_pos: qb.pos }, function(data) {});
}

function loadJukebox() {
  if (!qb.loaded) {
    // handle jukebox playlist
    qb.np = niftyplayer('np');
    if (!qb.np) {
      setTimeout('loadJukebox()',1000);
      return;
    }

    qb.np.registerEvent('onSongOver','npNext()');
    qb.loaded = true;
    qb.paused=false;
  }
  updateJukebox();
}

  function updateJukebox() {
  $.getJSON('index.php', { id: '<?php echo $id; ?>', update: 'true' }, function(data) {
      if (qb.lastcommand != data.time) {
	qb.lastcommand = data.time;

	switch (data.command) {
	case "playlist":
	  qb.playlist = data.playlist;
	  
	  state = qb.np.getState();

	  if (data.addtype == 'replace') {
	    qb.pos = 0;
	    qb.np.loadAndPlay(qb.playlist[qb.pos]);
	    return;
	  }

	  if (state == 'empty' && qb.playlist.length > 0) {
	    qb.np.loadAndPlay(qb.playlist[qb.pos]);
	    return;
	  }

	  break;
	case "stop":
	  qb.np.stop();
	  break;
	case "play":
	  qb.np.play();
	  break;
	case "pause":
	  if (!qb.paused)
	    qb.np.pause();
	  else
	    qb.np.play();
	  qb.paused=!qb.paused;
	  break;
	case "next":
	  npNext();
	  break;
	case "previous":
	  if (qb.pos > 0) {
	    qb.pos--;
	  }
	  qb.np.loadAndPlay(qb.playlist[qb.pos]);
	case "jumpto":
	  qb.pos = data.pos;
	  qb.np.loadAndPlay(qb.playlist[qb.pos]);
	}


      }
    });
  
  
  
  
  setTimeout('updateJukebox()',<?php echo $POLL_TIME*1000; ?>);
}
</script>
  </head>
  <body>
  <script type="text/javascript">
  loadJukebox();
  </script>
  <div>
  I am not just a boring webpage. I am a jukebox, and my id is 
  <em><?php echo $id; ?></em>.
  </div>
  <div id="qrcode">
    <?php 

	$host='http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
	$host = str_replace("/jukebox/index.php","/api.php",$host);
	//$id=$id;
	$user="chumby";
	$pass="chumby";
	$barcode = '{host:"'.htmlentities($host).'",'.
		    'jb_id:"'.htmlentities($id).'",'.
		    'username:"'.htmlentities($user).'",'.
		    'password:"'.htmlentities($pass).'"}'; 	 
    ?>
  <img src="jukeboxes/quickbox/qr/php/qr_img.php?s=5&d=<?php echo urlencode($barcode) ?>"/>
  </div>
<div id="box" style="position:absolute;top:0px;left:-200px">
<?php
$nifty = 'jukeboxes/quickbox/niftyplayer.swf?as=0'; 
?>
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="165" height="38" id="np" align="">
  <param name=movie value="<?php echo $nifty; ?>">
  <param name=quality value=high>
  <param name=bgcolor value=#FFFFFF>
  <embed src="<?php echo $nifty; ?>" quality=high bgcolor=#FFFFFF width="165" height="38" name="np" align="" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
  </embed>
</object>
</div>

<!--  <a href="javascript:niftyplayer('np').playToggle()">Play/Pause</a>
-->

    
    <?php
    /* 
    require_once(dirname(__FILE__).'/class.php');
$jbArr = jzJukebox::getJbArr();
foreach ($jbArr as $jid=>$jb) {
  if ($jid == $id) {
    $_REQUEST['jb_playwhere']='jukebox';
    $_REQUEST['jb_id'] = $jid;
    break;
  }
}
    
    require_once(dirname(__FILE__).'/../jukebox.php'); 
    */
?>

  </body>
</html>
