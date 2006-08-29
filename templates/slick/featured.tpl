{php}
	global $jz_path;
	$blocks = new jzBlocks();
	$node = new jzMediaNode($jz_path);
	
	$featuredArtists = $node->getFeatured(distanceTo("artist",$node));
	$featuredAlbums = $node->getFeatured(distanceTo("album",$node));
	$featCtr=0;
	if (count($featuredAlbums) <> 0){ $featCtr++; }
	if (count($featuredArtists) <> 0){ $featCtr++; }
			
	if ($featCtr == 0){
		return;
	}
{/php}
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td width="50%" valign="top">
			{php}
				$blocks->showFeatured($featuredArtists, 300, true);
			{/php}
		</td>
		<td><table width="100%" cellpadding="2" cellspacing="0" border="0"><tr><td></td></tr></table></td>
		<td width="50%" valign="top">
			{php}
				$blocks->showFeatured($featuredAlbums, 300, true);
			{/php}
		</td>
	</tr>
</table>
<table width="100%" cellpadding="2" cellspacing="0" border="0"><tr><td width="100%" height="5"></td></tr></table>