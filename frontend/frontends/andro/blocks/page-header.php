<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
global $jukebox;
	// Let's setup our objects
	$display = new jzDisplay();
	$blocks = new jzBlocks();
	$smarty = smartySetup();
	
	if (!is_object($node)) $node = new jzMediaNode();			
	
	// Let's include the settings file
	include_once($include_path. 'frontend/frontends/andro/settings.php');
	
	// TO DO - SMARTY!!!
	$display->preHeader();	
	
	$smarty->assign('this_page', $this_page);
	$smarty->assign('img_home', $img_home);
	$smarty->assign('cms_mode', $cms_mode);
	$smarty->assign('image_dir', $image_dir);
	$smarty->assign('jinzora_url', $jinzora_url);
	$smarty->assign('word_search', word('Search:'));
	$smarty->assign('word_all_media', word('All Media'));
	if ($cms_mode == "true") {
		$smarty->assign('method', "GET");
	} else {
		$smarty->assign('method', "POST");
	}

if ($jukebox == "true" && !defined('NO_AJAX_JUKEBOX')) {
				$smarty->assign('searchOnSubmit', 'onSubmit="return searchKeywords(this,\'' . htmlentities($this_page) . '\');"');
			} else {
			  $smarty->assign('searchOnSubmit', "");
			}

	$formFields="";	
	foreach (getURLVars($this_page) as $key => $val) { 
		$formFields .= '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) . '">'; 
	} 
	$smarty->assign('formFields', $formFields);
	$formFields="";
	if (distanceTo("artist") !== false){
		$searchFields .= '<option value="artists">'. word("Artists"). '</option>'. "\n";
	}
	if (distanceTo("album") !== false) {
		$searchFields .= '<option value="albums">' . word("Albums"). '</option>'. "\n";
	}
	$smarty->assign('optionFields', $formFields);
	$smarty->assign('searchFields', $searchFields);			
	$smarty->assign('word_tracks', word('Tracks'));
	$smarty->assign('word_lyrics', word('Lyrics'));
	$smarty->assign('login_link', $display->loginLink(false, false, true, false, true));
	if ($jzUSER->getSetting('edit_prefs') !== false) {
	  $smarty->assign('prefs_link', $display->popupLink("preferences", word('Preferences'), true));
	} else {
		$smarty->assign('prefs_link', "");
	}
	
	$smarty->assign('randomizer',$blocks->randomGenerateSelector($node,word("Play:") . ' ',true));
	
	jzTemplate($smarty, "page-header");
?>