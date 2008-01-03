<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');

/* playback handled in backend.php :: handleJukeboxVars() */
function doTemplate($node) {
  global $jbArr,$jzUSER;

  $display = &new jzDisplay();
  $smarty = mobileSmarty();
  $smarty->assign('Playback',word('Playback'));
  $smarty->assign('SendToDevice',word('Send to Device:'));
  $smarty->assign('AddToPlaylist',word('Add to Playlist:'));

  $path = $node->getPath("String");

  $url = array('jz_path'=>$path,'page'=>'browse');
  $pbt = array();
  $playlists = array();

  $url['jz_player'] = 'stream';
  $url['jz_player_type'] = 'stream';
  $selected = (!actionIsQueue() && checkPlayback() == 'stream');
  $pbt[] = array('label' => word('Stream media'), 'url'=>urlize($url), 'selected'=>$selected);

  $url['jz_player_type'] = 'jukebox';
  if (isset($jbArr) && is_array($jbArr)) {
    for ($i = 0; $i < sizeof($jbArr); $i++) {
      $url['jz_player'] = $i;
      $url['jz_player_type'] = 'jukebox';
      $selected = (!actionIsQueue() && checkPlayback() == 'jukebox' && $_SESSION['jb_id'] == $i);
      $pbt[] = array('label' => word('Send to %s', $jbArr[$i]['description']), 'url' => urlize($url), 'selected'=>$selected);
    }
  }

  $smarty->assign('devices',$pbt);
  
  /* playlists */
  $url['jz_player_type'] = 'playlist';
  $url['jz_player'] = 'session';
  $selected = (actionIsQueue() && $_SESSION['jz_playlist_queue'] == 'session');
  $playlists[] = array('label' => word('Quick List'), 'url' => urlize($url),'selected'=>$selected);
  

  $lists = $jzUSER->listPlaylists("static");
  foreach ($lists as $id => $plName) {
    $url['jz_player'] = $id;
    $selected = (actionIsQueue() && $_SESSION['jz_playlist_queue'] == $id);
    $playlists[] = array('label'=> $plName, 'url' => urlize($url), 'selected'=>$selected);
  }

  $smarty->assign('playlists',$playlists);
  
  $url['jz_player'] = 'new';
  $smarty->assign('newList',array('href'=>'#',
				  'onclick'=>"window.location='".urlize($url)."'.concat('&playlistname='.concat(document.getElementById('playlistname').value)); return true;",
				  'name'=>word('My Playlist'),
				  'inputID'=>word('playlistname'),
				  'label'=>word('New list:'),
				  'selected'=>false));
  jzTemplate($smarty,'settings');
}

?>