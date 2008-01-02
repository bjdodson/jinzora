{literal}
<style type="text/css">
#crumbs ul {
  margin-left: 0;
  padding-left: 0;
  display: inline;
  border: none;
} 

#crumbs ul li {
  margin-left: 0;
  padding-left: 2px;
  border: none;
  list-style: none;
  display: inline;
}


#media div {
  width:100%;
  height:100%;

}

#media table {
  height:100%;
  width:100%;
  margin:0;
}

#media table tr td {
  padding:0 8 0 8;
}

/* big links for easy clicking */
#media table tr td a {
  width:100%;
  height:100%;
  display:block;
  padding:8 0 8 0;
}
</style>
{/literal}

  <div id="crumbs">
    <ul>
     {section name=crumb loop=$breadcrumbs start=-1 step=-1}
     <li><span style="white-space:nowrap;">
     {if not $smarty.section.crumb.first}
     &#187; 
     {/if}
       <a href="{$breadcrumbs[crumb].link}"> 
         {$breadcrumbs[crumb].name|truncate:15}
       </a>
     </span></li>
    {/section}
    </ul>
  </div>
  {include file="$templates/letters.tpl"}


<div id="media">

  {section name=node loop=$nodes}
  {if $smarty.section.node.index is even}
  <table class="jz_row1">
  {else}
  <table class="jz_row2">
  {/if}
  {section name=anchor loop=$nodes[node].anchors}
    <a name="{$nodes[node].anchors[anchor]}"/>
  {/section}
  <tr>
    <td align="left">
      <a href="{$nodes[node].link}"> {$nodes[node].name|truncate:35} </a>
    </td><td align="right">
      {$nodes[node].openPlayTag}>{$Play}</a>
    </td>
  </tr>
  </table>
  {/section}



  {section name="track" loop=$tracks}
  {if $smarty.section.track.index+$smarty.section.node.index is even}
  <table class="jz_row1">
  {else}
  <table class="jz_row2">
  {/if}
  <tr>
    <td align="left">
    {$tracks[track].openPlayTag}>
      {$tracks[track].number}. {$tracks[track].name|truncate:35}
      &nbsp;({$tracks[track].length})
    </a>
    </td><td align="right">
      {$tracks[track].openPlayTag}>{$Play}</a>
    </td>
  </tr>
  </table>  
  {/section}

</div>