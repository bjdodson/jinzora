{section name=i loop=$breadcrumbs start=-1 step=-1}
<a href="{$breadcrumbs[i].link}"> 
  {$up_arrow} {$breadcrumbs[i].name|truncate:15} 
</a>
{/section}
