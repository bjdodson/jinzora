<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
global $img_more;
	if ($cms_mode == "true"){
		$mode = "POST";
	} else {
		$mode = "GET";
	}								
	if (isset($_POST['jz_path'])){
		$bcArray = explode("/",$_POST['jz_path']);
	} else {
		$bcArray = explode("/",$_GET['jz_path']);
	}
	
	// Now we need to cut the last item off the list
	$bcArray = array_slice($bcArray,0,count($bcArray)-1);
	$bread_crumbs  = '<form action="'. $this_page. '" method="'. $mode. '">'. "\n";
	$bread_crumbs .= '<select style="width:175px" class="jz_select" name="'. jz_encode('jz_path'). '" onChange="form.submit();">'. "\n";
					
	$parent = $node->getParent();
	$nodes = $parent->getSubNodes("nodes");
	sortElements($nodes);
	foreach ($nodes as $child) {
		$path = $child->getPath("String");
		$bread_crumbs .= '<option ';
		// Is this the current one?
		if ($child->getName() == $node->getName()){
			$bread_crumbs .= ' selected ';
		}
		$bread_crumbs .= 'value="'. jz_encode($path). '">'. $display->returnShortName($child->getName(),20). '</option>'. "\n";
	}
	$bread_crumbs .= '</select>'. "\n";
	//$display->hiddenVariableField("jz_path");
	$bread_crumbs .=$display->hiddenPageVars(true);					
	$bread_crumbs .='<input type="hidden" name="frontend" value="'. $_GET['frontend']. '">'. "\n";
	$bread_crumbs .="</form>";
	
	$smarty->assign('bread_crumbs',$bread_crumbs);				
	$smarty->assign('play_button',$display->playButton($node, false, false, false, true));
	$smarty->assign('random_button',$display->randomPlayButton($node, false, false, false, true));

	$url_array = array();
	$url_array['jz_path'] = $node->getPath("String");
	$url_array['action'] = "popup";
	$url_array['ptype'] = "iteminfo"; 
	$smarty->assign('info_button',urlize($url_array));				
	
	$smarty->assign('allow_resample',"false");
	if ($display->wantResampleDropdown($node)){
		$smarty->assign('allow_resample',"true");
		$smarty->assign('resample_box',$display->displayResampleDropdown($node, word("Resample") . ": ",true));
	}
	$smarty->assign('help_access',$help_access);
$smarty->assign('img_info',$img_more);

	jzTemplate($smarty, "breadcrumbs");
?>