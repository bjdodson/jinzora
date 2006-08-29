<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	// Let's set the image directory
	$image_dir = "$root_dir/style/$jinzora_skin";
	// Let's clean up the variables
	$albumDispName = str_replace("'","",$album); 
	$genreDispName = str_replace("'","",$genre); 
	$artistDispName = str_replace("'","",$artist); 
	
	// Now we need to include the menu library
	include_once($web_root. $root_dir. '/lib/menu.lib.php');
	
	// Now we need to do a fix on all the words to remove the ' from them (if they had them)	
	$word_tools = str_replace("'","",word_tools); 
	$word_update_cache = str_replace("'","",word_update_cache); 
	$word_delete_cache = str_replace("'","",word_delete_cache); 
	$word_update_id3v1 = str_replace("'","",word_update_id3v1); 
	$word_user_manager = str_replace("'","",word_user_manager); 
	$word_search_for_album_art = str_replace("'","",word_search_for_album_art); 
	$word_enter_setup = str_replace("'","",word_enter_setup); 
	$word_check_for_update = str_replace("'","",word_check_for_update);
	$word_add_fake_track = str_replace("'","",word_add_fake_track);		
	$word_all_tags = str_replace("'","",word_all_tags);	
	$word_for = str_replace("'","",word_for);		
	$word_retrieve_lyrics = str_replace("'","",word_retrieve_lyrics); 	
	$word_reports = str_replace("'","",word_reports); 	
?>
<script language="JavaScript" type="text/javascript">
var jzToolsMenu =
[
	[null,'<?php echo $word_tools; ?>',null,null,'',
		<?php
			if (stristr($_SESSION['prev_page'],"?")){ $sep = "&"; } else { $sep = "?"; }
			$this_url = $_SESSION['prev_page']. $sep. 'editmenu=addmedia&info='. jzstripslashes(urlencode($genre). "/". urlencode($artist). "/". urlencode($album));
		?>
		['<img src="<?php echo $image_dir; ?>/addmedia.gif" />','<?php echo word_add_media; ?>','<?php echo $this_url; ?>',null,''],
		['<img src="<?php echo $image_dir; ?>/playlist.gif" />','<?php echo $word_delete_cache; ?>','<?php echo $this_page. $url_seperator; ?>ptype=tools&action=deletecache',null,''],
		['<img src="<?php echo $image_dir; ?>/playlist.gif" />','<?php echo $word_update_cache; ?>','<?php echo $this_page. $url_seperator; ?>ptype=tools&action=updatecache',null,''],
		
		<?php
			// Now let's see if they are in 3 dir mode and give them the option to update the level
			if ($directory_level == "3" and $genre <> ""){
				$this_url = $this_page. $url_seperator. 'ptype=updatealltag&info='. urlencode($genre). "&return=". urlencode($genre);
				?>
					['<img src="<?php echo $image_dir; ?>/edit.gif" />','<?php echo $word_update_id3v1; ?>','',null,'',
						['<img src="<?php echo $image_dir; ?>/edit.gif" />','<?php echo $word_all_tags; ?>','<?php echo $this_page. $url_seperator; ?>ptype=tools&action=upid3',null,''],
						<?php
							$this_url = $this_page. $url_seperator. 'ptype=updatealltag&info='. urlencode($genre). "&genre=". urlencode($genre). "&return=". urlencode($genre);
						?>
						['<img src="<?php echo $image_dir; ?>/edit.gif" />','<em><?php echo $word_for; ?>: <?php echo $genre; ?></em>','<?php echo $this_url; ?>',null,''],
				<?php
				// Now let's see if they are on an artist
				if ($artist <> ""){
					$this_url = $this_page. $url_seperator. 'ptype=updatealltag&info='. urlencode($genre). "/". urlencode($artist). "&return=". urlencode($genre). "|". urlencode($artist);
					?>
						['<img src="<?php echo $image_dir; ?>/edit.gif" />','<em><?php echo $word_for; ?>: <?php echo $artist; ?></em>','<?php echo $this_url; ?>',null,''],
					<?php
				}
				?>
				],
				<?php
			} else {
				?>
					['<img src="<?php echo $image_dir; ?>/edit.gif" />','<?php echo $word_update_id3v1; ?>','<?php echo $this_page. $url_seperator; ?>ptype=tools&action=upid3',null,''],
				<?php	
			}
		?>
		<?php
			// Now let's see if they are in 3 dir mode and give them the option to update the level
			if ($directory_level == "3" and $genre <> ""){
				$this_url = $this_page. $url_seperator. 'ptype=updatealltag&info='. urlencode($genre). "&return=". urlencode($genre);
				?>
					['<img src="<?php echo $image_dir; ?>/edit.gif" />','<?php echo $word_retrieve_lyrics; ?>','',null,'',
					<?php
						$this_url = $this_page. $url_seperator. 'ptype=retrievealllyrics&info='. urlencode($genre). "&return=". urlencode($genre)
					?>
					['<img src="<?php echo $image_dir; ?>/edit.gif" />','<em><?php echo $word_for; ?>: <?php echo $genre; ?></em>','<?php echo $this_url; ?>',null,''],
				<?php
				// Now let's see if they are on an artist
				if ($artist <> ""){
					$this_url = $this_page. $url_seperator. 'ptype=retrievelyrics&info='. urlencode($genre). "/". urlencode($artist). "&return=". urlencode($genre). "|". urlencode($artist);
					?>
						['<img src="<?php echo $image_dir; ?>/edit.gif" />','<em><?php echo $word_for; ?>: <?php echo $artist; ?></em>','<?php echo $this_url; ?>',null,''],
					<?php
				}
				?>
				],
				<?php
			}
		?>
		
		<?php
			if($enable_meta_search){
		?>
			<?php
				$this_url = $this_page. $url_seperator. 'ptype=retrievegenremetadata&info='. urlencode($genre);
			?>
			['<img src="<?php echo $image_dir; ?>/art.gif" />','Attempt Meta Data Retrevial for: <em><?php echo $genre;?></em>','<?php echo $this_url; ?>',null,'',],
		<?php
			}
		?>		
				
		['<img src="<?php echo $image_dir; ?>/user.gif" />','<?php echo $word_user_manager; ?>','<?php echo $this_page. $url_seperator; ?>ptype=tools&action=usermanager',null,''],
		['<img src="<?php echo $image_dir; ?>/art.gif" />','<?php echo $word_search_for_album_art; ?>','<?php echo $this_page. $url_seperator; ?>ptype=tools&action=searchforart',null,''],
		['<img src="<?php echo $image_dir; ?>/setup.gif" />','<?php echo $word_enter_setup; ?>','<?php echo $this_page. $url_seperator. 'ptype='. $_GET['ptype']. '&genre='. urlencode($genre). '&artist='. urlencode($artist). '&album='. urlencode($album). '&editmenu=setupsystem'; ?>',null,''],
		
		['<img src="<?php echo $image_dir; ?>/playlist.gif" />','<?php echo $word_reports; ?>','<?php echo $this_page. $url_seperator. 'ptype='. $_GET['ptype']. '&genre='. urlencode($genre). '&artist='. urlencode($artist). '&album='. urlencode($album). '&editmenu=reports'; ?>',null,''],
		
		['<img src="<?php echo $image_dir; ?>/updates.gif" />','<?php echo $word_check_for_update; ?>','<?php echo $this_page. $url_seperator; ?>ptype=tools&action=checkforupdates',null,''],
	],
];

</script>
<span id="myToolsMenu"></span>
<script language="JavaScript" type="text/javascript">
	cmDraw ('myToolsMenu', jzToolsMenu, 'hbr', cmjz, 'jz');
</script>
