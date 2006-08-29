{$title}
<form action="{$action}" method="post">
	<select class="jz_select" id="resamplerate" name="'. jz_encode("jz_resample"). '" style="width:50" onChange="{$onchange}">
		<option value="">-</option>
		{html_options values=$resample_rates output=$resample_rates selected=$cur_rate}
	</select> Kbps
</form>