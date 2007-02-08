<!-- have more available to us.. example:
{$album_art}
-->
{literal}
<style>
* {
font-size:22px;
}

a {
font-size:22px;
}

{/literal}
</style>
{if $error_message}
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jz_block_td" height="100%">
	<tr>
		<td width="100%" valign="top" height="100%">
			{$error_message}
		</td>
	</tr>
</table>

{else}
<table cellpadding="0" cellspacing="0" border="0" class="jz_block_td" height="100%" style="width:100%">
	<tr>
		<td width="100%" valign="top" height="100%">
			<nobr>
			{$play_button}
			{$pause_button}
			{$stop_button}
			{$prev_button}
			{$next_button}
			{$shuffle_button}
			{$clear_button}
			<!--
			{$repeat_button}
			 -->
			<br/>
			{if $jb_status} <!-- && $jukebox_display == "playlist" -->
				Status: {$jb_status}
				<br/>
			{/if}
			</nobr>
			<!--
			{if $jukebox_display == "art"}
				<span>
				{$artist} - 
				{$album}
				</span>
			{/if}
			-->
		</td></tr><tr><td style="font-size:20px;">
{if $jukebox_display == "playlist"}
		{if $playlist_form_action}
		<form action="{$playlist_form_action}" method="{$playlist_form_method}" id="jbPlaylistForm">
			<input type="hidden" name="action" value="jukebox">
			<input type="hidden" name="subaction" value="jukebox-command">
			<input type="hidden" id="jbCommand" name="command" value="jumpto">
			<select multiple name="jbjumpto[]" id="jukeboxJumpToSelect" class="jz_select" size="7" style="width:250px;font-size:20px;" {if $playlist_jump_supported}onclick="setJbFormCommand('jumpto'); sendJukeboxForm(); return false;"{/if}>

				{foreach from=$playlist item=track key=id}
					<option value="{$track.index}" 
						{if $track.selected}selected{/if} 
						{if $track.playing}style="font-weight:bold;" selected>* 
						{else}>
						{/if}
						{$track.label}
					</option>
				{/foreach}
			</select>
			<!--
			{if $playlist_move_supported or $playlist_del_supported}
			<div id="jbPlaylistButtons" style="text-align:right;">
			{if $playlist_move_supported}
			<a href="#" onclick="{$moveup_link}">{$moveup_button}</a>
			<a href="#" onclick="{$movedown_link}">{$movedown_button}</a>
			{/if}
			{if $playlist_del_supported}
			<a href="#" onclick="{$del_link}">{$del_button}</a>
			{/if}
			</div>
			{/if}
			-->
		</form>
		{/if}
{/if}
{if $jukebox_display == "art"}
<a href="{$album_url}" target="main">{$album_art}</a>
{/if}

		</td>
	</tr>
</table>

{/if}