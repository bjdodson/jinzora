<?php
$display = new jzDisplay();
$display->preheader();
if (checkPlayback() == "jukebox") {
  echo '<div id="jukebox">';
  jzBlock('jukebox');
  echo '</div>';
}
?>