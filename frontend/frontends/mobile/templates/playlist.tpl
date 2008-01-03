{literal}
<style type="text/css">
#playlist div {
  width:100%;
  height:100%;

}

#playlist table {
  height:100%;
  width:100%;
  margin:0;
}

#playlist table tr td {
  padding:0 8 0 8;
}

/* big links for easy clicking */
#playlist table tr td a {
  width:100%;
  height:100%;
  display:block;
  padding:8 0 8 0;
}

#plHeader {
  margin: 5px 0px 0px 10px;
  text-decoration:underline;
}

#plLinks {
  margin: 0px 0px 10px 25px;
}

</style>
{/literal}
<div id="playlist">
  <div id="plHeader">
    {$plName}
  </div>
  <div id="plLinks">
  {$openPlayTag}>{$Play}</a>{if $isStatic} | {$openShuffleTag}>{$Shuffle}</a>{/if}
  </div>
  {section name="element" loop=$elements}
  {if $smarty.section.element.index is even}
  <table class="jz_row1">
  {else}
  <table class="jz_row2">
  {/if}
  <tr>
    <td align="left">
          {$smarty.section.element.index+1}.
          {$elements[element].name|truncate:$main_truncate_length}
    </td><td align="right">
      {$elements[element].openPlayTag}>{$Play}</a>
    </td>
  </tr>
  </table>  
  {/section}
</div>