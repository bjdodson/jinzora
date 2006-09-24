<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
* Sends a transcoded file bundle
*
* @author Ross Carlson
* @since 06/10/05
* @version 06/10/05
*
**/
global $include_path, $node;

// Now let's include the libraries
include_once ($include_path . 'lib/jzcomp.lib.php');

// Now we have an array of files let's use them to create the download
sendFileBundle(unserialize($_GET['jz_files']), $node->getName());

exit ();
?>
