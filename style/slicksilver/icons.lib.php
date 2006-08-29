<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *     
	* JINZORA | Web-based Media Streamer 
	*
	* Jinzora is a Web-based media streamer, primarily desgined to stream MP3s 
	* (but can be used for any media file that can stream from HTTP). 
	* Jinzora can be integrated into a CMS site, run as a standalone application, 
	* or integrated into any PHP website.  It is released under the GNU GPL.
	* 
	* Jinzora Author:
	* Ross Carlson: ross@jasbone.com
	* http://www.jinzora.org
	* Documentation: http://www.jinzora.org/docs	
	* Support: http://www.jinzora.org/forum
	* Downloads: http://www.jinzora.org/downloads
	* License: GNU GPL <http://www.gnu.org/copyleft/gpl.html>
	* 
	* Contributors:
	* Please see http://www.jinzora.org/modules.php?op=modload&name=jz_whois&file=index
	*
	* Code Purpose: This page defines all the different icons for Jinzora
	* Created: 9.24.03 by Ross Carlson
	*
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	
	// Now let's actually define the icons
	if (!isset($image_dir)){
		$image_dir = $include_path. 'style/'. $skin. '/';
	}
	$main_img_dir = $include_path. 'style/images/';

	$disabled_dir = $root_dir. '/style/images/';
	$img_delete = '<img src="'. $image_dir. 'delete.gif" border=0 alt="'. word("Delete"). '" title="'. word("Delete"). '">';
	$img_download = '<img src="'. $image_dir. 'download.gif" border=0 alt="'. word("Download"). '" title="'. word("Download"). '">';
	$img_home = '<img src="'. $image_dir. 'home.gif" border=0 alt="'. word("Return Home"). '" title="'. word("Return Home"). '">';
	$img_more = '<img src="'. $image_dir. 'more.gif" border=0 alt="'. word("More"). '" title="'. word("More"). '">';
	$img_play = '<img src="'. $image_dir. 'play.gif" border=0 alt="'. word("Play"). '" title="'. word("Play"). '">';
	$img_tiny_play = '<img src="'. $image_dir. 'tiny-play.gif" border=0 alt="'. word("Play"). '" title="'. word("Play"). '">';
	$img_blank = '<img src="'. $image_dir. 'blank.gif" border=0>';
	$img_random_play = '<img src="'. $image_dir. 'random.gif" border=0 alt="'. word("Play Random"). '" title="'. word("Play Random"). '">';
	$img_move = '<img src="'. $image_dir. 'move.gif" border=0 alt="'. word("Move Item"). '" title="'. word("Move Item"). '">';
	$img_up_arrow = '<img src="'. $image_dir. 'up-arrow.gif" border=0 alt="'. word("Up level"). '" title="'. word("Up level"). '">';
	$img_playlist = '<img src="'. $image_dir. 'playlist.gif" border=0 alt="'. word("Playlist"). '" title="'. word("Playlist"). '">';
	$img_rate = '<img src="'. $image_dir. 'rate.gif" border=0 alt="'. word("Rate"). '" title="'. word("Rate"). '">';
	$img_star = '<img src="'. $image_dir. 'star.gif" border=0 >';
	$img_half_star = '<img src="'. $image_dir. 'half-star.gif" border=0 >';
	$img_discuss = '<img src="'. $image_dir. 'discuss.gif" border=0 alt="'. word("Discuss"). '" title="'. word("Discuss"). '">';
	$img_clear = '<img src="'. $image_dir. 'clear.gif" border=0 alt="'. word("Clear"). '" title="'. word("Clear"). '">';
	$img_star_half_empty = '<img src="'. $image_dir. 'star-half-empty.gif" border=0 alt="'. word("Rate"). '" title="'. word("Rate"). '">';
	$img_star_full_empty = '<img src="'. $image_dir. 'star-full-empty.gif" border=0 alt="'. word("Rate"). '" title="'. word("Rate"). '">';
	$img_star_right = '<img src="'. $image_dir. 'star-right.gif" border=0 alt="'. word("Rate"). '" title="'. word("Rate"). '">';
	$img_star_half = '<img src="'. $image_dir. 'star-half.gif" border=0 alt="'. word("Rate"). '" title="'. word("Rate"). '">';
	$img_star_full = '<img src="'. $image_dir. 'star-full.gif" border=0 alt="'. word("Rate"). '" title="'. word("Rate"). '">';
	$img_star_left = '<img src="'. $image_dir. 'star-left.gif" border=0 alt="'. word("Rate"). '" title="'. word("Rate"). '">';
	$img_fav_track = '<img src="'. $image_dir. 'rate.gif" border=0 alt="'. word("Add to favorites"). '" title="'. word("Add to favorites"). '">';
	$img_lofi = '<img src="'. $image_dir. 'play-lofi.gif" border=0 alt="'. word("Play Lofi"). '" title="'. word("Play Lofi"). '">';
	$img_new = '<img src="'. $image_dir. 'new.gif" border=0 alt="'. word("New"). '" title="'. word("New"). '">';
	$img_slim_pop = '<img src="'. $image_dir. 'slim-pop.gif" border=0 alt=Slimzora title=Slimzora>';
	$img_sm_logo = '<img src="'. $root_dir. '/style/images/powered-by-small.gif" border=0 alt="'. $this_pgm. $version. '" title="'. $this_pgm. $version. '">';	
	$img_slimzora = '<img src="'. $root_dir. '/style/images/slimzora.gif" border=0 alt="'. $this_pgm. $version. '" title="'. $this_pgm. $version. '">';	
	$img_email = '<img src="'. $image_dir. 'email.gif" border=0 alt="'. word("Share via email"). '" title="'. word("Share via email"). '">';	
	$img_add = '<img src="'. $image_dir. 'add.gif" border=0 alt="'. word("Add to"). '" title="'. word("Add to"). '">';
	$img_clip = '<img src="'. $image_dir. 'play-clip.gif" border=0 alt="'. word("Play clip"). '" title="'. word("Play clip"). '">';
	$img_check = '<img src="'. $image_dir. 'check.gif" border=0 alt="'. word("Check all"). '" title="'. word("Check all"). '">';
	$img_check_none = '<img src="'. $image_dir. 'check-none.gif" border=0 alt="'. word("Check none"). '" title="'. word("Check none"). '">';
	$img_pause = '<img src="'. $image_dir. 'pause.gif" border=0 alt="'. word("Pause"). '" title="'. word("Pause"). '">';
	$img_stop = '<img src="'. $image_dir. 'stop.gif" border=0 alt="'. word("Stop"). '" title="'. word("Stop"). '">';
	$img_previous = '<img src="'. $image_dir. 'previous.gif" border=0 alt="'. word("Previous"). '" title="'. word("Previous"). '">';
	$img_next = '<img src="'. $image_dir. 'next.gif" border=0 alt="'. word("Next"). '" title="'. word("Next"). '">';
	$img_rss = '<img src="'. $image_dir. 'rss.gif" border=0>';
	$img_podcast = '<img src="'. $image_dir. 'podcast.gif" border=0 alt="'. word("Subscribe to Podcast"). '" title="'. word("Subscribe to Podcast"). '">';
	$img_login = '<img src="'. $image_dir. 'login.gif" border=0 alt="login" title="Login">';
	$img_logout = '<img src="'. $image_dir. 'logout.gif" border=0 alt="logout" title="Logout">';
	$img_prefs = '<img src="'. $image_dir. 'prefs.gif" border=0 alt="prefs" title="Preferences">';
	$img_ht_alpha_listing_artists = '<img src="'. $image_dir. 'ht-alpha_listing_artists.gif" border=0 alt="alphabetical listing (artists)">';
	$img_ht_alpha_listing_albums = '<img src="'. $image_dir. 'ht-alpha_listing_albums.gif" border=0 alt="alphabetical listing (albums)">';
	$img_ht_browse = '<img src="'. $image_dir. 'ht-browse.gif" border=0 alt="browse">';
	$img_ht_genres = '<img src="'. $image_dir. 'ht-genres.gif" border=0 alt="genres">';
	$img_ht_random_albums = '<img src="'. $image_dir. 'ht-random_albums.gif" border=0 alt="random albums">';
	$img_ht_charts = '<img src="'. $image_dir. 'ht-charts.gif" border=0 alt="charts">';
	$img_ht_view_all_art = '<img src="'. $image_dir. 'ht-view_all_art.gif" border=0 alt="view all art" title="View All Art">';
	$img_ht_artists = '<img src="'. $image_dir. 'ht-artists.gif" border=0 alt="artists">';
	$img_ht_albums = '<img src="'. $image_dir. 'ht-albums.gif" border=0 alt="albums">';
	$img_user = '<img src="'. $image_dir. 'user.gif" border=0 alt="'. word("user"). '" title="'. word("Current User"). '">'; 


	
	
	// These are the jukebox images
	$img_jb_play = '<img src="'. $image_dir. 'jb_play.gif" border=0 alt="'. word("Play"). '" title="'. word("Play"). '">';
	$img_jb_pause = '<img src="'. $image_dir. 'jb_pause.gif" border=0 alt="'. word("Pause"). '" title="'. word("Pause"). '">';
	$img_jb_stop = '<img src="'. $image_dir. 'jb_stop.gif" border=0 alt="'. word("Stop"). '" title="'. word("Stop"). '">';
	$img_jb_previous = '<img src="'. $image_dir. 'jb_previous.gif" border=0 alt="'. word("Previous"). '" title="'. word("Previous"). '">';
	$img_jb_next = '<img src="'. $image_dir. 'jb_next.gif" border=0 alt="'. word("Next"). '" title="'. word("Next"). '">';
	$img_jb_random_play = '<img src="'. $image_dir. 'jb_random.gif" border=0 alt="'. word("Play Random"). '" title="'. word("Play Random"). '">';
	$img_jb_clear = '<img src="'. $image_dir. 'jb_clear.gif" border=0 alt="'. word("Clear"). '" title="'. word("Clear"). '">';
	
	// Set up some raw images:
	$raw_img_play = $image_dir.'play.gif';
	$raw_img_random_play = $image_dir.'random.gif';
	$raw_img_download = $image_dir.'download.gif';
	$raw_img_add = $image_dir.'newplaylist.gif';
	$raw_img_play_clear = $image_dir.'clear.gif';
	$raw_img_new = $image_dir.'new.gif';
	
	// Now let's create the blank icons
	if ($jinzora_skin == 'cms-theme'){
		$dis_dir = $disabled_dir;
	} else {
		$dis_dir = $image_dir;
	}

	$img_add_dis = '<img src="'. $dis_dir. 'add-disabled.gif" border=0 alt="'. word("Add to"). '" title="'. word("Add to"). '">';
	$img_delete_dis = '<img src="'. $dis_dir. 'delete-disabled.gif" border=0 alt="'. word("Delete"). '" title="'. word("Delete"). '">';
	$img_download_dis = '<img src="'. $dis_dir. 'download-disabled.gif" border=0 alt="'. word("Download"). '" title="'. word("Download"). '">';
	$img_more_dis = '<img src="'. $dis_dir. 'more-disabled.gif" border=0 alt="'. word("More"). '" title="'. word("More"). '">';
	$img_play_dis = '<img src="'. $dis_dir. 'play-disabled.gif" border=0 alt="'. word("Play"). '" title="'. word("Play"). '">';
	$img_random_play_dis = '<img src="'. $dis_dir. 'random-disabled.gif" border=0 alt="'. word("Play Random"). '" title="'. word("Play Random"). '">';
	$img_move_dis = '<img src="'. $dis_dir. 'move-disabled.gif" border=0 alt="'. word("Move Item"). '" title="'. word("Move Item"). '">';
	$img_up_arrow_dis = '<img src="'. $dis_dir. 'up-arrow-disabled.gif" border=0 alt="'. word("Up level"). '" title="'. word("Up level"). '">';
	$img_playlist_dis = '<img src="'. $dis_dir. 'playlist-disabled.gif" border=0 alt="'. word("Playlist"). '" title="'. word("Playlist"). '">';
	$img_discuss_dis = '<img src="'. $dis_dir. 'discuss-disabled.gif" border=0 alt="'. word("Discuss"). '" title="'. word("Discuss"). '">';
	$img_tiny_play_dis  = '<img src="'. $dis_dir. 'tiny-play-disabled.gif" border=0 alt="'. word("Play"). '" title="'. word("Play"). '">';
?>
