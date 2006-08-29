<table width="100%" cellpadding="2" cellspacing="0" border="0"><tr><td width="100%" height="5"></td></tr></table>

<div style="margin-left:3px; margin-right:3px; margin-bottom:0px; margin-top:0px;">
	<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td height="23" class="jz_main_block_topl">&nbsp;</td>
			<td width="50%" class="jz_main_block_topm" nowrap>
				<span class="headertextshadow">
					<strong>
						{$title}
						<font color="{$jz_bg_color}" class="headertext">
							{$title}
						</font>
					</strong>
				</span>
			</td>
			<td width="50%" align="right" class="jz_main_block_topm" nowrap>
				{$breadcrumbs}
			</td>
			<td class="jz_main_block_topr">&nbsp;</td>
		</tr>
	</table>

	<table width="100%" cellspacing="0" cellpadding="2">
		<tr>
			<td colspan="4" class="jz_block_td">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jz_left_iblock_inner">
					<tr>	
						{php}
							global $jz_path;
							$display = new jzDisplay();
							$blocks = new jzBlocks();
							$node = new jzMediaNode($jz_path);
						{/php}				
						{if $show_genre == "true"}
							<td width="20%">
								{php}
									$display->popupLink("genre");
								{/php}
								{if $genre_drop == "true"}
									<br />
									<form action="{$this_page}" method="{$mode}">
									{php}
										$display->hiddenPageVars(); 
										$display->dropdown("genre"); 
									{/php}
									</form>
								{/if}
							</td>
						{/if}		
						
						
						{if $show_artist == "true"}
							<td width="20%">
								{php}
									$display->popupLink("artist");
								{/php}
								{if $artist_drop == "true"}
									<br>
									<form action="{$this_page}" method="{$mode}">
									{php}
										$display->hiddenPageVars(); 
										$display->dropdown("artist"); 
									{/php}
									</form>
								{/if}
							</td>
						{/if}
						
						
						{if $show_album == "true"}
							<td width="20%">
								{php}
									$display->popupLink("album");
								{/php}
								{if $album_drop == "true"}
									<br>
									<form action="{$this_page}" method="{$mode}">
									{php}
										$display->hiddenPageVars(); 
										$display->dropdown("album"); 
									{/php}
									</form>
								{/if}
							</td>
						{/if}
						
						
						{if $song_drop == "true"}
							<td width="20%">
								{php}
									$display->popupLink("track");
								{/php}
								<br>
								<form action="{$this_page}" method="{$mode}">
								{php}
									$display->hiddenVariableField('action','playlist');
									$display->hiddenVariableField('type','track');
									$display->dropdown("track"); 
								{/php}
								</form>
							</td>
						{/if}
						
						
						{if $quick_drop == "true"}
							<td width="20%">
								<nobr>
								{php}
									$blocks->randomGenerateSelector($node);
								{/php}
								</nobr>
							</td>
						{/if}
						
						
						{if $show_resample == "true"}
							<td width="20%">
								<nobr>
								{php}
									$display->displayResampleDropdown($node)
								{/php}
								</nobr>
							</td>
						{/if}
					</tr> 
				</table>
			</td>
		</tr>
	</table>
</div>