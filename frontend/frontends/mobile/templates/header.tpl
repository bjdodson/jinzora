<div id="header">
{section name=i loop=$breadcrumbs start=-1 step=-1}
<span style="white-space:nowrap;">
<a href="{$breadcrumbs[i].link}"> 
  {$up_arrow} {$breadcrumbs[i].name|truncate:15} 
</a>
</span>
{/section}
</div>
