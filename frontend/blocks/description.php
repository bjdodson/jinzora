<?php
	$smarty = smartySetup();		
	$display = new jzDisplay();
	
	$desc = $node->getDescription();
	if (isNothing($desc)) {
		return false;
	}
	if ($desc_truncate === false) {
		$desc_truncate = 700;
	}
	
	$smarty->assign('description', $display->returnShortName($desc,$desc_truncate));
	$smarty->assign('read_more',"");
	if (strlen($desc) > $desc_truncate){
		$url_array = array();
		$url_array['jz_path'] = $node->getPath("String");
		$url_array['action'] = "popup";
		$url_array['ptype'] = "readmore";
		$smarty->assign('read_more', '<a href="'. urlize($url_array). '" onclick="openPopup(this, 450, 450); return false;">...read more</a>');
	}	
	jzTemplate('description');
?>