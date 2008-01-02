<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');

function controller($node) {
  $display = &new jzDisplay();
  $smarty = mobileSmarty();
 
  jzTemplate($smarty,'jukebox');
}

?>