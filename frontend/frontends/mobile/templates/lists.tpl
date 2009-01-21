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


<div>
  <ul><li><a href="#playlists">{$Playlists}</a></li>
  {section name="header" loop=$charts}<li><a href="#chart_{$smarty.section.header.index}">{$charts[header].title}</a></li>{/section}
</div>
<div id="bodyDiv">
  <a name="playlists"/>
  <h1>{$Playlists}</h1>
  {section name=playlist loop=$playlists}
  {if $smarty.section.playlist.index is even}
  <table class="jz_row1">
  {else}
  <table class="jz_row2">
  {/if}
    <tr>
      <td align="left">
        <a href="{$playlists[playlist].editHREF}">
        {$playlists[playlist].name|truncate:15:"...":true}
        </a>
      </td>
      <td align="right">
        {$playlists[playlist].openPlayTag}>{$Play}</a>
	{if $playlists[playlist].isStatic } 
        | {$playlists[playlist].openShuffleTag}>{$Shuffle}</a>
	{/if}
      </td>
    </tr>
  </table>
  {/section}

  {section name=chart loop=$charts}
  <a name="chart_{$smarty.section.chart.index}"/>	
  <h1>{$charts[chart].title}</h1>
    {section name=entry loop=$charts[chart].entries}
    {if $smarty.section.entry.index is even}
    <table class="jz_row1">
    {else}
    <table class="jz_row2">
    {/if}
      <tr>
        <td align="left">
          <a href="{$charts[chart].entries[entry].link}">
            {$charts[chart].entries[entry].name|truncate:$main_truncate_length:"...":true}
          </a>
        </td>
        <td align="right">
          {$charts[chart].entries[entry].openPlayTag}>{$Play}</a>
  	</td>
      </tr>
    </table>
    {/section}

  {/section}
</div>