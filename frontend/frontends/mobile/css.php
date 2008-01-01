<?php  if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
$fontsize = '12px';
?>
<style>
/* This affects just about everything... */
body {
	background-color: <?php echo jz_pg_bg_color; ?>;
	margin: 0 0 0 0;
	font-family: Verdana, Sans;
	font-size: <?php echo $fontsize; ?>
	color: <?php echo jz_font_color; ?>;
}
a {
	text-decoration: none;
        font-size: <?php echo $fontsize; ?>
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
}
.jz_row2 { 
	background-color: <?php echo jz_row2; ?>; 
}
.jz_track_table {
        font-size: <?php echo $fontsize; ?>
	color: black;
	border-collapse: collapse;
}
</style>