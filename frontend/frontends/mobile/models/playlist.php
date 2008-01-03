<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');

function doTemplate($node) {
  global $jzUSER;
  $display = &new jzDisplay();
  $smarty = mobileSmarty();
  $smarty->assign('Play',word('Play'));
  $smarty->assign('Shuffle',word('Shuffle'));

  if (!isset($_REQUEST['playlist']) && !isset($_SESSION['jz_playlist_queue'])) {
    jzTemplate($smart,'playlist');
    return;
  }

  $elements = array();
  if (isset($_REQUEST['playlist'])) {
    $pl = $jzUSER->loadPlaylist($_REQUEST['playlist']);
  } else {
    $pl = $jzUSER->loadPlaylist($_SESSION['jz_playlist_queue']);
  }

  $smarty->assign('plName',$pl->getName());
  $smarty->assign('openPlayTag',$display->getOpenPlayTag($pl));
  $smarty->assign('openShuffleTag',$display->getOpenPlayTag($pl,true));
  $smarty->assign('isStatic',($pl->getPLType() == 'static'));

  $list = $pl->getList();
  foreach ($list as $el) {
    $elements[] = array('name'=>$el->getName(),
			'openPlayTag'=>$display->getOpenPlayTag($el));
  }
  $smarty->assign('elements',$elements);
 
  jzTemplate($smarty,'playlist');
}

?>