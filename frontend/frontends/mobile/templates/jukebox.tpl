{literal}
<style type="text/css">
#jbButtons {
  text-align:center;
  margin: 10px 10px 10px 15px;
}

#jbButtons a {
  font-weight:bold;
}

#jbAddTypes a {
  margin:0px 0px 0px 20px;
  padding:0;

}

</style>
<script type="text/javascript">
// would be better to use jquery, but
// we want to minimize bandwidth for phones
function setAddTypeStyle(cur) {
  var i = 0;
  while (next = document.getElementById('addType'+i)) {
    if ('addType'+i==cur) {
      next.style.fontWeight='bold';
    } else {
      next.style.fontWeight='';
    }
    i++;
  }
}
</script>
{/literal}

<div id="jbButtons">
{if $openPlayTag}
{$openPlayTag}>{$Play}</a>
{/if}
{if $openPauseTag}
{$openPauseTag}>{$Pause}</a>
{/if}
{if $openStopTag}
{$openStopTag}>{$Stop}</a>
{/if}
{if $openPrevTag}
{$openPrevTag}>{$Previous}</a>
{/if}
{if $openNextTag}
{$openNextTag}>{$Next}</a>
{/if}
</div>

{if $whereAdd}
<div id="jbAddTypes">
<h2>{$whereAdd}</h2>
{section name=addType loop=$addTypes}  
  <a id="addType{$smarty.section.addType.index}" href="{$addTypes[addType].href}" onclick="setAddTypeStyle('addType{$smarty.section.addType.index}')" {if $addTypes[addType].selected}style="font-weight:bold;"{/if}>{$addTypes[addType].label}</a><br/>
{/section}
</div>
{/if}