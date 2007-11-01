<?php 
	if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');	
	
	// Now let's actually define the icons
	if (!isset($image_dir)){
		$image_dir = $include_path. 'style/'. $skin. '/';
	}
	$main_img_dir = $include_path. 'style/images/';
	$disabled_dir = $root_dir. '/style/images/';	
	
	// Now let's create the blank icons
	if ($jinzora_skin == 'cms-theme'){
		$dis_dir = $disabled_dir;
	} else {
		$dis_dir = $image_dir;
	}	
	$img_begin = '<span id="jz_images"><img src="'. $image_dir. 'blank.gif" border=0 class="icon ';
	
	
	/**
	 * Use of these constants is not recommended.
	 * Instead, use the icon function:
	 * icon('play', array('option' => 'value', ...) )
	 */
	 
	$img_play 			= $img_begin. 'icon-play" alt="'. word("Play"). '" title="'. word("Play"). '"></span>'; 
	$img_random_play 	= $img_begin. 'icon-random" alt="'. word("Play Random"). '" title="'. word("Play Random"). '"></span>';
	$img_download 		= $img_begin. 'icon-download" alt="'. word("Download"). '" title="'. word("Download"). '"></span>';
	$img_prefs 			= $img_begin. 'icon-prefs" alt="'. word("Preferences"). '" title="'. word("Prefrences"). '"></span>';
	$img_home 			= $img_begin. 'icon-home" alt="'. word("Return Home"). '" title="'. word("Return Home"). '"></span>';
	$img_clip 			= $img_begin. 'icon-clip" alt="'. word("Play clip"). '" title="'. word("Play clip"). '"></span>';
	$img_check 			= $img_begin. 'icon-check" alt="'. word("Check all"). '" title="'. word("Check all"). '"></span>';
	$img_check_none 	= $img_begin. 'icon-nocheck" alt="'. word("Check none"). '" title="'. word("Check none"). '"></span>';
	$img_add 			= $img_begin. 'icon-add" alt="'. word("Add to"). '" title="'. word("Add to"). '"></span>';
	$img_more 			= $img_begin. 'icon-more" alt="'. word("More"). '" title="'. word("More"). '"></span>';
	$img_tools 			= $img_begin. 'icon-tools" alt="'. word("Admin Tools"). '" title="'. word("Admin Tools"). '"></span>';
	$img_slim_pop 		= $img_begin. 'icon-slimpop" alt=Slimzora title=Slimzora></span>';
	$img_play_dis 		= $img_begin. 'icon-play-disabled" alt="'. word("Play"). '" title="'. word("Play"). '"></span>';
	$img_random_play_dis= $img_begin. 'icon-random-disabled" alt="'. word("Play Random"). '" title="'. word("Play Random"). '"></span>';
	$img_download_dis 	= $img_begin. 'icon-download-disabled" alt="'. word("Download"). '" title="'. word("Download"). '"></span>';	
	$img_tiny_play 		= $img_begin. 'icon-tiny-play" alt="'. word("Play"). '" title="'. word("Play"). '"></span>';
	$img_tiny_play_dis  = $img_begin. 'icon-tiny-play-dis" alt="'. word("Play"). '" title="'. word("Play"). '"></span>';
	$img_tiny_info 		= $img_begin. 'icon-tiny-info" alt="'. word("Information"). '" title="'. word("Information"). '"></span>';
	$img_playlist 		= $img_begin. 'icon-playlist" alt="'. word("Playlist"). '" title="'. word("Playlist"). '"></span>';
	$img_rate 			= $img_begin. 'icon-rate" alt="'. word("Rate"). '" title="'. word("Rate"). '"></span>';
	$img_discuss 		= $img_begin. 'icon-discuss" alt="'. word("Discuss"). '" title="'. word("Discuss"). '"></span>';
	$img_discuss_dis 	= $img_begin. 'icon-discuss-disabled" alt="'. word("Discuss"). '" title="'. word("Discuss"). '"></span>';
	$img_email			= $img_begin. 'icon-email" alt="'. word("Share via email"). '" title="'. word("Share via email"). '"></span>';	
	$img_rss 			= $img_begin. 'icon-rss" alt="'. word("RSS Feed"). '" title="'. word("RSS Feed"). '"></span>';	
	$img_dollar 		= $img_begin. 'icon-purchase" alt="'. word("Purchase"). '" title="'. word("Purchase"). '"></span>';
	$img_podcast 		= $img_begin. 'icon-podcast" alt="'. word("Subscribe to Podcast"). '" title="'. word("Subscribe to Podcast"). '"></span>';
	$img_up_arrow 		= $img_begin. 'icon-uparrow" alt="'. word("Up level"). '" title="'. word("Up level"). '"></span>';
	$img_browse 		= $img_begin. 'icon-browse" alt="'. word("Browse"). '" title="'. word("Browse"). '"></span>';

	// These are the jukebox images
	$img_jb_play 		= $img_begin. 'icon-play" alt="'. word("Play"). '" title="'. word("Play"). '"></span>';
	$img_jb_pause 		= $img_begin. 'icon-pause" alt="'. word("Pause"). '" title="'. word("Pause"). '"></span>';
	$img_jb_stop 		= $img_begin. 'icon-stop" alt="'. word("Stop"). '" title="'. word("Stop"). '"></span>';
	$img_jb_previous 	= $img_begin. 'icon-previous" alt="'. word("Previous"). '" title="'. word("Previous"). '"></span>';
	$img_jb_next 		= $img_begin. 'icon-next" alt="'. word("Next"). '" title="'. word("Next"). '"></span>';
	$img_jb_random_play = $img_begin. 'icon-random" alt="'. word("Play Random"). '" title="'. word("Play Random"). '"></span>';
	$img_jb_clear 		= $img_begin. 'icon-clear" alt="'. word("Clear"). '" title="'. word("Clear"). '"></span>';
	$img_jb_repeat 		= $img_begin. 'icon-repeat" alt="'. word("Repeat"). '" title="'. word("Repeat"). '"></span>';
	$img_jb_no_repeat 	= $img_begin. 'icon-norepeat" alt="'. word("No Repeat"). '" title="'. word("No Repeat"). '"></span>';
	$img_arrow_up 		= $img_begin. 'icon-up" alt="'. word("Move Up"). '" title="'. word("Move Up"). '"></span>';
	$img_arrow_down 	= $img_begin. 'icon-down" alt="'. word("Move Down"). '" title="'. word("Move Down"). '"></span>';
	
	// These are for the stars	
	$img_star_half_empty .= $img_begin. 'icon-star-h-e" alt="'. word("Rate"). '" title="'. word("Rate"). '"></span>';
	$img_star_full_empty .= $img_begin. 'icon-star-f-e" alt="'. word("Rate"). '" title="'. word("Rate"). '"></span>';
	$img_star_right 	 .= $img_begin. 'icon-star-r" alt="'. word("Rate"). '" title="'. word("Rate"). '"></span>';
	$img_star_left 		 .= $img_begin. 'icon-star-l" alt="'. word("Rate"). '" title="'. word("Rate"). '"></span>';
	$img_star_full 		 .= $img_begin. 'icon-star-f" alt="'. word("Rate"). '" title="'. word("Rate"). '"></span>';

	// This are various random icons
	$img_blank 			= '<img src="'. $image_dir. 'blank.gif" border=0>';
	$img_login 			= '<img src="'. $image_dir. 'login.gif" border=0 alt="'. word("Login in/out"). '" title="'. word("Login in/out"). '">';
	$img_delete 		= '<img src="'. $image_dir. 'delete.gif" border=0 alt="'. word("Delete"). '" title="'. word("Delete"). '">';
	$img_move 			= '<img src="'. $image_dir. 'move.gif" border=0 alt="'. word("Move Item"). '" title="'. word("Move Item"). '">';
	$img_star 			= '<img src="'. $image_dir. 'star.gif" border=0 >';
	$img_clear 			= '<img src="'. $image_dir. 'clear.gif" border=0 alt="'. word("Clear"). '" title="'. word("Clear"). '">';
	$img_fav_track 		= '<img src="'. $image_dir. 'rate.gif" border=0 alt="'. word("Add to favorites"). '" title="'. word("Add to favorites"). '">';
	$img_lofi 			= '<img src="'. $image_dir. 'play-lofi.gif" border=0 alt="'. word("Play Lofi"). '" title="'. word("Play Lofi"). '">';
        $img_sm_logo 		= '<img src="'. $root_dir. '/style/images/powered-by-small.gif" border=0 alt="'. $this_pgm. $version. '" title="'. $this_pgm. $version. '">';	
	$img_slimzora 		= '<img src="'. $root_dir. '/style/images/slimzora.gif" border=0 alt="'. $this_pgm. $version. '" title="'. $this_pgm. $version. '">';	
	$img_add_fav 		= '<img src="'. $image_dir. 'add-fav.gif" border=0 alt="'. word("Add to Favorites"). '" title="'. word("Add to Favorites"). '">';
	$img_pause 			= '<img src="'. $image_dir. 'pause.gif" border=0 alt="'. word("Pause"). '" title="'. word("Pause"). '">';
	$img_stop 			= '<img src="'. $image_dir. 'stop.gif" border=0 alt="'. word("Stop"). '" title="'. word("Stop"). '">';
	$img_previous 		= '<img src="'. $image_dir. 'previous.gif" border=0 alt="'. word("Previous"). '" title="'. word("Previous"). '">';
	$img_next 			= '<img src="'. $image_dir. 'next.gif" border=0 alt="'. word("Next"). '" title="'. word("Next"). '">';

	// Set up some raw images:
	$raw_img_play 		= $image_dir.'play.gif';
	$raw_img_random_play = $image_dir.'random.gif';
	$raw_img_download 	= $image_dir.'download.gif';
	$raw_img_add 		= $image_dir.'newplaylist.gif';
	$raw_img_play_clear = $image_dir.'clear.gif';
	$raw_img_new 		= $image_dir.'new.gif';

	$img_add_dis 		= '<img src="'. $dis_dir. 'add-disabled.gif" border=0 alt="'. word("Add to"). '" title="'. word("Add to"). '">';
	$img_delete_dis 	= '<img src="'. $dis_dir. 'delete-disabled.gif" border=0 alt="'. word("Delete"). '" title="'. word("Delete"). '">';
	$img_more_dis 		= '<img src="'. $dis_dir. 'more-disabled.gif" border=0 alt="'. word("More"). '" title="'. word("More"). '">';
	$img_move_dis 		= '<img src="'. $dis_dir. 'move-disabled.gif" border=0 alt="'. word("Move Item"). '" title="'. word("Move Item"). '">';
	$img_up_arrow_dis 	= '<img src="'. $dis_dir. 'up-arrow-disabled.gif" border=0 alt="'. word("Up level"). '" title="'. word("Up level"). '">';
	$img_playlist_dis 	= '<img src="'. $dis_dir. 'playlist-disabled.gif" border=0 alt="'. word("Playlist"). '" title="'. word("Playlist"). '">';

/**
 * Returns an icon with the given arguments.
 * Arguments is an array. Valid keys include
 * anything that can be put in an <img> tag.
 * the key 'literal' means paste the exact content
 * of the value.
 *
 * @author Ben Dodson
 * @since 10/31/2007
 **/
function icon($type, $args = array()) {
  global $img_begin;

  $str = $img_begin;
  $str .= 'icon-' . $type . '"';

  foreach ($args as $opt => $val) {
    if ($opt == 'literal') {
      $str .= $val;
    } else {
      $str .= ' ' . $opt . '= "' . htmlentities($val) . '"';
    }
  }

  return $str . '></span>';
}
?>