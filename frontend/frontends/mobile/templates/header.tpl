{literal}
<style type="text/css">
#tabs {
  padding-bottom:8px;
}
#tabs ul {
  width:100%;
  margin-left: 0;
  margin-top: 0;
  padding-left: 0;
  display: inline;
} 

#tabs ul li {
  margin-left: 2px;
  margin-top: 0px;
  padding: 2px 2px 2px 2px;
  list-style: none;
  display: inline;
  border-left: 1px solid;
  border-right: 1px solid;
  border-bottom: 1px solid;
  text-align: center;
}

.selected {
  font-weight:bold;
}
</style>
{/literal}

<div id="header">
  <div id="tabs">
    <ul>
      {section name=tab loop=$tabs}
      <li {if $tabs[tab].selected}class="selected"{/if}>
        <a href="{$tabs[tab].link}">{$tabs[tab].name|truncate:12:"..":true}</a>
      </li>
      {/section}
    </ul>    
  </div>
</div>