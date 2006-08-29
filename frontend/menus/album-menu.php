<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	// Let's set the image directory
        global $image_dir,$node,$jzUSER;
	// Let's clean up the variables
	$albumDispName = str_replace("'","",getInformation($node,"album")); 
	$genreDispName = str_replace("'","",getInformation($node,"genre")); 
	$artistDispName = str_replace("'","",getInformation($node,"artist")); 
	
	// Now we need to include the menu library
	include_once($include_path. "frontend/menus/menu.lib.php");
	
	// Now we need to do a fix on all the words to remove the ' from them (if they had them)
	$word_play_all_albums_from = str_replace("'","",word("Play all albums from")); 
	$word_randomize_all_albums_from = str_replace("'","",word("Randomize all albums from")); 
	$word_play = str_replace("'","",word("Play")); 
	$word_selected = str_replace("'","",word("Selected")); 
	$word_session_playlist = str_replace("'","",word("Session Playlist")); 
	$word_new_playlist = str_replace("'","",word("New Playlist")); 
	$word_new = str_replace("'","",word("New")); 
	$word_media_management = str_replace("'","",word("Media Management")); 
	$word_actions = str_replace("'","",word("Actions")); 
	$word_search = str_replace("'","",word("Search")); 
	$word_rewrite_tags = str_replace("'","",word("Rewrite ID3 Tags")); 
	$word_download_album = str_replace("'","",word("Download Album")); 
	$word_group_features = str_replace("'","",word("Group Features")); 
	$word_play_album = str_replace("'","",word("Play Album")); 
	$word_play_random = str_replace("'","",word("Play Random")); 
	$word_item_information = str_replace("'","",word("Item Information")); 
	$word_change_art = str_replace("'","",word("Change Art")); 
	$word_browse_album = str_replace("'","",word("Browse Album")); 
	$word_edit_album_info = str_replace("'","",word("Edit album information")); 
	$word_information = str_replace("'","",word("Information")); 
	$word_echocloud = str_replace("'","",word("Echocloud similar artists")); 
	$word_add_fake_track = str_replace("'","",word("Add fake track"));	
	$word_size = str_replace("'","",word("Size"));	
	$word_bit_rate = str_replace("'","",word("Bitrate"));	
	$word_sample_rate = str_replace("'","",word("Sample Rate"));	
	$word_date = str_replace("'","",word("Date"));
	$word_length = str_replace("'","",word("Length"));
	$word_track_number = str_replace("'","",word("Track Number"));
	$word_retrieve_lyrics = str_replace("'","",word("Retrieve Lyrics"));
	$word_share_via_email = str_replace("'","",word("Share via email"));
	$word_bulk_edit = str_replace("'","",word("Bulk Edit"));
?>
<script language="JavaScript" type="text/javascript">
var jzMenu =
[
	[null,'<?php echo $word_actions; ?>',null,null,'',
		<?php
			// Let's see if they only wanted track plays
			if ($track_play_only <> "true"){
		?>
		['<img src="<?php echo $image_dir; ?>play.gif" />','<?php echo $word_play_album. ' <em>'. $albumDispName. '</em>'; ?>','<?php echo $display->link($node, false, false, false, true, true, false, true); ?>',null,''],
		<?php
			}
		?>
		<?php
			if ($disable_random != "true"){
		?>
			['<img src="<?php echo $image_dir; ?>random.gif" />','<?php echo $word_play_random. ' <em>'. $albumDispName. '</em>'; ?>','<?php echo $display->link($node, false, false, false, true, true, true, true); ?>',null,''],
		<?php
			}
		?>
		<?php
			// Let's see if they only wanted track plays
			if ($track_play_only <> "true"){
		?>
		<?php
			// Now we need to make sure they are NOT in 1 level mode
			if (($anode = $node->getAncestor("artist")) !== false){
		?>
			['<img src="<?php echo $image_dir; ?>play.gif" />','<?php echo $word_play_all_albums_from. ' <em>'. $artistDispName. '</em>'; ?>','<?php echo $display->link($anode, false, false, false, true, true, false, true); ?>',null,''],
		<?php
			}
		?>
		<?php
			if ($disable_random != "true"){
		?>
			['<img src="<?php echo $image_dir; ?>random.gif" />','<?php echo $word_randomize_all_albums_from. ' <em>'. $artistDispName. '</em>'; ?>','<?php echo $display->link($anode, false, false, false, true, true, true, true); ?>',null,''],
		<?php
			}
		?>
		<?php
			}
		?>
		<?php
			if (checkPermission($jzUSER,"download")){
				// First let's get the size for this album
				$album_size = 0;
				$tracks = $node->getSubNodes("tracks",-1);
				foreach ($tracks as $track) {
				  $meta = $track->getMeta();
				  $album_size += $meta['size'];
				}
				$album_size = $album_size . " MB";
				$dnl_url = $display->downloadButton($node, true);
				
				?>
				['<img src="<?php echo $image_dir; ?>download.gif" />','<?php echo $word_download_album. ' <em>'. $albumDispName. ' ('. $album_size. ')</em>'; ?>','<?php echo $dnl_url; ?>',null,''],
				
				<?php
			}
		?>		
		<?php
			$share_url = "";
			
			// Now let's see if they wanted to enable email sending
			if ($allow_send_email == "true"){

						// Now let's create the URL for the link to send via email
						$email_href = $this_site. $root_dir. '/playlists.php?emailalbum='. base64_encode(jzstripslashes($genre. "/". $artist. "/". $album));
						$share_url = 'mailto:?subject='. $artistDispName. ' - '. $albumDispName. '&body=Click to play '. $artistDispName. ' - '. $albumDispName. ':%0D%0A%0D%0A'. $email_href. '%0D%0A%0D%0APowered by Jinzora %0D%0AJinzora :: Free Your Media%0D%0Ahttp://www.jinzora.org';
				?>
				['<img src="<?php echo $image_dir; ?>email.gif" />','<?php echo $word_share_via_email. " <em>". $albumDispName. "</em>"; ?>','<?php echo $share_url; ?>',null,''],
				<?php
			}
		?>
	],
	
];

</script>
<span id="myMenuID"></span>
<script language="JavaScript" type="text/javascript">
	cmDraw ('myMenuID', jzMenu, 'hbr', cmjz, 'jz');
</script>
