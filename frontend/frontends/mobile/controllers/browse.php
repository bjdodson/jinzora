<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');

function controller($node) {
  $display = &new jzDisplay();
  $smarty = mobileSmarty();

  $breadcrumbs = array();
  if (isset($_REQUEST['jz_path'])) {
    $me = $node;
    while ($me->getLevel() > 0) {
      $breadcrumbs[] = array("name" => $me->getName(),"link" => urlize(array('jz_path'=>$me->getPath("String"))));
      $me = $me->getParent();
    }
  }
  
  $breadcrumbs[] = array("name"=>word("Home"),"link"=>urlize(array()));
  $smarty->assign('breadcrumbs',$breadcrumbs);

  if (actionIsQueue()) {
    $smarty->assign('Play',word('Add'));    
  } else {
    $smarty->assign('Play',word('Play'));
  }
  
  $myNodes = $node->getSubNodes('nodes');
  sortElements($myNodes);
  $myTracks = $node->getSubNodes('tracks');
  
  $nodes = array();
  for ($i = 0; $i < sizeof($myNodes); $i++) {
    $e = $myNodes[$i];
    $nodes[] = smartyNode($myNodes[$i]);
  }
  $smarty->assign('nodes',$nodes);

  $tracks = array();
  for ($i = 0; $i < sizeof($myTracks); $i++) {
    $e = $myNodes[$i];
    $tracks[] = smartyTrack($myTracks[$i]);
  }
  $smarty->assign('tracks',$tracks);
  
  jzTemplate($smarty,'browse');
}

function smartyNode($e) {
  global $compare_ignores_the;
  static  $anchor = 'A';

  $display = new jzDisplay();
  $arr = array();
  $arr['name'] = $e->getName();
  $arr['link'] = urlize(array('jz_path'=>$e->getPath("String")));
    
  if ($e->getPType() == "album" || $e->getPType == "disk") {
    if (isset($_SESSION['jz_playlist_queue'])) {
      $arr['openPlayTag'] = $display->getOpenAddToListTag($e);
    } else {
      $arr['openPlayTag'] = $display->getOpenPlayTag($e);
    }
  } else {
    if (actionIsQueue()) {
      $arr['openPlayTag'] = $display->getOpenAddToListTag($e);
    } else {
      $arr['openPlayTag'] = $display->getOpenPlayTag($e,true,50);
    }
  }
  
  $compName = $arr['name'];
  if ($compare_ignores_the == "true" && strtoupper(substr($compName,0,4)) == 'THE ') {
    $compName = substr($compName,4);
  }
  $compName = trim($compName);

  $anchors = array();
  if ($i == 0) {
    $anchors[]='anchor_NUM';
    $first = false;
  }
  while (strlen($anchor) == 1 && ($anchor < strtoupper($compName) || $i == sizeof($items)-1)) {
    $anchors[] = 'anchor_'.$anchor++;
  }
  $arr['anchors'] = $anchors;

  return $arr;
}

function smartyTrack($e) {
  $display = new jzDisplay();

  // meta  
  $arr = $e->getMeta();
  if (!is_array($arr)) $arr = array();

  $arr['length'] = convertSecMins($arr['length']);
  $arr['name'] = $e->getName();
  if (actionIsQueue()) {
    $arr['openPlayTag'] = $display->getOpenAddToListTag($e);
  } else {
    $arr['openPlayTag'] = $display->getOpenPlayTag($e);
  }
  return $arr;
}

?>