<?php
	// constants:
	// JZ_FRONTEND_OVERRIDE
	// JZ_STYLE_OVERRIDE
	// JZ_LANGUAGE_OVERRIDE
	
	// todo: jukebox
	
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	
	if (false !== stristr($useragent, 'Nintendo Wii')) {
		define('JZ_FRONTEND_OVERRIDE','wiijay');
	}
	
?>