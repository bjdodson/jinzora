<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
* Sitewide settings editor
*
* @author Ben Dodson
* @since 2/2/05
* @version 2/2/05
*
**/

global $css, $jzSERVICES;

// Let's setup the css for the page
include_once ($css);

// Now let's open the service for this
$jzSERVICES->loadService("players", $_GET['embed_player']);
$jzSERVICES->displayPlayer();

?>
