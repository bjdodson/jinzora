{literal}
<style type="text/css">
#media div {
  width:100%;
  height:100%;
  padding: 8 8 8 8;
}

#media div table {
height:100%;
width:100%;
margin:0;
}

/* big links for easy clicking */
#media div table tr td a {
  width:100%;
  height:100%;
  display:block;
}
</style>
{/literal}

<div id="media">

  {section name=node loop=$nodes}
  {if $smarty.section.node.index is even}
  <div class="jz_row1">
  {else}
  <div class="jz_row2">
  {/if}
  {section name=anchor loop=$nodes[node].anchors}
    <a name="{$nodes[node].anchors[anchor]}"/>
  {/section}
  <table width="100%"><tr>
    <td align="left">
      <a href="{$nodes[node].link}"> {$nodes[node].name|truncate:35} </a>
    </td><td align="right">
      {$nodes[node].openPlayTag}>Play</a>
    </td>
  </tr></table>
  </div>
  {/section}



  {section name="track" loop=$tracks}
  {if $smarty.section.track.index+$smarty.section.node.index is even}
  <div class="jz_row1">
  {else}
  <div class="jz_row2">
  {/if}
  <table width="100%"><tr>
    <td align="left">
      {$tracks[track].openPlayTag}>{$tracks[track].number} {$tracks[track].name|truncate:35}</a>
      ({$tracks[track].length})
    </td><td align="right">
      {$tracks[track].openPlayTag}>Play</a>
    </td>
  </tr></table>
  </div>  
  {/section}

</div>