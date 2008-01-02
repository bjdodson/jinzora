{literal}
<style type="text/css">

#bodyDiv div {
  margin:0;
  padding: 0;
  vertical-align:middle;
  padding: 8px 8px 8px 8px;
width:auto;
}

#bodyDiv table {
  height:100%;
  width:100%;
  margin:0;
}

#bodyDiv table tr td {
  padding:8 8 8 8;
}

</style>
{/literal}

<div id="bodyDiv">
  {section name=playlist loop=$playlists}
  {if $smarty.section.playlist.index is even}
  <table class="jz_row1">
  {else}
  <table class="jz_row2">
  {/if}
    <tr>
      <td align="left">
        {$playlists[playlist].name|truncate:25}
      </td>
      <td align="right">
        <a href="{$playlists[playlist].play}">{$play}</a> | 
        <a href="{$playlists[playlist].shuffle}">{$shuffle}</a>
      </td>
    </tr>
  </table>
  {/section}
</div>