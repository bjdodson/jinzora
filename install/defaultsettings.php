<?php
// Master Settings
$config_version = $version;
$install_complete = "yes";
		
// System Settings
$hierarchy = $_POST['hierarchy'];
$root_dir = $root_dir;
$media_dirs = str_replace("\\","/",substr($_SESSION['all_media_paths'],0,strlen($_SESSION['all_media_paths'])-1));
$web_dirs = "";
$cms_type = $_POST['cms_type'];
$backend = $_POST['backend'];
$default_importer = $_POST['importer'];
$live_update = "true";
$allow_filesystem_modify = "false";
$allow_id3_modify = "false";
$audio_types = "mp3|ogg|wma|wav|midi|mid|flac|aac|mp4|rm|mpc|m4a|wv|shn|ape|ofr";
$video_types = "avi|wmv|mpeg|mov|mpg|flv";
$playlist_types = "m3u";
$ext_graphic = "jpg|gif|png|jpeg";
$track_num_seperator = " - |.|-";
$date_format = "n.d.Y g:i a";
$short_date = "n/d/y";
$gzip_handler = "true";
$ssl_stream = "false";
$protocols = "http://|ftp://|https://|mms://";
$media_lock_mode = "off";
$enable_podcast = "false";	
$enable_podcast_subscribe = "false";	
$podcast_folder = "data/podcasts";	
$enable_logging = "false";	
$enable_shopping = "false";	
$secure_urls = "false";		
$enable_favorites = "false";		
$enable_page_caching = "false";		
$cache_age_days = "5";		
$gzip_page_cache = "false";				
$security_key = md5(uniqid("ji") . uniqid("nz") . uniqid("ora"));
$enable_query_cache = "false";
		
// Playlist Settings
$enable_playlist = "true";
$playlist_ext = "m3u";
$use_ext_playlists = "true";
$asx_file_types = "avi|mpeg|mpg|asf|wmv";
$asx_show_trackdetail = "true";
$max_playlist_length = "250";
$random_play_amounts = "1|5|10|25|50|100";
$default_random_count = "25";
$default_random_type = "Songs";
$embedded_player = "";
$enable_audioscrobbler = "true";	
$as_override_user = "";	
$as_override_pass = "";	
$as_override_all = "false";	
$as_max_retry = "5";			
		
// Display Settings
$site_title = "Jinzora Media Jukebox";
$jinzora_skin = $_POST['style'];
$frontend = $_POST['frontend'];
$jz_lang_file = $_POST['jz_lang_file'];
$allow_lang_choice = "false";
$allow_style_choice = "false";
$allow_interface_choice = "false";
$show_loggedin_level = "false";
$help_access = "all";	
$artist_truncate = "20";
$quick_list_truncate = "24";
$album_name_truncate = "20";
$show_page_load_time = "false";
$show_sub_numbers = "true";
$sort_by_year = "true";
$num_other_albums = "3";
$header_drops = "true";
$genre_drop = "true";
$artist_drop = "true";
$album_drop = "false";
$song_drop = "false";
$quick_drop = "true";
$days_for_new = "15";
$hide_id3_comments = "true";	
$show_all_checkboxes = "false";
$status_blocks_refresh = "30";
$compare_ignores_the = "true";
$handle_compilations = "false";
$embedded_header = "";
$embedded_footer = "";
$who_is_where_height = "8";
		
// Image Settings
$resize_images = "true";
$keep_porportions = "true";
$auto_search_art = "false";
$create_blank_art = "false";
$default_art = "folder|cover|mainArt";
		
// Groupware Settings
$enable_discussion = "false";
$enable_requests = "false";
$enable_ratings = "false";
$rating_weight = "0.5";
$track_plays = "true";
$display_downloads = "true";
$secure_links = "false";
$user_tracking_display = "false";
$user_tracking_age = "30";
$disable_random = "false";
$info_level = "admin";
$track_play_only = "false";
$allow_clips = "false";
$clip_length = "30";
$clip_start = "60";
	
// Charts and Random Album Settings
$display_charts = "false";
$chart_types = "topplayalbum,topplayartist,topviewartist,newalbums";
$num_items_in_charts = "5";
$random_albums = "4";
$random_per_slot = "4";
$random_rate = "8000";	
$random_art_size = "100x100";	
$rss_in_charts = "true";	
$chart_timeout_days = "0";	
			
// Resampling
$allow_resample = "false";
$force_resample = "false";
$default_resample = "32";
$resampleRates = "192|128|112|96|64|48|32";
$path_to_lame = "/usr/local/bin/lame";
$path_to_flac = "/usr/local/bin/flac";
$path_to_mpc = "/usr/local/bin/mppdec";
$path_to_wavunpack = "/usr/local/bin/wvunpack";
$path_to_oggdec = "/usr/local/bin/oggdec";
$path_to_oggenc = "/usr/local/bin/oggenc";
$path_to_mpcenc = "/usr/local/bin/mppenc";
$path_to_wavpack = "/usr/local/bin/wavpack";
$path_to_wmadec = "c:\\pub\\wmadec"; //Sorry, Windows only!
$path_to_faad = "c:\\pub\\faad"; //Sorry, Windows only - use mplayer on Linux!
$path_to_shn = "/usr/local/bin/shorten";	
$path_to_ofr = "/usr/local/bin/ofr";	
$path_to_macpipe = "/usr/local/bin/macpipe";	
$path_to_mplayer = "/usr/bin/mplayer";	
$mplayer_opts = "-ao pcm -aofile /dev/stdout";	
$lame_cmd = "$path_to_lame -S --silent --quiet -m j -b ";
$lame_opts = "/usr/local/bin/lame -b 32 -f -m m";
$always_resample = "flac|mpc|wv|wav|shn|m4a|ape|ofr|ogg|wma|m4a";
$always_resample_rate = "128";
$allow_resample_downloads = "true";
$resample_cache_size = "100";
$no_resample_subnets = "(192\.168\..*\..*)|(127\..*\..*\..*)";
		
// Download Settings
$multiple_download_mode = "zip";
$single_download_mode = "raw";
		
// Email Settings
$allow_send_email = "false";
$email_from_address = "user@jinzora.org";
$email_from_name = "Jinzora";
$email_server = "server";	
		
// Jukebox Settings
$jukebox = $_POST['jukebox'];
$jukebox_display = "default";
$jukebox_default_addtype = "current";
$default_jukebox = "stream";
$home_jukebox_subnets = "(192.168..*..*)|(127..*..*..*)";
$home_jukebox_id = "stream";
$jb_volumes = "100|90|80|70|60|50|40|30|20|10|0";		
		
// Keyword Settings
$keyword_radio = "@radio";
$keyword_random = "@random";
$keyword_play = "@play";
$keyword_track = "@track";
$keyword_album = "@album";
$keyword_artist = "@artist";
$keyword_genre = "@genre";
$keyword_lyrics = "@lyrics";
$keyword_limit = "@limit";
$keyword_id = "@id";
		
// Audioscrobbler Settings
$audioscrobbler_user = "";
$audioscrobbler_pass = "";

?>
