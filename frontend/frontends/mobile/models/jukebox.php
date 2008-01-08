<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');

function doTemplate($node) {
  global $jzUSER;

  $display = &new jzDisplay();
  $smarty = mobileSmarty();

  $jb = new jzJukebox();
  if (!$jb->connect()) {
    //jzTemplate($smarty,'jukeboxError');
    echo 'Error connecting to jukebox.';
    return;
  }

  $smarty->assign('Play',word('Play'));
  $smarty->assign('Pause',word('Pause'));
  $smarty->assign('Stop',word('Stop'));
  $smarty->assign('Previous',word('Previous'));
  $smarty->assign('Next',word('Next'));
  $smarty->assign('Shuffle',word('Shuffle'));
  $smarty->assign('Clear',word('Clear'));  


  /* buttons */
  if (checkPermission($jzUSER, "jukebox_admin")) {
    $func = $jb->jbAbilities();

    if ($func['playbutton']) {
      $smarty->assign('openPlayTag',$display->getOpenJukeboxActionTag('play'));
    }
    if ($func['pausebutton']) {
      $smarty->assign('openPauseTag',$display->getOpenJukeboxActionTag('pause'));
    }
    if ($func['stopbutton']) {
      $smarty->assign('openStopTag',$display->getOpenJukeboxActionTag('stop'));
    }
    if ($func['prevbutton']) {
      $smarty->assign('openPrevTag',$display->getOpenJukeboxActionTag('previous'));
    }
    if ($func['nextbutton']) {
      $smarty->assign('openNextTag',$display->getOpenJukeboxActionTag('next'));
    }
    if ($func['shufflebutton']) {
      $smarty->assign('openShuffleTag',$display->getOpenJukeboxActionTag('random_play'));
    }
    if ($func['clearbutton']) {
      $smarty->assign('openClearTag',$display->getOpenJukeboxActionTag('clear'));
    }

    if ($func['volume']) {
      $smarty->assign('Volume',word('Volume:'));
      $smarty->assign('volumeSteps',range(0,100,5));
      $vol = $_SESSION['jz_jbvol-' . $_SESSION['jb_id']];
      if (!isset($vol) || !is_numeric($vol)) $vol = 0;
      $smarty->assign('currentVolume',$vol);
    }

    if ($func['addtype']) {
      /* how to add media */
      $smarty->assign('whereAdd',word('Add media:'));

      function jbHREF($type) {
	return "javascript:sendJukeboxRequest('addwhere','$type');";
      }

      $set = array();
      $set[] = array('href'=>jbHREF('current'),'label'=>'After current track','selected'=>($_SESSION['jb-addtype'] == "current"));
      $set[] = array('href'=>jbHREF('begin'),'label'=>'At beginning of playlist','selected'=>($_SESSION['jb-addtype'] == "begin"));
      $set[] = array('href'=>jbHREF('end'),'label'=>'At end of playlist','selected'=>($_SESSION['jb-addtype'] == "end"));
      $set[] = array('href'=>jbHREF('replace'),'label'=>'Replace current playlist','selected'=>($_SESSION['jb-addtype'] == "replace"));

      $smarty->assign('addTypes',$set);
    }
  }

  jzTemplate($smarty,'jukebox');
}

?>