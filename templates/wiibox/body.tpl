{literal}
<script language="javascript">
function goSetHeight() {
  if (parent == window) return;
  else parent.setIframeHeight('body');
}
</script>
{/literal}

<body onload="goSetHeight()">
	{foreach from=$nodes item=el}
	{$el.name} : {$el.link} : {$el.playlink}<br/>
	{/foreach}
</body>