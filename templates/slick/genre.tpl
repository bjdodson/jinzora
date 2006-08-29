{php}
	global $jz_path;
	$display = &new jzDisplay();
	$fe = &new jzFrontend();
	$blocks = new jzBlocks();
	$node = new jzMediaNode($jz_path);
	$smarty = smartySetup();
	
	global $show_frontpage_items, $show_alphabet, $random_albums;
{/php}
<table width="100%">
	<tr>
		<td width="100%" valign="top" height="100%">
			{if $site_news <> ""}
				{include file="$smarty_include/templates/blocks/block-site-news.tpl"}
			{/if}
			{if $editor_pick_title <> ""}
				{include file="$smarty_include/templates/slick/featured.tpl"}
			{/if}
			
			{php}		
				if ($show_frontpage_items == "true") {
					$sfi = true;
				} else {
					$sfi = false;
				}
				if ($show_alphabet == "true") {
					$sa = true;
				} else {
					$sa = false;
				}
				$blocks->slickMediaBrowser($node,$sfi,$sa);
			{/php}
		</td>
		{php}
			// Now let's see if we need the featured block or not
			if ($node->getLevel() == 0){
				if ($blocks->showFeaturedBlock($node,true)){
					// Ok, let's show it
					echo '<td width="1%" valign="top">';
					// Now let's show the featured stuff
					$blocks->showFeaturedBlock($node);
					echo '</td>';
				}
			}
		{/php}
	</tr>
	<tr>
		<td width="100%" colspan="2" valign="top">
			{php}
				// Now let's see if we should display random albums
				if ($random_albums <> "0" and !isset($_GET['jz_letter'])){
					$blocks->slickRandomAlbums($node, $node->getName());
				}
				
				// Now let's show the charts
				$blocks->showSlickCharts($node,$chart_types);
				
				// we might still have tracks.
				$tracks = $node->getSubNodes("tracks");
				if (count($tracks) <> 0){
					$blocks->blockHeader(word("Tracks"));
					$blocks->blockBodyOpen();
					$blocks->trackTable($tracks, "album");
					$blocks->blockBodyClose();
					$blocks->blockSpacer();
				}
			
				$fe->footer();
			{/php}
		</td>
	</tr>
</table>