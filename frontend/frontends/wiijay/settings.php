<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	$advanced_tooltips = "true";
	$cols_in_genre = "3";
	$max_song_name_length = "20";
	$show_frontpage_items = "true";
	$show_lyrics_links = "false";
        $show_album_art = "true";
        $show_artist_art = "true";
        $num_album_cols = "2";
        $num_track_cols = "1";
        $num_artist_cols = "2";
        $random_albums = "4";
        $random_art_size = "100";
        $art_size = "150";
        $artist_art_size = "150";
        $jb_art_size = "200";
        $album_name_truncate = "12"; // for charts
        $jukebox_display = "art"; // art|playlist

        define('JZ_FORCE_EMBEDDED_PLAYER','true');
        $jzSERVICES->loadService('players','jwmp3');
        $JWMP3_OPTS = "autostart=true&thumbsinplaylist=false&showeq=true&showdigits=true&repeat=false&shuffle=false&lightcolor=0x1414E9&backcolor=0x1e1e1e&frontcolor=0xCCCCCC&autoscroll=true";
?>