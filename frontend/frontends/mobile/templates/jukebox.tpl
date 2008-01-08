{assign var=volFilled value='blue'}
{assign var=volNotFilled value='red'}
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

#jbAddTypes {
  margin-bottom: 15px;
}

#volume {
  cursor:pointer;
  display:block;
  float:none;
}

#volume div a {
  background-color:gray;
  border:0;
  width:10px;
  height:20px;
  margin:0;
  padding:0;
  display:block;
  float:left;
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

{if $volumeSteps}
{literal}
<script type="text/javascript">
  function changeVolume(i,vol) {
    sendJukeboxRequest('volume',vol);

    var j = 1; // must be same as section volumeStep's start
    var el = null, name = null;
    while (null != (el = document.getElementById(name = 'vol'+j))) {
      if (j <= i) {
        el.style.backgroundColor='{/literal}{$volFilled}{literal}';
      } else {
        el.style.backgroundColor='{/literal}{$volNotFilled}{literal}';    
      }
      j++;
    }
    
  }
</script>
{/literal}
<br/>
<div id="volume">
{$Volume}<div id="knob">{section name=volumeStep loop=$volumeSteps start=1}<a id="vol{$smarty.section.volumeStep.index}" onclick="changeVolume({$smarty.section.volumeStep.index},{$volumeSteps[volumeStep]})" style="background-color:{if $volumeSteps[volumeStep] <= $currentVolume}{$volFilled}{else}{$volNotFilled}{/if};"></a>{/section}</div>
{/if}
</div>
{if $whereAdd}
<div id="jbAddTypes">
<br/>
<h2>{$whereAdd}</h2>
{section name=addType loop=$addTypes}  
  <a id="addType{$smarty.section.addType.index}" href="{$addTypes[addType].href}" onclick="setAddTypeStyle('addType{$smarty.section.addType.index}')" {if $addTypes[addType].selected}style="font-weight:bold;"{/if}>{$addTypes[addType].label}</a><br/>
{/section}
</div>
{/if}