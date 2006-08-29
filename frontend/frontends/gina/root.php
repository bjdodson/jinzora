<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');

function drawPage($node) {
  global $random_albums, $include_path;

  $display = new jzDisplay();
  $blocks = new jzBlocks();

  echo '<br>';
  $blocks->blockBodyOpen();

  if (isset($_GET['letter'])) {
    $nodes = $node->getAlphabetical($_GET['letter'], 'nodes', 2);
  } else {
    $nodes = $node->getSubNodes('nodes');
  }
  $tracks = $node->getSubNodes('tracks');
  
  // Now let's display the site description
$news = $blocks->siteNews($node);
if ($news <> ""){
	echo "<br><center>". $news. "<center>";
}

  $cols = 4;
  $curCol = 1;
  $per_col = round(sizeof($nodes) / $cols);
  $i = 0;
  $percent = round(100 / $cols);
  echo "<table cellpadding=\"4\" width=\"100%\"><tr class=\"jz_col_table_tr\"><td class=\"jz_col_table\" valign=\"top\" width=\"${percent}%\"><table>";

  foreach ($nodes as $el) {
    if ($i == $per_col && $curCol != $cols) {
      $curCol++;
      echo "</table></td><td class=\"jz_col_table\" valign=\"top\" width=\"${percent}%\"><table width=\"100%\">";
      $i = 0;
    }
    echo "<tr><td>";
    $display->link($el);
    echo "</td></tr>";
    $i++;
  }
  echo "</table></td></tr></table>";
  
  if (!isset($_GET['letter'])) {
    $url = array();
    $url['letter'] = '#';
    
    echo "| <a href=\"" . urlize($url) . "\">#</a>";
    for ($let = 'A'; $let != 'Z'; $let++) {
      $url['letter'] = $let;
      echo " | <a href=\"" . urlize($url) . "\">".$let."</a>";
    }
    $url['letter'] = "*";
    echo " | <a href=\"".urlize($url) . "\">ALL</a> |";
    echo "<br>";
    if (sizeof($tracks) > 0) {
      $blocks->trackTable($tracks,false,true);
    }
    
    if ($random_albums <> "0"){
      echo "<br>";
      $blocks->randomAlbums($node, $node->getName());
    }
  }
  $blocks->blockBodyClose();
  echo "<br>";
}
  
