<?php 
	defined('JZ_SECURE_ACCESS') or define('JZ_SECURE_ACCESS','true');
	
	if (!(isset($define_only) && $define_only)) {
	  include_once('../../system.php');		
	  include_once('../../settings.php');
    }	
	$skin = "vampire";	
	
	// Now let's set the colors for this thing
	define("jz_pg_bg_color","#000000");	
	define("jz_bg_color","#570000");
	define("jz_fg_color","#570000");
	define("jz_font_color","#D10303");
	define("jz_link_color","#D10303");
	define("jz_link_hover_color","#D10303");
	define("jz_headers","#D10303");	
	define("jz_row1","#952C2C");
	define("jz_row2","#3C3C3C");	
	define("jz_select_bg","#310101");
	define("jz_select_font_color","#FFFFFF");
	define("jz_submit_bg","#310101");
	define("jz_submit_font_color","#FFFFFF");
	define("jz_input_bg","#310101");
	define("jz_input_font_color","#FFFFFF");
	define("jz_default_table_color","#952C2C");
	define("jz_default_border","1px solid black");
	define("jz_default_table_color","#000000");
	define("jz_default_border","1px solid black");
	
	// Do they want the whole stylesheet
	if (isset($define_only)){if ($define_only){return;}}
	
	include_once(dirname(__FILE__)."/../css.php");
	include_once(dirname(__FILE__)."/../icon_css.php");
?>