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
  }

  jzTemplate($smarty,'jukebox');
}

?>