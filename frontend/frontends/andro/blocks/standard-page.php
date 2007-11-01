<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	// Now let's get the sub nodes to where we are
global $img_folder;
	if (isset($_GET['jz_letter'])) {
		$root = new jzMediaNode();
		$nodes = $root->getAlphabetical($_GET['jz_letter'],"nodes",distanceTo("artist"));
	} else if ($node->getLevel() == 0 && $show_frontpage_items == "false") {
		$nodes = array();
	} else {
		$nodes = $node->getSubNodes("nodes");
	}

	// Now let's sort
	if ($sort_by_year == "true" and $node->getPType() =="artist"){
		sortElements($nodes,"year");
	} else {
		sortElements($nodes,"name");
	}

	$smarty->assign('form_action', urlize());
	$smarty->assign('hidden_field', '<input type="hidden" name="'.jz_encode('jz_list_type').'" value="'.jz_encode('nodes').'">');

	// Now let's loop through the nodes
	$i=0;
	$c=0;
	foreach($nodes as $item){
		$array[$i]['name'] = $item->getName();
		$array[$i]['path'] = $item->getPath('string');
		$array[$i]['link'] = $display->link($item,$img_folder, false, false, true);
		$name = $item->getName();
		if (!isNothing($item->getYear()) and $item->getPType() == "album"){
			$name .= " (". $item->getYear(). ")";
		}
		$array[$i]['name'] = $display->link($item, $name, false, false, true);
		if (($count = $item->getSubNodeCount("nodes")) <> 0){
			if ($count > 1) {
				$folder = word("folders");
			} else {
				$folder = word("folder");
			}
			$array[$i]['items'] = $display->link($item,$count. " ". $folder, false, false, true);					
		} else {
			if (($count = $item->getSubNodeCount("tracks")) <> 0){
				if ($count > 1) {
					$files = word("files");
				} else {
					$files = word("file");
				}
				$array[$i]['items'] = $display->link($item,$count. " ". $files, false, false, true);
			}
		}
		$array[$i]['play_button'] = $display->playButton($item, false, false, false, true);
		$array[$i]['random_button'] = $display->randomPlayButton($item, false, false, false, true); 
		
		// Now do we hvae another row?
		$array[$i]['subitems'] = false;
		if (($art = $item->getMainArt($image_size. "x". $image_size)) <> false or (($desc = $item->getDescription()) <> "")) {
			$array[$i]['subitems'] = true;
			$array[$i]['art'] = false;
			if ($art){
				$array[$i]['art'] = $display->link($item,$display->returnImage($art,$node->getName(),$image_size,$image_size,"limit",false,false,"left","4","4"),false,false,true);
			}
			$array[$i]['desc'] = $display->returnShortName($item->getDescription(),$desc_truncate);
			$array[$i]['read_more'] = false;
			// Do we need the read more link?
			if (strlen($item->getDescription()) > $desc_truncate){
				$url_array = array();
				$url_array['jz_path'] = $item->getPath("String");
				$url_array['action'] = "popup";
				$url_array['ptype'] = "readmore";
				$array[$i]['read_more'] =  ' <a href="'. urlize($url_array). '" onclick="openPopup(this, 450, 450); return false;">...read more</a>';
			}
		}
		$c = 1 - $c; 
		$array[$i]['row'] = $row_colors[$c];
		$i++;
	}
	$smarty->assign('image_dir', $image_dir);
	$smarty->assign('items', $array);
	
	jzTemplate($smarty, "standard-page");
?>