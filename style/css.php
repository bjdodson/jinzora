<?php   define('JZ_SECURE_ACCESS','true');
	header('Content-type: text/css');
	if (!defined(jz_font_size)){
		define("jz_font_size","11px");	
	}
	if (isset($_REQUEST['root_dir']) || isset($_REQUEST['skin'])) {
		die();
	}
	require_once(dirname(__FILE__).'/../system.php');
?>

#jz_images img.icon{
	background: url('<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/icons.gif?v=<?php echo $version; ?>') no-repeat;
	width: 17px;
	margin: 0px;
	vertical-align: bottom;
}       
#jz_images img.icon-play{background-position: 0px 0px;}
#jz_images img.icon-random{background-position: -16px 0px;}
#jz_images img.icon-download{background-position: -32px 0px;}
#jz_images img.icon-prefs{background-position: -48px 0px;}
#jz_images img.icon-home{background-position: -64px 0px;}
#jz_images img.icon-clip{background-position: -80px 0px;}
#jz_images img.icon-check{background-position: -96px 0px;}
#jz_images img.icon-nocheck{background-position: -112px 0px;}
#jz_images img.icon-add{background-position: -128px 0px;}
#jz_images img.icon-more{background-position: -144px 0px;}
#jz_images img.icon-tools{background-position: -160px 0px;}
#jz_images img.icon-slimpop{background-position: -176px 0px;}
#jz_images img.icon-play-disabled{background-position: -192px 0px;}
#jz_images img.icon-random-disabled{background-position: -208px 0px;}
#jz_images img.icon-download-disabled{background-position: -224px 0px;}
#jz_images img.icon-tiny-play{background-position: -240px 0px; width: 11px; height: 12px; vertical-align: top;}
#jz_images img.icon-tiny-play-dis{background-position: -250px 0px; width: 11px; height: 12px; vertical-align: top;}
#jz_images img.icon-tiny-info{background-position: -260px 0px; width: 11px; height: 12px; vertical-align: top;}
#jz_images img.icon-playlist{background-position: -272px 0px;}
#jz_images img.icon-rate{background-position: -288px 0px;}
#jz_images img.icon-discuss{background-position: -304px 0px;}
#jz_images img.icon-discuss-disabled{background-position: -320px 0px;}
#jz_images img.icon-email{background-position: -336px 0px;}
#jz_images img.icon-pause{background-position: -352px 0px;}
#jz_images img.icon-stop{background-position: -368px 0px;}
#jz_images img.icon-previous{background-position: -384px 0px;}
#jz_images img.icon-next{background-position: -400px 0px;}
#jz_images img.icon-clear{background-position: -416px 0px;}
#jz_images img.icon-repeat{background-position: -432px 0px;}
#jz_images img.icon-norepeat{background-position: -448px 0px;}
#jz_images img.icon-rss{background-position: -464px 0px; width: 23px; height: 12px; vertical-align: top;}
#jz_images img.icon-purchase{background-position: -490px 0px;}
#jz_images img.icon-podcast{background-position: -506px 0px;}
#jz_images img.icon-uparrow{background-position: -522px 0px; width: 15px;}
#jz_images img.icon-star-h-e{background-position: -538px 0px; width: 9px; height: 16px; vertical-align: bottom;}
#jz_images img.icon-star-f-e{background-position: -548px 0px; width: 9px; height: 16px; vertical-align: bottom;}
#jz_images img.icon-star-r{background-position: -559px 0px; width: 3px; height: 16px; vertical-align: bottom;}
#jz_images img.icon-star-l{background-position: -563px 0px; width: 3px; height: 16px; vertical-align: bottom;}
#jz_images img.icon-star-f{background-position: -567px 0px; width: 9px; height: 16px; vertical-align: bottom;}
#jz_images img.icon-up{background-position: -577px 0px;}
#jz_images img.icon-down{background-position: -593px 0px;}
#jz_images img.icon-browse{background-position: -609px 0px;}

#jz_images img.icon-art{background-position: -16px -27px;}
#jz_images img.icon-media{background-position: -32px -27px;}
#jz_images img.icon-new{background-position: -48px -27px; height:14px;}
#jz_images img.icon-user{background-position: -64px -27px;}

.jz_main_block_topl {
	background: url('<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/icons.gif') no-repeat;
	width: 5px;
	margin: 0px;
	vertical-align: bottom;
	background-position: 0px -27px;
}

.jz_main_block_topm {
	background: url('<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/sect-header-middle.gif') repeat;
}

.jz_main_block_topr {
	background: url('<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/icons.gif') no-repeat;
	width: 5px;
	margin: 0px;
	vertical-align: bottom;
	background-position: -7px -27px;
}




#slickLeftNav{
	float: left;
}
#slickLeftBlockPad{
	padding:3px 3px 3px 3px;
}
#slickLeftBlock {
	border: 1px solid #474747; 
	background-color: <?php echo jz_bg_color; ?>;
	width: 147px;
	font-size: 10px;
}
#slickLeftBlockHeader {
	font-weight: bold;
	font-size: 12px;
}
#slickLeftBlockSpace {
	padding: 2px 2px 2px 2px;
}
#slickMainBlockBody {
	padding: 4px 4px 4px 4px;
	border: 1px solid #474747; 
	background-color: <?php echo jz_bg_color; ?>;
}
#slickMainBlockHeaderRight{
	float: right;
}
.spiffy{
	display:block;
}
.spiffy *{
	display:block;
	height:1px;
	overflow:hidden;
	background:#5a5a5a;
}
.spiffy1{
	border-right:1px solid #5a5a5a;
	padding-right:1px;
	margin-right:3px;
	border-left:1px solid #5a5a5a;
	padding-left:1px;
	margin-left:3px;
	background:#5a5a5a;
}
.spiffy2{
	border-right:1px solid #5a5a5a;
	border-left:1px solid #5a5a5a;
	padding:0px 1px;
	background:#5a5a5a;
	margin:0px 1px;
}
.spiffy3{
	border-right:1px solid #5a5a5a;
	border-left:1px solid #5a5a5a;
	margin:0px 1px;
}
.spiffy4{
	border-right:1px solid #5a5a5a;
	border-left:1px solid #5a5a5a;
}
.spiffy5{
	border-right:1px solid #5a5a5a;
	border-left:1px solid #5a5a5a;
}
.spiffy_content{
	padding:0px 5px 5px;
	background:#5a5a5a;
	color: #FFFFFF;
}
#j_b_pad {
	width:16px;
	height:14px;
	padding: 5px 0px 0px 16px;
}

#j_pref_b {
	background:url(<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/prefs.gif) no-repeat left;
	width:16px;
	height:14px;
	padding: 5px 0px 0px 16px;
	border:0px;
}
#j_login_b {
	background:url(<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/login.gif) no-repeat left;
	width:16px;
	height:14px;
	padding: 5px 0px 0px 16px;
	border:0px;
}
#j_p_b {
	background:url(<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/play.gif) no-repeat left;
	width:16px;
	height:14px;
	padding: 5px 0px 0px 16px;
	border:0px;
}
#j_p_r_b {
	background:url(<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/random.gif) no-repeat left;
	width:16px;
	height:14px;
	padding: 5px 0px 0px 16px;
	border:0px;
}
#j_d_b {
	background:url(<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/download.gif) no-repeat left;
	width:16px;
	height:14px;
	padding: 5px 0px 0px 16px;
	border:0px;
}
#j_p_b_t {
	background:url(<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/tiny-play.gif) no-repeat left;
	width:10px;
	height:9px;
	padding: 0px 0px 0px 10px;
	border:0px;
}
body {
	background: <?php echo jz_pg_bg_color; ?>;
	margin: 0px; 0px; 0px; 1px;
	font-family: Verdana, Sans;
	font-size: 10px;
	color: <?php echo jz_font_color; ?>;
}
.headertextshadow { position: relative; left: 1px; top: 1px; color: <?php echo jz_pg_bg_color; ?>; }
.headertext { position: absolute; left: -1px; top: -1px; color: <?php echo jz_link_color; ?>;}
.and_head1 { background-color:<?php echo jz_bg_color; ?>; }
.and_head2 { background-color:<?php echo jz_pg_bg_color; ?>; }
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
.jz_left_iblock_inner {
	background: <?php echo jz_bg_color; ?>
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
	background-color: <?php echo jz_bg_color; ?>;
}
td {
	font-family: Verdana, Sans;
	font-size: <?php echo jz_font_size; ?>;
	color: <?php echo jz_headers; ?>;
}
form { 
	display:inline;
}
a {
	text-decoration: none;
	font-size: <?php echo jz_font_size; ?>;
}
a:link { 
	color: <?php echo jz_link_color; ?>;
}
a:visited { 
	color: <?php echo jz_link_color; ?>;
}
a:hover {
	color: <?php echo jz_link_hover_color; ?>;
}
.jz_select {
	background: <?php echo jz_select_bg; ?>;
	color: <?php echo jz_select_font_color; ?>;
	font-size: 11px;
	border-width: 1px;
	margin-bottom:2px;
}
.jz_submit {
	border: 1px solid black;
	background: <?php echo jz_submit_bg; ?>;
	color: <?php echo jz_submit_font_color; ?>;
	font-size: 11px;
	border-width: 1px;
}
.jz_input {
	font-family: Verdana, Sans;
	color: <?php echo jz_input_font_color; ?>;
	background-color: <?php echo jz_input_bg; ?>;
	font-size: 11px;
	border-width: 1px;
	margin-bottom: 2px;
}
.jz_header_table {
	border-collapse: collapse;
}
.jz_header_table_outer {
	font-size: 10px;
	color: <?php echo jz_font_color; ?>;
	background-color: <?php echo jz_bg_color; ?>;
	border: 1px solid black;
	padding:10px;
	margin-top:10px;
}
.jz_header_table_td {
	font-size: 11px;
}
.jz_header_table_href:link {
	font-size: 10px;
}
.jz_header_table_href {
	font-size: 11px;
}
.jz_headerTitle {
	font-size: 11px;
	font-weight: bold;
}
.jz_footer_table {
	text-decoration: none;
	font-size: 11px;
	color: #000000;
	background-color: <?php echo jz_bg_color; ?>;
	border: 1px solid black;
}
.jz_artistDesc {
	font-size: 11px;
	color:<?php echo jz_font_color; ?>;
}
.jz_artist_album {
	font-size: 11px;
}
.jz_row1 { 
	background-color: <?php echo jz_row1; ?>;
	border-left: 1px solid black;
	border-right: 1px solid black;
}
.jz_row2 { 
	background-color: <?php echo jz_row2; ?>; 
	border-left: 1px solid black;
	border-right: 1px solid black;
}
.jz_sm_font {
	font-size: 11px;
}
.jz_col_table_main {
	font-size: 11px;
	color: #000000;
}
.jz_col_table {
	height: 25px;
}
.jz_col_table_tr {
	font-size: 11px;
	color: #000000;
}
.jz_artist_table {
	font-size: 11px;
	color: #000000;
}
.jz_artist_table_td {
	font-size: 11px;
	color: #000000;
}
.jz_album_cover_table_href {
	font-size: 11px;
	line-height: 150%;
}
.jz_track_table {
	font-size: 11px;
	color: black;
	border-collapse: collapse;
}
.jz_track_table_songs_td {
	border-top: 1px solid black;
	border-bottom: 1px solid black;
	color: <?php echo jz_font_color; ?>;
}
a.jz_random_art_block:link {
	font-size: 10px;
}
a.jz_random_art_block:visited {
	font-size: 10px;
}
a.jz_random_art_block:hover {
	font-size: 10px;
}
.jz_random_art_block img { border: 1px solid black; }
a.jz_track_table_songs_href:link {
	color: <?php echo jz_link_color; ?>;
}
a.jz_track_table_songs_href:visited {
	color: <?php echo jz_link_color; ?>;
}
a.jz_track_table_songs_href:hover {
	color: <?php echo jz_link_hover_color; ?>;
}

.jz_track_album_table_td {
	font-size: 11px;
	color: black;
	border-collapse: collapse;
}
.jz_album_cover_picture {
	border: 1px solid black;
}
.jz_album_cover_picture img { 
	border: 1px solid black; 
}
.full{
	width: 100%
}
.jz_nj_block_body{ 
		font-family: Arial, Helvetica, sans-serif; 
		color:<?php echo jz_font_color; ?>;
		border-left: <?php echo jz_default_border; ?>;
		border-right: <?php echo jz_default_border; ?>;
		border-bottom: <?php echo jz_default_border; ?>;
		background-color:<?php echo jz_bg_color; ?>;
	}