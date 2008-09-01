<?php 
	defined('JZ_SECURE_ACCESS') or define('JZ_SECURE_ACCESS','true');

    if (!(isset($define_only) && $define_only)) {
	  include_once('../../system.php');		
	  include_once('../../settings.php');
    }
	$skin = "sandstone";	
	
	// Now let's set the colors for this thing
	define("jz_pg_bg_color","#9999CC"); // The primary background color for the page
	define("jz_bg_color","#CCCC99"); // The backgroud color for items in the page
	define("jz_fg_color","#CCCC99"); // The forground color for items in the page
	define("jz_font_color","#333333"); // The primary font color
	define("jz_link_color","#66669B"); // The primary link color
	define("jz_link_hover_color","#000000"); // The color for hovering fonts
	define("jz_headers","#000000"); // The color for fonts in the blocks
	
	// The below items relate to form elements
	define("jz_select_bg","#EFEFCC");
	define("jz_select_font_color","#000000");
	define("jz_submit_bg","#EFEFCC");
	define("jz_submit_font_color","#000000");
	define("jz_input_bg","#EFEFCC");
	define("jz_input_font_color","#000000");
	
	// The rows in the tracks table and other tables
	define("jz_row1","#EFEFD0");
	define("jz_row2","#EFEFEF");
	define("jz_default_table_color","#9999CC");
	define("jz_default_border","1px solid black");
	
	// Do they want the whole stylesheet
	if (isset($define_only) && $define_only){return;}
       
	include_once(dirname(__FILE__)."/../css.php");
	include_once(dirname(__FILE__)."/../icon_css.php");
?>