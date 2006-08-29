<div id="slickLeftBlock">
	<div id="slickLeftBlockPad">
		<div id="nowstreaming"></div>
		{literal}
		<script>
			function callNowStream() {
				x_returnNowStreaming(callNowStream_cb)
			}					
			function callNowStream_cb(a) { // callback function for callme. ajax_it_up's output is put in 'a'.
				document.getElementById("nowstreaming").innerHTML = a;
			}					
			// Now let's update the block
			nsCountDown();					
			// Now let's updated it every X seconds						
			function nsCountDown(tLen, clock){						
				callNowStream();
				setTimeout("nsCountDown()",{/literal}{$status_blocks_refresh}{literal});
			}
		</script>
		{/literal}
	</div>
</div>
<div id="slickLeftBlockSpace"></div>