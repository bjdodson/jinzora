<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');

function controller($node) {
  $display = &new jzDisplay();
  $smarty = smartySetup();
  $smarty->assign('templates',dirname(__FILE__).'/../templates');
 
  jzTemplate($smarty,'jukebox');
}

?>