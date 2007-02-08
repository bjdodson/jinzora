<?php
	// constants:
	// JZ_FRONTEND_OVERRIDE
	// JZ_STYLE_OVERRIDE
	// JZ_LANGUAGE_OVERRIDE
	
	// todo: jukebox
	
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	
	// This is for the Nintendo Wii
	if (false !== stristr($useragent, 'Nintendo Wii')) {
		define('JZ_FRONTEND_OVERRIDE','wiijay');
	}
	
	// This is for Windows Mobile Devices
	if (false !== stristr($useragent, 'Windows CE')) {
		define('JZ_FRONTEND_OVERRIDE','slimzora');
		define('JZ_STYLE_OVERRIDE','sandstone');
		global $jzSERVICES;
		$jzSERVICES->loadService('players','ptunes');
	}
		
?>