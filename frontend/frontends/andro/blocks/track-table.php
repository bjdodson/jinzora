<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	global $row_colors, $jinzora_skin, $root_dir, $show_artist_album, $show_track_num, $this_page, $img_play;
	
	// Let's setup the objects
	$display = &new jzDisplay();
	$smarty = smartySetup();
	
	$node = $tracks[0]->getAncestor('album');
	if (!$node) { $node = $tracks[0]->getParent(); }

	$smarty->assign('this_page', $this_page);
	$smarty->assign('form_action', jz_encode("action"));
	$smarty->assign('form_action_val', jz_encode("mediaAction"));
	$smarty->assign('form_path', jz_encode("jz_path"));
	$smarty->assign('form_path_val', htmlentities(jz_encode($node->getPath("String"))));
	$smarty->assign('form_list_type', jz_encode("jz_list_type"));
	$smarty->assign('form_list_type_val', jz_encode("tracks"));
	
	$i=0;$c=0;
	// Now let's loop through the track nodes
	foreach($tracks as $track){
		// Let's get the meta data
		// This will return the meta data (like length, size, bitrate) into a keyed array
		$meta = $track->getMeta();

		$c = 1 - $c;
		$array[$i]['row_color'] = $row_colors[$c];
		$array[$i]['path'] = jz_encode($track->getPath("String"));
		$array[$i]['download_button'] = $display->downloadButton($track, true, true);
		$array[$i]['play_button'] = $display->playLink($track, $img_play, false, false, true);
		
		$array[$i]['track_num'] = "";
		// Did they want track numbers?
		if ($show_track_num == "true"){
			// Now let's link to this track
			$number = $meta['number'];
			if ($number <> ""){
				if (strlen($number) < 2){
					$array[$i]['track_num'] = "&nbsp;"; 
				}
				$array[$i]['track_num'] .= $number. ". ";
			}
		}				
		$array[$i]['track_name'] = $display->playLink($track, $meta['title'], false, false, true);
		$array[$i]['show_artist_album'] = $show_artist_album;

		// Now let's get the parents
		$parent = $track->getAncestor("album");
		$gparent = $track->getAncestor("artist");
		if (is_object($gparent)){
			$array[$i]['artist'] = $gparent->getName();
		} else {
			$array[$i]['artist'] = "";
		}
		if (is_object($parent)){
			$array[$i]['album'] = $parent->getName();
		} else {
			$array[$i]['album'] = "";
		}
			
		// Now let's link to this track
		$array[$i]['length'] = convertSecMins($meta['length']); 
		$array[$i]['bitrate'] = $meta['bitrate']. " Kbit/s";
		$array[$i]['size'] = $meta['size']. " MB";
		$eArr = explode(".",$meta['filename']);
		$array[$i]['type'] = strtoupper($eArr[count($eArr)-1]);
		
		$i++;
	}
	$smarty->assign('items', $array);
	jzTemplate($smarty, "track-table");
	
	// a bit of a hack.. don't know why this wasn't here.
	if ($purpose == "search"){
		$this->playlistBar();
	}
?>