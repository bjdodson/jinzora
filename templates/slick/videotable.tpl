<table class="jz_track_table" width="100%" cellpadding="1">
	<TR>
	{section name=track loop=$tracks}
		<TD valign="top" align="center" nowrap>
			{if $tracks[track].art <> false}
				<a href="{$tracks[track].playlink}"><img src="{$tracks[track].art}" border="0"></a><br>
			{/if}
			<a href="{$tracks[track].playlink}">{$tracks[track].name}</a> ({$tracks[track].length})<br> 
			{$word_viewed}: {$tracks[track].playcount} time{if $tracks[track].playcount > 1}s{/if}{if $tracks[track].playcount == 0}s{/if}
			<br>
			<a href="{$tracks[track].playlink}">{$word_watch_now}</a> |
			<a href="{$tracks[track].downloadlink}">{$word_download}</a>
			<br><br><br />
		</TD>
		{if not ($smarty.section.track.rownum mod $cols)}
			{if not $smarty.section.track.last}
				</TR><TR>
			{/if}
		{/if}
		{if $smarty.section.track.last}
			{* pad the cells not yet created *}
			{math equation = "n - a % n" n=$cols a=$tracks|@count assign="cells"}
			{if $cells ne $cols}
			{section name=pad loop=$cells}
				<TD>&nbsp;</TD>
			{/section}
			{/if}
			</TR>
		{/if}
	{/section}
</TABLE>