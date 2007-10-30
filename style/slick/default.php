<?php 
	define('JZ_SECURE_ACCESS','true');

    if (!(isset($define_only) && $define_only)) {
	  include_once('../../system.php');		
	  include_once('../../settings.php');
    }
    $skin = "slick";	
	
	// Now let's set the colors for this thing
	define("jz_pg_bg_color","#000000");
	define("jz_bg_color","#242424");
	define("jz_fg_color","#2C2C2C");
	define("jz_font_color","#FFFFFF");
	define("jz_link_color","#ffffff");
	define("jz_link_hover_color","#0DE103");
	define("jz_headers","#ffffff");
	define("jz_row1","#151515");
	define("jz_row2","#000000");
	define("jz_select_bg","#1d1d1d");
	define("jz_select_font_color","#FFFFFF");
	define("jz_submit_bg","#1d1d1d");
	define("jz_submit_font_color","#FFFFFF");
	define("jz_input_bg","#1d1d1d");
	define("jz_input_font_color","#FFFFFF");
	define("jz_default_table_color","#424242");
	define("jz_default_border","1px solid black");	

	// Do they want the whole stylesheet
	if (isset($define_only)){if ($define_only){return;}}
	
	include_once("../css.php");
?>