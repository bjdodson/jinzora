<div id="slickLeftBlock">
	<div id="slickLeftBlockPad">
		<div id="whoiswhere"></div>
		{literal}
		<script>
			function callWhoisWhere() {
				x_returnWhoisWhere(callWhoisWhere_cb)
			}						
			function callWhoisWhere_cb(a) { // callback function for callme. ajax_it_up's output is put in 'a'.
				document.getElementById("whoiswhere").innerHTML = a;
			}						
			// Now let's update the block
			nsCountDown_wiw();
			// Now let's updated it every X seconds						
			function nsCountDown_wiw(tLen, clock){
				callWhoisWhere();
				setTimeout("nsCountDown_wiw()",{/literal}{$status_blocks_refresh}{literal});
			}
			
		</script>
		{/literal}
	</div>
</div>
<div id="slickLeftBlockSpace"></div>