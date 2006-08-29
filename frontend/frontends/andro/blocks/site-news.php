<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	// Let's show the news
	$siteNews = $blocks->siteNews($node);
	if ($siteNews <> ""){
		$smarty->assign('site_news', $siteNews);
		jzTemplate($smarty, "site-news");
	}
?>