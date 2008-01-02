<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');

function controller($node) {
  global $jzUSER,$display;
  $display = &new jzDisplay();
  $smarty = smartySetup();
  $smarty->assign('templates',dirname(__FILE__).'/../templates');
  $smarty->assign('play',word('Play'));
  $smarty->assign('shuffle',word('Shuffle'));

  $sm_lists = array();

  $l = $jzUSER->loadPlaylist("session");
  if ($l->length() > 0) {
    
    $sm_lists[] = array('name'=>word("Quick List"),
			'openPlayTag'=>$display->getOpenPlayTag($l),
			'isStatic'=>true,
			'openShuffleTag'=>$display->getOpenPlayTag($l,true));
    
  }

  $lists = $jzUSER->listPlaylists("static") + $jzUSER->listPlaylists("dynamic"); // use "all" to mix ordering
  foreach ($lists as $id => $plName) {
    $l = $jzUSER->loadPlaylist($id);
    $static = ($l->getPLType() == 'static') ? true : false;

    $sm_lists[] = array('name'=>$plName,
			'openPlayTag'=>$display->getOpenPlayTag($l),
			'isStatic'=>$static,
			'openShuffleTag'=>$display->getOpenPlayTag($l,true));
  }
  $smarty->assign('playlists',$sm_lists);
 
  jzTemplate($smarty,'lists');
}

?>