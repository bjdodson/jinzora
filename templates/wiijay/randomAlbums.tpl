<table width="100%" cellspacing="3" align="center">
  <tr> 
  {foreach from=$albums item=album}
    <td class="jz_block_td" align="center">
      <!--{$album.name}-->
      <a href="{$album.link}">{$album.art}</a><br/>{$album.playlink}
    </td>
  {/foreach}
  </tr>
</table>