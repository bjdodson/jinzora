<?php
	header('Content-type: text/css');
	if (!defined(jz_font_size)){
		define("jz_font_size","11px");	
	}
?>
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