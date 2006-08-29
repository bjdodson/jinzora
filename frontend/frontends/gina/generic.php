<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');

function drawPage($node) {
  global $random_albums, $include_path;

  $display = new jzDisplay();
  $blocks = new jzBlocks();

  $nodes = $node->getSubNodes('nodes');
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
  echo "<table cellpadding=\"4\" width=\"100%\"><tr class=\"jz_col_table_tr\"><td class=\"jz_col_table\" valign=\"top\" width=\"${percent}%\"><table width=\"100%\"><tr><td>";

  foreach ($nodes as $el) {
    if ($i == $per_col && $curCol != $cols) {
      $curCol++;
      echo "</td></tr></table></td><td class=\"jz_col_table\" valign=\"top\" width=\"${percent}%\"><table width=\"100%\"><tr><td>";
      $i = 0;
    }
    $display->link($el);
    echo "</td></tr><tr><td>";
    $i++;
  }
  echo "</td></tr></table></td></tr></table>";
  echo "<br>";

  echo "<br>";
  if (sizeof($tracks) > 0) {
    $blocks->trackTable($tracks,false,true);
  }

  if ($random_albums <> "0"){
    include_once($include_path. "frontend/blocks/random-albums.php");
    echo "<br>";
    $blocks->randomAlbums(&$node, $node->getName());
    echo "<br>";
  }

}

