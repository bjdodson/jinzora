<?php  if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
$fontsize = '12';
?>
<style>
/* This affects just about everything... */
body,div {
	background-color: <?php echo jz_pg_bg_color; ?>;
	margin: 0 0 0 0;
	font-family: Verdana, Sans;
	font-size: <?php echo $fontsize; ?>px;
	color: <?php echo jz_font_color; ?>;
}
a {
	text-decoration: none;
        font-size: <?php echo $fontsize; ?>px;
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
        border:0;
	font-size:<?php echo $fontsize; ?>px;
}
.jz_row2 { 
	background-color: <?php echo jz_row2; ?>; 
        border:0;
	font-size:<?php echo $fontsize; ?>px;
}
.jz_track_table {
  font-size: <?php echo $fontsize; ?>px;
	color: black;
	border-collapse: collapse;
}

td {
 color: <?php echo jz_headers; ?>;
}

h1 {
  font-size: <?php echo$fontsize+2; ?>px;
  text-align:center;
  margin-top:20px;
  text-decoration:underline;
}

h2 {
  font-size: <?php echo$fontsize+1; ?>px;
  text-align:left;
  margin: 10px 0px 5px 10px;
  text-decoration:underline;
}

div.headerLinks {
  width:auto;
  text-align:center;
  margin:4px 0px 4px 0px;
}

div.headerLinks a {
  padding: 1px;
  border: 1px solid;
  margin: 0px 1px 0px 1px;
  text-align: center;
}

div.headerLinks span {
  white-space:nowrap;border:1px solid yellow;
}
</style>