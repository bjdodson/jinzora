<?php
$display = new jzDisplay();
$display->preheader();
if (checkPlayback() == "jukebox") {
  echo '<div id="jukebox">';
  include(jzBlock('jukebox'));
  echo '</div>';
}
?>