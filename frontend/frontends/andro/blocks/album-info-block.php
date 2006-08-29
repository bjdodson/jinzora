<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	// Now do we have art or image or desc at the album level
	if ($node->getPType() == "album"){
		if (($art = $node->getMainArt($image_size. "x". $image_size)) <> false or (($desc = $node->getDescription()) <> "")) {	
			$desc = $node->getDescription();
			// Let's display the name
			$artist = $node->getAncestor('artist');
			$smarty->assign('artist_link',"");
			if ($artist !== false) {
				$smarty->assign('artist_link', $display->link($artist,$artist->getName(),false, false, true));
			}
			$smarty->assign('album_name',$node->getName());
			
			$smarty->assign('album_year',"");
			if (!isNothing($node->getYear())){
				$smarty->assign('album_year',$node->getYear());
			}
	
			$smarty->assign('album_art',"");
			if ($art){
				if ($desc){$align="left";}else{$align="center";}
				$smarty->assign('album_art',$display->returnImage($art,$node->getName(),$image_size,$image_size,"limit",false,false,$align,"4","4"));
			}
			$smarty->assign('album_desc',$display->returnShortName($desc,$desc_truncate));
			
			// Do we need the read more link?
			$smarty->assign('read_more',"");
			if (strlen($desc) > $desc_truncate){
				$url_array = array();
				$url_array['jz_path'] = $node->getPath("String");
				$url_array['action'] = "popup";
				$url_array['ptype'] = "readmore";
				$smarty->assign('read_more','<a href="'. urlize($url_array). '" onclick="openPopup(this, 450, 450); return false;">...read more</a>');
			}
			
			jzTemplate($smarty, "album-info-block");
		}
	}
?>