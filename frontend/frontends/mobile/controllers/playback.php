<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');

/* playback handled in backend.php :: handleJukeboxVars() */
function controller($node) {
  global $jbArr,$jzUSER;

  $display = &new jzDisplay();
  $smarty = smartySetup();
  $smarty->assign('templates',dirname(__FILE__).'/../templates');

  $path = $node->getPath("String");

  $url = array('jz_path'=>$path,'page'=>'browse');
  $pbt = array();

  $url['jz_player'] = 'stream';
  $url['jz_player_type'] = 'stream';
  $pbt[] = array('label' => word('Stream media'), 'url'=>urlize($url));

  $url['jz_player_type'] = 'jukebox';
  if (isset($jbArr) && is_array($jbArr)) {
    for ($i = 0; $i < sizeof($jbArr); $i++) {
      $url['jz_player'] = $i;
      $url['jz_player_type'] = 'jukebox';
      $pbt[] = array('label' => word('Send to %s', $jbArr[$i]['description']), 'url' => urlize($url));
    }
  }

  $url['jz_player_type'] = 'playlist';
  $url['jz_player'] = 'session';
  $pbt[] = array('label' => word('Add to Quick List'), 'url' => urlize($url));
  

  $lists = $jzUSER->listPlaylists("static");
  foreach ($lists as $id => $plName) {
    $url['jz_player'] = $id;
    $pbt[] = array('label'=> word('Add to playlist "%s"', $plName), 'url' => urlize($url));
  }

  $smarty->assign('players',$pbt);
  
  $url['jz_player'] = 'new';
  $smarty->assign('newList',array('href'=>'#',
				  'onclick'=>"window.location='".urlize($url)."'.concat('&playlistname='.concat(document.getElementById('playlistname').value)); return true;",
				  'name'=>word('My Playlist'),
				  'inputID'=>word('playlistname'),
				  'label'=>word('Add to new list:')));
  jzTemplate($smarty,'playback');
}

?>