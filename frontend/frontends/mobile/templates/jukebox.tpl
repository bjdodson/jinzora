{literal}
<style type="text/css">
#jbButtons {
  text-align:center;
  margin: 10px 10px 10px 15px;
}

#jbButtons a {
  font-weight:bold;
}
</style>
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
