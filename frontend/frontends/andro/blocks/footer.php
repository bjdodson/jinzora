<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	$smarty->assign('jinzora_url', $jinzora_url);
	$smarty->assign('version', $version);
	$smarty->assign('image_dir', $image_dir);
	
	$smarty->assign('page_load', "");
	if ($show_page_load_time == "true" and $_SESSION['jz_load_time'] <> ""){
		// Ok, let's get the difference
		$diff = round(microtime_diff($_SESSION['jz_load_time'],microtime()),3);
		$smarty->assign('page_load', $diff. " ". word("seconds"). "&nbsp;"); 
	}
        if ($jzUSER->getID() == $jzUSER->lookupUID('NOBODY')) {
	  $smarty->assign("logged_in",false);
	} else {
	  $smarty->assign("logged_in",true);
	}
	$smarty->assign('username', $jzUSER->getName());
	$smarty->assign('word_logged_in', word("Logged in as"));
	
	jzTemplate($smarty, "footer");
	
	$jzSERVICES->cmsClose();
?>