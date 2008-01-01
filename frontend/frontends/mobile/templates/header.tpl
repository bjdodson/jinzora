{literal}
<style type="text/css">
#crumbs {

}

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

</style>
{/literal}

<div id="header">
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
</div>