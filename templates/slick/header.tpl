<tr>
	<td width="148" height="100%" valign="top">
		<table width="100%" cellpadding="1">
			<tr>
				<td height="1" width="100%"></td>
			</tr>
		</table>
		{php}	
			global $jz_path, $disable_leftbar;
			$blocks = new jzBlocks();
			$node = new jzMediaNode($jz_path);
			
			if ($disable_leftbar != "true") {
				$blocks->slickLeftNavigation($node);			
				$blocks->slickFillerBlock();
			}
		{/php}
	<td width="99%" valign="top">
