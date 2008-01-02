<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');

function controller($node) {
  global $jzUSER;
  $display = &new jzDisplay();
  $smarty = smartySetup();
  $smarty->assign('templates',dirname(__FILE__).'/../templates');
  $smarty->assign('play',word('Play'));
  $smarty->assign('shuffle',word('Shuffle'));

  $sm_lists = array();

  $pl = $jzUSER->loadPlaylist("session");
  if ($pl->length() > 0) {
    $sm_lists[] = array('name'=>word("Quick List"),
			'play'=>'blank',
			'shuffle'=>'blank');
  }

  $lists = $jzUSER->listPlaylists("all");
  foreach ($lists as $id => $plName) {
    $sm_lists[] = array('name'=>$plName,
			'play'=>'blank',
			'shuffle'=>'blank');
  }
  $smarty->assign('playlists',$sm_lists);
 
  jzTemplate($smarty,'lists');
}

?>