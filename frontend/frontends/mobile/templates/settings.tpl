{literal}
<style type="text/css">

#bodyDiv div {
  margin:0;
  padding: 0;
  vertical-align:middle;
}

/* big links for easy clicking */
#bodyDiv div a {
  height:100%;
  display:block;
  padding: 8px 8px 8px 8px;  
}

{/literal}
#{$newList.inputID}
	{literal} {
  margin: 4px 2px 4px 2px;
  width:80px;
}
</style>
{/literal}

<div id="bodyDiv">
  <h1>{$Playback}</h1>
  <h2>{$SendToDevice}</h2>
  {section name=player loop=$devices}
  {if $smarty.section.player.index is even}
  <div class="jz_row1">
  {else}
  <div class="jz_row2">
  {/if}
  {if $devices[player].selected}<span style="font-weight:bold;">{/if}
    <a href="{$devices[player].url}"> {$devices[player].label|truncate:$chars_per_line:"...":true} </a>
  {if $devices[player].selected}</span>{/if}
  </div>

  {/section}
  
  <h2>{$AddToPlaylist}</h2>
  {section name=playlist loop=$playlists}
  {if $smarty.section.playlist.index is even}
  <div class="jz_row1">
  {else}
  <div class="jz_row2">
  {/if}
  {if $playlists[playlist].selected}<span style="font-weight:bold;">{/if}
    <a href="{$playlists[playlist].url}"> {$playlists[playlist].label|truncate:$chars_per_line:"...":true} </a>
  {if $playlists[playlist].selected}</span> {/if}
  </div>
  {/section}
  {if $smarty.section.playlist.index is even}
  <div class="jz_row1">
  {else}
  <div class="jz_row2">
  {/if}
    <a style="display:inline;padding:0 4 0 8; vertical-align:middle;" href="{$newList.href}" onclick="{$newList.onclick}">{$newList.label}</a>
    <input id="{$newList.inputID}" value="{$newList.name}"/>
  </div>
</div>