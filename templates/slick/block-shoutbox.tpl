<div id="slickLeftBlock">
	<div id="slickLeftBlockPad">
		{literal}
		<script src="{/literal}{$root_dir}{literal}/data/yshout/js/prototype.js" type="text/javascript"></script>
		<script src="{/literal}{$root_dir}{literal}/data/yshout/js/yshout.js" type="text/javascript"></script>
		<font size="1">
			<strong>Shoutbox</strong>
			{/literal}
				{if $admin == true}
					- {$purge_link}
				{/if}
			{literal}
		</font>
		<br />	
		<div id="yshout"></div>
		<script type="text/javascript">
			loadYShout({
				yUser: '{/literal}{$username}{literal}',
				yPath: '{/literal}{$root_dir}{literal}/data/yshout/'
			});
		</script>
		{/literal}
	</div>
</div>
<div id="slickLeftBlockSpace"></div>