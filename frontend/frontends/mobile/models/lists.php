<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');

function doTemplate($node) {
  global $jzUSER,$display,$chart_size;
  $display = &new jzDisplay();
  $smarty = mobileSmarty();
  $smarty->assign('Play',word('Play'));
  $smarty->assign('Shuffle',word('Shuffle'));
  
  /** Playlists **/
  $smarty->assign('Playlists',word('Playlists'));

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



  /** Charts **/
  /**
   * array of titles and lists */
  $root = new jzMediaNode();
  $charts = array();


  /* recently added albums */
  $chart = array();
  $chart['title'] = word('New Albums');
  $entries = array();
  $list = $root->getRecentlyAdded('nodes',distanceTo('album'),$chart_size);
  for ($i = 0; $i < sizeof($list); $i++) {
    $entries[] = array('name'=>$list[$i]->getName(),
		       'link'=>urlize(array('jz_path'=>$list[$i]->getPath("String"))),
		       'openPlayTag'=>$display->getOpenPlayTag($list[$i]));
  }
  $chart['entries'] = $entries;
  $charts[] = $chart;


  /* recently played albums */
  $chart = array();
  $chart['title'] = word('Recently Played Albums');
  $entries = array();
  $list = $root->getRecentlyPlayed('nodes',distanceTo('album'),$chart_size);
  for ($i = 0; $i < sizeof($list); $i++) {
    $entries[] = array('name'=>$list[$i]->getName(),
		       'link'=>urlize(array('jz_path'=>$list[$i]->getPath("String"))),
		       'openPlayTag'=>$display->getOpenPlayTag($list[$i]));
  }
  $chart['entries'] = $entries;
  $charts[] = $chart;

  

  
  $smarty->assign('charts',$charts);


  jzTemplate($smarty,'lists');
}

?>