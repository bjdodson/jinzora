<?php 
	define('JZ_SECURE_ACCESS','true');
	
	if (!(isset($define_only) && $define_only)) {
	  include_once('../../system.php');		
	  include_once('../../settings.php');
    }
	$skin = "bluegray";	

	// Now let's set the colors for this thing
	define("jz_pg_bg_color","#CDCDCD");	
	define("jz_bg_color","#5273AD");
	define("jz_fg_color","#5273AD");
	define("jz_font_color","#000000");
	define("jz_link_color","#000000");
	define("jz_link_hover_color","#0DE103");
	define("jz_headers","#000000");	
	define("jz_row1","#5273AD");
	define("jz_row2","#D4D4D4");	
	define("jz_select_bg","#CDCDCD");
	define("jz_select_font_color","#000000");
	define("jz_submit_bg","#CDCDCD");
	define("jz_submit_font_color","#000000");
	define("jz_input_bg","#CDCDCD");
	define("jz_input_font_color","#000000");
	define("jz_default_table_color","#A5B6D5");
	define("jz_default_border","1px solid black");	
	define("jz_default_table_color","#000000");
	define("jz_default_border","1px solid black");
	
	// Do they want the whole stylesheet
	if (isset($define_only)){if ($define_only){return;}}
	
	include_once("../css.php");
?>