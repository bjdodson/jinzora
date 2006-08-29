<?php 
	define('JZ_SECURE_ACCESS','true');
	
	if (!(isset($define_only) && $define_only)) {
	  include_once('../../system.php');		
	  include_once('../../settings.php');
    }
	$skin = "slicksilver";

	// Now let's set the colors for this thing
	define("jz_pg_bg_color","#FFFFFF");
	define("jz_bg_color","#e2e2e2");
	define("jz_fg_color","#e2e2e2");
	define("jz_font_color","#666666");
	define("jz_link_color","#000000");
	define("jz_link_hover_color","#CC3333");
	define("jz_headers","#000000");
	define("jz_row1","#FFFFFF");
	define("jz_row2","#FFFFFF");
	define("jz_select_bg","#FFFFFF");
	define("jz_select_font_color","#000000");
	define("jz_submit_bg","#FFFFFF");
	define("jz_submit_font_color","#000000");
	define("jz_input_bg","#FFFFFF");
	define("jz_input_font_color","#000000");	
	define("jz_default_table_color","#e2e2e2");
	define("jz_default_border","1px solid black");
	
	// Do they want the whole stylesheet
	if (isset($define_only)){if ($define_only){return;}}
	
	include_once("../css.php");
?>
	body {
		background-image: url("<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/tile.gif");
    background-repeat: repeat;
	}