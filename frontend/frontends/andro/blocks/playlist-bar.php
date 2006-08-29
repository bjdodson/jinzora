<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	if (sizeof($nodes) > 0 || sizeof($tracks) > 0) {
		$smarty->assign('addListButton', $display->addListButton(true));				
		$smarty->assign('hidden_1', $display->hiddenVariableField('action','mediaAction'), true, true);
		$smarty->assign('hidden_2', $display->hiddenVariableField('path',$_GET['jz_path']), true, true);
		$url_array = array();
		$url_array['action'] = "popup";
		$url_array['ptype'] = "playlistedit";
		$smarty->assign('playlist_button', '<a href="javascript:;" onClick="openPopup('. "'". urlize($url_array). "'". ',600,600); return false;"><img src="'. $image_dir. 'playlist.gif" border="0"></a>');
		$smarty->assign('playlist_select', $display->playlistSelect(115, false, "all", true, "jz_playlist", true));
		$smarty->assign('playlist_play_button',$display->playListButton(true));
		$smarty->assign('playlist_random_button',$display->randomListButton(true));
		
		jzTemplate($smarty, "playlist-bar");
	}
?>