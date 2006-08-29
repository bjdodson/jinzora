<?php
	if (!isset($$display)) {
		$display = new jzDisplay();
	}
	$smarty = smartySetup();
	$smarty->assign('width',$width);
	
	jzTemplate($smarty, "block-body-open");
?>