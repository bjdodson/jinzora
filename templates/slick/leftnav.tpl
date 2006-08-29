<div id="slickLeftNav">
{php}
	$blocks = new jzBlocks();
	$node = new jzMediaNode($_GET['jz_path']);
	
	$blocks->blockLogo();
	$blocks->blockUser();
	$blocks->blockNowStreaming();
	$blocks->blockWhoIsWhere();
	$blocks->blockSearch();
	$blocks->blockPlaylists();
	$blocks->blockBrowsing();
	$blocks->blockOptions($node);
	$blocks->blockGoogleAds();
	$blocks->blockShoutbox();
{/php}
</div>