<?php
global $random_art_size;
$display = new jzDisplay();
$sm = smartySetup();
if (!isset($node) || $random_albums <= 0) {
  return;
}
$art = array();
$artArray = $node->getSubNodes("nodes",distanceTo("album",$node),true,$random_albums,true);
foreach ($artArray as $al) {
  $art[] = array (
		  'name' => $al->getName(),
		  'link' => urlize(array('jz_path' => $al->getPath("string"))),
		  'playlink' => $display->playlink($al,'Play',false,false,true),
		  'art' => $display->returnImage($al->getMainArt($random_art_size . 'x' . $random_art_size),$al->getName(),$random_art_size,$random_art_size,"fixed")
		  );
}

$sm->assign('albums', $art);
jzTemplate($sm,'randomAlbums');

?>