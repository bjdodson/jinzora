<?php
	define('JZ_SECURE_ACCESS','true');
		
	if (!(isset($define_only) && $define_only)) {
	  include_once('../../system.php');		
	  include_once('../../settings.php');
    }
	$skin = "slamp";
	
	// Now let's set the colors for this thing
	define("jz_pg_bg_color","#B4BAC6"); // The primary background color for the page
	define("jz_bg_color","#082863"); // The backgroud color for items in the page
	define("jz_fg_color","#082863"); // The forground color for items in the page
	define("jz_font_color","#FFFFFF"); // The primary font color
	define("jz_link_color","#FFFF00"); // The primary link color
	define("jz_link_hover_color","#FFFFFF"); // The color for hovering fonts
	define("jz_headers","#FFFFFF"); // The color for fonts in the blocks
	define("jz_select_bg","#DDDDDD");
	define("jz_select_font_color","#082863");
	define("jz_submit_bg","#DDDDDD");
	define("jz_submit_font_color","#082863");
	define("jz_input_bg","#DDDDDD");
	define("jz_input_font_color","#082863");
	define("jz_row1","#082863");
	define("jz_row2","#2B4C8A");
	define("jz_default_table_color","#082863");
	define("jz_default_border","1px solid black");
	
	// Do they want the whole stylesheet
	if (isset($define_only)){if ($define_only){return;}}	
	
	include_once(dirname(__FILE__)."/../css.php");
	include_once(dirname(__FILE__)."/../icon_css.php");
?>
