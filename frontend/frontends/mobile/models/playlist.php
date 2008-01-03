<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');

function doTemplate($node) {
  global $jzUSER;
  $display = &new jzDisplay();
  $smarty = mobileSmarty();

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
  $list = $pl->getList();
  foreach ($list as $el) {
    $elements[] = array('name'=>$el->getName());
  }
  $smarty->assign('elements',$elements);
 
  jzTemplate($smarty,'playlist');
}

?>