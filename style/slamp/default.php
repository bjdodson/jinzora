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
	
	// custom-sized icons below.
        // this skin uses individual .gifs for each icon,
        // but the preferred method is to use a common icons.gif file, which improves load times.
        $icon_base = $root_dir . '/style/' . $skin;
?>

#jz_images img.icon{
	background: url('<?php echo $icon_base; ?>/icons.gif?v=<?php echo $version; ?>') no-repeat;
	width: 18px;
	margin: 0px;
	vertical-align: bottom;
        cursor:pointer;
}       

#jz_images img.icon-play{
  background: url('<?php echo $icon_base; ?>/play.gif') no-repeat;
}

#jz_images img.icon-random{
  background: url('<?php echo $icon_base; ?>/random.gif') no-repeat;
}

#jz_images img.icon-download{
  background: url('<?php echo $icon_base; ?>/download.gif') no-repeat;
}

#jz_images img.icon-prefs{
  background: url('<?php echo $icon_base ?>/edit.gif') no-repeat;
}

#jz_images img.icon-home{
  background: url('<?php echo $icon_base; ?>/home.gif') no-repeat;
}

#jz_images img.icon-clip{
  background: url('<?php echo $icon_base; ?>/play-clip.gif') no-repeat;
}

#jz_images img.icon-check{
  background: url('<?php echo $icon_base; ?>/check.gif') no-repeat;
}

#jz_images img.icon-nocheck{
  background: url('<?php echo $icon_base; ?>/check-none.gif') no-repeat;
}

#jz_images img.icon-add{
  background: url('<?php echo $icon_base; ?>/add.gif') no-repeat;
}

#jz_images img.icon-more{
  background: url('<?php echo $icon_base; ?>/more.gif') no-repeat;
}

#jz_images img.icon-tools{
  background: url('<?php echo $icon_base; ?>/edit.gif') no-repeat;
}

#jz_images img.icon-slimpop{
  background: url('<?php echo $icon_base; ?>/slim-pop.gif') no-repeat;
}

#jz_images img.icon-play-disabled{
  background: url('<?php echo $icon_base; ?>/play-disabled.gif') no-repeat;
}

#jz_images img.icon-random-disabled{
  background: url('<?php echo $icon_base; ?>/random-disabled.gif') no-repeat;
}

#jz_images img.icon-download-disabled{
  background: url('<?php echo $icon_base; ?>/download-disabled.gif') no-repeat;
}

#jz_images img.icon-tiny-play{
  background: url('<?php echo $icon_base; ?>/tiny-play.gif') no-repeat;
}

#jz_images img.icon-tiny-play-dis{
  background: url('<?php echo $icon_base; ?>/tiny-play-disabled.gif') no-repeat;
}

#jz_images img.icon-tiny-info{
  background: url('<?php echo $icon_base; ?>/tiny-info.gif') no-repeat;
}

#jz_images img.icon-playlist{
  background: url('<?php echo $icon_base; ?>/playlist.gif') no-repeat;
}

#jz_images img.icon-rate{
  background: url('<?php echo $icon_base; ?>/rate.gif') no-repeat;
}

#jz_images img.icon-discuss{
  background: url('<?php echo $icon_base; ?>/discuss.gif') no-repeat;
}

#jz_images img.icon-discuss-disabled{
  background: url('<?php echo $icon_base; ?>/discuss-disabled.gif') no-repeat;
}

#jz_images img.icon-email{
  background: url('<?php echo $icon_base; ?>/email.gif') no-repeat;
}

#jz_images img.icon-pause{
  background: url('<?php echo $icon_base; ?>/pause.gif') no-repeat;
}

#jz_images img.icon-stop{
  background: url('<?php echo $icon_base; ?>/stop.gif') no-repeat;
}

#jz_images img.icon-previous{
  background: url('<?php echo $icon_base; ?>/previous.gif') no-repeat;
}

#jz_images img.icon-next{
  background: url('<?php echo $icon_base; ?>/next.gif') no-repeat;
}

#jz_images img.icon-clear{
  background: url('<?php echo $icon_base; ?>/clear.gif') no-repeat;
}

#jz_images img.icon-repeat{
  background: url('<?php echo $icon_base; ?>/jb_repeat.gif') no-repeat;
}

#jz_images img.icon-norepeat{
  background: url('<?php echo $icon_base; ?>/jb_no_repeat.gif') no-repeat;
}

#jz_images img.icon-rss{
  background: url('<?php echo $icon_base; ?>/rss.gif') no-repeat;
  width:23px;
}

#jz_images img.icon-purchase{
  background: url('<?php echo $icon_base; ?>/dollar.gif') no-repeat;
}

#jz_images img.icon-podcast{
  background: url('<?php echo $icon_base; ?>/podcast.gif') no-repeat;
}

#jz_images img.icon-uparrow{
  background: url('<?php echo $icon_base; ?>/up-arrow.gif') no-repeat;
}

#jz_images img.icon-star-h-e{
  background: url('<?php echo $icon_base; ?>/star-half-empty.gif') no-repeat;
}

#jz_images img.icon-star-f-e{
  background: url('<?php echo $icon_base; ?>/atar-full-empty.gif') no-repeat;
}

#jz_images img.icon-star-r{
  background: url('<?php echo $icon_base; ?>/star-right.gif') no-repeat;
}

#jz_images img.icon-star-l{
  background: url('<?php echo $icon_base; ?>/star-left.gif') no-repeat;
}

#jz_images img.icon-star-f{
  background: url('<?php echo $icon_base; ?>/star-full.gif') no-repeat;
}

#jz_images img.icon-up{
  background: url('<?php echo $icon_base; ?>/arrow-up.gif') no-repeat;
}

#jz_images img.icon-down{
  background: url('<?php echo $icon_base; ?>/arrow-down.gif') no-repeat;
}

#jz_images img.icon-browse{
  background: url('<?php echo $icon_base; ?>/browse.gif') no-repeat;
}

#jz_images img.icon-art{
  background: url('<?php echo $icon_base; ?>/art.gif') no-repeat;
}

#jz_images img.icon-media{
  background: url('<?php echo $icon_base; ?>/addmedia.gif') no-repeat;
}

#jz_images img.icon-new{
  background: url('<?php echo $icon_base; ?>/new.gif') no-repeat;
  position: absolute;
  margin-left: 4px; 
}

#jz_images img.icon-user{
  background: url('<?php echo $icon_base; ?>/user.gif') no-repeat;
}

.jz_main_block_topl {
	background: url('<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/sect-header-left.gif') no-repeat;
	width: 5px;
	margin: 0px;
	vertical-align: bottom;
}

.jz_main_block_topm {
	background: url('<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/sect-header-middle.gif') repeat;
}

.jz_main_block_topr {
	background: url('<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/sect-header-right.gif') no-repeat;
	width: 5px;
	margin: 0px;
	vertical-align: bottom;
}