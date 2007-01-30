<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
// This file is for general-use client side ajax scripts.
// You can put frontend-specific AJAX functions in:
// /frontend/frontends/{FRONTEND}/ajax_scripts.php and
// /frontend/frontends/{FRONTEND}/ajax.php
global $jzSERVICES;
?>
<script>
var playback = '<?php echo checkPlayback(); ?>';
var streamto = '<?php echo checkPlayback(true); ?>';
function playbackLink(url) {  
  if (playback == 'stream') {
  	return true;
    //window.open(url,'_self');
  } else if (playback == 'jukebox') {
    <?php if (!defined('NO_AJAX_JUKEBOX')) { ?>
    ajax_direct_call(url,sendJukeboxRequest_cb);
    <?php } else { ?>
    window.open(url,'_SELF');
    <?php } ?> 
  } 
  <?php if (checkPlayback(true) == 'embedded') { ?>
  else if (playback == 'embedded') {
    win = openMediaPlayer(url,<?php echo $jzSERVICES->returnPlayerWidth(); ?>,<?php echo $jzSERVICES->returnPlayerHeight(); ?>);
  }
  <?php } ?>
  return false;
}

function updatePlaylist_cb(a) {
  alert('<?php echo word('Playlist updated.'); ?>');
  obj = document.getElementById("playlistDisplay");
  if (obj != false) {
    obj.innerHTML = a;
  }
}

function submitPlaybackForm(button,url) {  
  form = button.form;
  document.pressedVal = null;

  if (button.type == "button" || button.type == "submit" || button.type == "image") {
    document.pressed = button.name;
    document.pressedVal = button.value;
  } else {
    document.pressed = null;
  }

  if (button.name == '<?php echo jz_encode('addList'); ?>' || button.name == '<?php echo jz_encode('addPath'); ?>') {
    ajax_submit_form(form,url,updatePlaylist_cb);
    return false;
  }

  if (playback == 'stream') {
    return true;
  } else if (playback == 'jukebox') {
    <?php if (!defined('NO_AJAX_JUKEBOX')) { ?> 
    ajax_submit_form(form,url,sendJukeboxRequest_cb);
    return false;
    <?php } else { ?>
    return true; 
    <?php } ?>
  }
  <?php if (checkPlayback(true) == 'embedded') { ?>
  else if (playback == 'embedded') {
    win = openMediaPlayer('',<?php echo $jzSERVICES->returnPlayerWidth(); ?>,<?php echo $jzSERVICES->returnPlayerHeight(); ?>);
    form.target='embeddedPlayer';
    return true;
  }
  <?php } ?>
}

function searchKeywords(f,url) {
  <?php global $keyword_radio, $keyword_random, $keyword_play; ?>
    var str = f.search_query.value;
    str = str.toUpperCase();

    var key1 = '<?php echo $keyword_radio; ?>';
    key1 = key1.toUpperCase();
    var key2 = '<?php echo $keyword_play; ?>';
    key2 = key2.toUpperCase();
    var key3 = '<?php echo $keyword_random; ?>';
    key3 = key3.toUpperCase();

    if (!(str.match(key1) || str.match(key2) || str.match(key3))) {
    	f.target='_self';
    	return true;
    }
    
    if (playback == 'jukebox') {
      ajax_submit_form(f,url,sendJukeboxRequest_cb);
       return false;
    }
    <?php if (checkPlayback(true) == 'embedded') { ?>
  else { // if (playback == 'embedded') {
    win = openMediaPlayer('',<?php echo $jzSERVICES->returnPlayerWidth(); ?>,<?php echo $jzSERVICES->returnPlayerHeight(); ?>);
    f.target='embeddedPlayer';
    return true;
  }
  <?php } else { ?>
  else {
    // proceed as usual.
    return true;
  }
    <?php } ?>
}

function maindiv(url) {
  ajax_direct_call(url,maindiv_cb);
	callBreadcrumbs();
	//callAlbumAlbumBlock();
}

function maindiv_cb(a) {
  document.getElementById("mainDiv").innerHTML = a;
}

function currentInfo(mysid, update) {
  x_returnCurrentInfo(mysid, update, currentInfo_cb);
}

function currentInfo_cb(a) {
  if (a != "") {
    document.getElementById("currentInfo").innerHTML = a;
  }
}

function nothing(a) {}

function setResample(v) {
  x_setResample(v,nothing);
  return false;
}

function alert_cb(a) { alert(a); }
</script>