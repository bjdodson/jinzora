<?php 
	if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
?>
<style>
/* This affects just about everything... */
body {
	background-color: <?php echo jz_pg_bg_color; ?>;
	margin: 0 0 0 0;
	font-family: Verdana, Sans;
	font-size: 13px;
	color: <?php echo jz_font_color; ?>;
}
a {
	text-decoration: none;
	font-size: 13px;
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
.jz_row1 { 
	background-color: <?php echo jz_row1; ?>;
        padding: 8 8 8 8;
}
.jz_row2 { 
	background-color: <?php echo jz_row2; ?>; 
        padding: 8 8 8 8;
}
.jz_track_table {
	font-size: 13px;
	color: black;
	border-collapse: collapse;
}
</style>