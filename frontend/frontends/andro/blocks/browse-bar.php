<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	// Can this user powersearch?
	$on=true;
	if ($jzUSER->getSetting('powersearch') and $on == true){		
		if ($cms_mode == "true") {
			$method = "GET";
		} else {
			$method = "POST";
		}
	}
	if (isset($_POST['jz_path'])){
		$jzPath = $_POST['jz_path'];
	} else {
		$jzPath = $_GET['jz_path'];
	}
	
	// Now should we show this bar?
	$bcArray = explode("/",$jzPath);
	$url = array();			
	$smarty->assign('home_link',urlize($url));
	$smarty->assign('word_home',word("Home"));
	
	// Now let's see if we need the breadcrumbs
	unset($bcArray[count($bcArray)-1]);
	$path="";$crumbs="";
	foreach($bcArray as $item){
		if ($item <> ""){
			$path .= "/". $item;
			$arr['jz_path'] = $path;
				$crumbs .= ' / <a href="'. urlize($arr). '">'. $item. '</a>';										
		}
		unset($arr);
	}
	$smarty->assign('bread_crumbs',$crumbs);
	
	$smarty->assign('artist_list',"");
	if ($show_artist_list == "true"){
		$artist_list  = word("Artists"). ": ";
		$artist_list .= '<form action="'. $this_page. '" method="post">';
		$artist_list .= $display->hiddenPageVars(true); 
		$artist_list .= $display->dropdown("artist", true, "jz_path", false, true); 
		$artist_list .= '</form>';
		$smarty->assign('artist_list',$artist_list);
	}
	jzTemplate($smarty, "browse-bar");
?>