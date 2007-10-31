<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
global $img_playlist,$img_check,$img_check_none;
	if (sizeof($nodes) > 0 || sizeof($tracks) > 0) {
		$smarty->assign('addListButton', $display->addListButton(true));				
		$smarty->assign('hidden_1', $display->hiddenVariableField('action','mediaAction'), true, true);
		$smarty->assign('hidden_2', $display->hiddenVariableField('path',$_GET['jz_path']), true, true);
		$url_array = array();
		$url_array['action'] = "popup";
		$url_array['ptype'] = "playlistedit";
		$smarty->assign('playlist_button', '<a href="javascript:;" onClick="openPopup('. "'". urlize($url_array). "'". ',600,600); return false;">'.$img_playlist.'</a>');
		$smarty->assign('playlist_select', $display->playlistSelect(115, false, "all", true, "jz_playlist", true));
		$smarty->assign('playlist_play_button',$display->playListButton(true));
		$smarty->assign('playlist_random_button',$display->randomListButton(true));
		$smarty->assign('img_check',$img_check);
		$smarty->assign('img_uncheck',$img_check_none);
		jzTemplate($smarty, "playlist-bar");
	}
?>