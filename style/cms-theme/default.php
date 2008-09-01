<?php 
	defined('JZ_SECURE_ACCESS') or define('JZ_SECURE_ACCESS','true');
	
	if (!(isset($define_only) && $define_only)) {
	  include_once('../../system.php');		
	  include_once('../../settings.php');
    }	
	$skin = "cms-theme";
	
	// Now let's set the colors for this thing
	define("jz_pg_bg_color","#FFFFFF"); // The primary background color for the page
	define("jz_bg_color","#FFFFFF"); // The backgroud color for items in the page
	define("jz_fg_color","#FFFFFF"); // The forground color for items in the page
	define("jz_font_color","#000000"); // The primary font color
	define("jz_link_color","#3C3E4D"); // The primary link color
	define("jz_link_hover_color","#000000"); // The color for hovering fonts
	define("jz_headers","#FFFFFF"); // The color for fonts in the blocks
	
	// The below items relate to form elements
	define("jz_select_bg","#EFEFCC");
	define("jz_select_font_color","#000000");
	define("jz_submit_bg","#EFEFCC");
	define("jz_submit_font_color","#000000");
	define("jz_input_bg","#EFEFCC");
	define("jz_input_font_color","#000000");
	
	// The rows in the tracks table and other tables
	define("jz_row1","#CCCC99");
	define("jz_row2","#EFEFCC");
	define("jz_default_table_color","#E0E09F");
	define("jz_default_border","1px solid black");
	
	// Do they want the whole stylesheet
	if (isset($define_only)){if ($define_only){return;}}
	
	header('Content-type: text/css');
?>
/* This affects just about everything... */
body {
	font-size: 10px;
}

.headertextshadow { position: relative; left: 1px; top: 1px; color: <?php echo jz_pg_bg_color; ?>; }
.headertext { position: absolute; left: -1px; top: -1px; color: <?php echo jz_link_color; ?>;}

.jz_artistDesc {
	font-size: 11px;
	color:<?php echo jz_font_color; ?>;
}

/* These are new for the slick frontend */
.jz_left_iblock_topl {
	background: url("<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/inner-block-top-left.gif"); 
	background-repeat:no-repeat;
}
.jz_left_iblock_topm {
	background: url("<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/inner-block-top-middle.gif"); 
}
.jz_left_iblock_topr {
	background: url("<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/inner-block-top-right.gif"); 
	background-repeat:no-repeat;
}
.jz_left_iblock_left {
	background: url("<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/inner-block-left.gif"); 
	background-repeat:repeat;
}
.jz_left_iblock_right {
	background: url("<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/inner-block-right.gif"); 
	background-repeat:repeat;
}
.jz_left_iblock_botl {
	background: url("<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/inner-block-bottom-left.gif"); 
	background-repeat:no-repeat;
}
.jz_left_iblock_botm {
	background: url("<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/inner-block-bottom-middle.gif"); 
}
.jz_left_iblock_botr {
	background: url("<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/inner-block-bottom-right.gif"); 
	background-repeat:no-repeat;
}
.jz_block_td {
	border: 1px solid #474747; 
}
.jz_main_block_topl {
	background: url("<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/sect-header-left.gif"); 
	background-repeat:no-repeat;
	background-position: left;
}
.jz_main_block_topm {
	background: url("<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/sect-header-middle.gif"); 
	background-repeat: repeat;
}
.jz_main_block_topr {
	background: url("<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/sect-header-right.gif"); 
	background-repeat:no-repeat;
	background-position: right;
}

/* This is for The forms */
form { 
	display:inline; 
}

.jz_select_sm1 {
	font-size: 9px;
	width: 40px;
}
.jz_select_sm2 {
	font-size: 9px;
	width: 70px;
}

/* This The The style for The main table in The header */
.jz_header_table {
	text-decoration: none;
	font-size: 12px;
}
/* This is The style for The header rows */
.jz_header_table_tr {
}

.jz_header_table_outer {
	font-size: 18px;
	border: 1px solid #828282;
	border-collapse: collapse;
	padding:10px;
	margin-top:10px;
}

/* This is The style for The header columns */
.jz_header_table_td {
	font-size: 10px;
}
/* This is The style of The links in The header */
.jz_header_table_href {

}
/* This is the style for the text of the Genre/Artist/Album in the top left of the header */
.jz_headerTitle {
	font-size: 14px;
	font-weight: bold;
}
/* This is the main table in the footer */
.jz_footer_table {
	text-decoration: none;
	font-size: 12px;
	border: 1px solid #828282;
	border-collapse: collapse;

}
.jz_footer_table_td {
	font-size: 10px;	
}


/* This is The main table style for The colum style pages (like Genre or Artist) */
.jz_col_table_main {

}
/* This is the inter tables where the colums are actually displayed */
.jz_col_table {
	height: 25px; 
	border-right: 1px dotted #FFFFFF;
}
/* This is The table rows in The main colum style pages (like Genre or Artist) */
.jz_col_table_tr {
	
}
/* This is The table details in The main colum style pages (like Genre or Artist) */
.jz_col_table_td {
	
}
/* This is The links in The main colum style pages (like Genre or Artist) */
.jz_col_table_href {

}
/* This is The main table style when viewing The artists page (one above The album) */
.jz_artist_table {

}
/* This is The row style when viewing The artists page (one above The album) */
.jz_artist_table_tr {

}
/* This is The table detail when viewing The artists page (one above The album) */
.jz_artist_table_td {

}
/* This is The links in The main colum style pages (like Genre or Artist) */
.jz_artist_table_href {
	text-decoration: none;
	font-size: 12px;
}
/* This is for The main table That holds The album covers */
.jz_album_cover_table {
	text-decoration: none;
	font-size: 12px;
}
/* This is The rows in The table That holds The album covers */
.jz_album_cover_table_tr {
	
}
/* This is The columns in The album cover table */
.jz_album_cover_table_td {

}
/* This is The links on The names of The albums in The album cover table */
.jz_album_cover_table_href {
	text-decoration: none;
	font-size: 10px;
}
/* This is the style around the ablum covers themselves */
.jz_album_cover_picture {
	border: 1px solid black;	
}
/* This is The style for The table on the tracks page that holds the album info */
.jz_track_album_table {
	text-decoration: none;
	font-size: 12px;
}
/* This is The style for the rows in the table on the tracks page that holds the album info */
.jz_track_album_table_tr {
	
}
/* This is The style for the cells in the table on the tracks page that holds the album info */
.jz_track_album_table_td {
	color: #000000;
}
/* This is The style for the links in the table on the tracks page that holds the album info */
.jz_track_album_table_href {
	text-decoration: none;
	font-size: 12px;
}
/* This is the table that is wrapped around each track - so each track is its own table and row */
.jz_track_table_each_track {

}
/* This is The style for The table That holds all The tracks */
.jz_track_table {
	
}
/* This is The row for The table That holds all The tracks */
.jz_track_table_tr {
	
}
/* This is The colum for The table That holds all The tracks */
.jz_track_table_td {
	
}
/* This is The link for The items above The tracks */
.jz_track_table_href {
	text-decoration: none;
	font-size: 12px;
}
/* These are The colums where The actual tracks are displayed */
.jz_track_table_songs_td {
	font-size: 12px;
}
/* This is for The actually links on The tracks Themselves */
.jz_track_table_songs_href {
	text-decoration: none;
	font-size: 12px;
}

.full
{
	width: 100%
}
