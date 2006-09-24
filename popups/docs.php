<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
* Shows the documentation system
* 
* @author Ross Carlson
* @version 01/19/05
* @since 01/19/05
*/

global $root_dir, $jz_lang_file;

// Let's refresh
echo '<META HTTP-EQUIV=Refresh CONTENT="0; URL=' . $root_dir . "/docs/" . $jz_lang_file . "/index.html" . '">';
?>
